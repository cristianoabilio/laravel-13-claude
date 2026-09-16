<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

function completedAppointmentForPatient(User $doctor, User $patient): Appointment
{
    return Appointment::factory()->create([
        'doctor_id' => $doctor->id,
        'patient_id' => $patient->id,
        'status' => AppointmentStatus::Completed,
    ]);
}

test('guests are redirected away from the patient messages page', function () {
    $this->get(route('patient.messages'))->assertRedirect(route('login'));
});

test('doctors cannot access the patient messages page', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->get(route('patient.messages'))
        ->assertRedirect(route('doctor.dashboard'));
});

test('only doctors with a completed appointment appear as chat contacts', function () {
    $patient = User::factory()->patient()->create();
    $treatedBy = User::factory()->doctor()->create();
    $onlyPending = User::factory()->doctor()->create();

    completedAppointmentForPatient($treatedBy, $patient);
    Appointment::factory()->create(['doctor_id' => $onlyPending->id, 'patient_id' => $patient->id, 'status' => AppointmentStatus::Pending]);

    $response = $this->actingAs($patient)->get(route('patient.messages'));

    $response->assertOk();
    $response->assertViewHas('contacts', function ($contacts) use ($treatedBy, $onlyPending) {
        return $contacts->pluck('id')->contains($treatedBy->id)
            && ! $contacts->pluck('id')->contains($onlyPending->id);
    });
});

test('a patient cannot open a conversation with a doctor who has not treated them', function () {
    $patient = User::factory()->patient()->create();
    $stranger = User::factory()->doctor()->create();

    $response = $this->actingAs($patient)->get(route('patient.messages', $stranger));

    $response->assertRedirect(route('patient.messages'));
    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('conversations', ['doctor_id' => $stranger->id, 'patient_id' => $patient->id]);
});

test('a patient cannot open a conversation with another patient', function () {
    $patient = User::factory()->patient()->create();
    $otherPatient = User::factory()->patient()->create();

    $response = $this->actingAs($patient)->get(route('patient.messages', $otherPatient));

    $response->assertRedirect(route('patient.messages'));
});

test('opening a conversation with a treating doctor creates it and shows the empty state', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();
    completedAppointmentForPatient($doctor, $patient);

    $response = $this->actingAs($patient)->get(route('patient.messages', $doctor));

    $response->assertOk();
    $response->assertViewHas('activeContact', fn ($contact) => $contact->id === $doctor->id);
    $this->assertDatabaseHas('conversations', ['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);
});

test('a patient can send a text message to a treating doctor', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();
    completedAppointmentForPatient($doctor, $patient);

    $response = $this->actingAs($patient)->post(route('patient.messages.store', $doctor), [
        'body' => 'Thank you for the consultation.',
    ]);

    $response->assertOk();
    $response->assertJsonPath('message.body', 'Thank you for the consultation.');
    $response->assertJsonPath('message.is_mine', true);

    $conversation = Conversation::where('doctor_id', $doctor->id)->where('patient_id', $patient->id)->first();
    $this->assertDatabaseHas('chat_messages', [
        'conversation_id' => $conversation->id,
        'sender_id' => $patient->id,
        'body' => 'Thank you for the consultation.',
    ]);
});

test('a patient can send an image message which is stored on the s3 disk', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();
    completedAppointmentForPatient($doctor, $patient);

    $image = UploadedFile::fake()->image('report.jpg', 800, 600);

    $response = $this->actingAs($patient)->post(route('patient.messages.store', $doctor), [
        'image' => $image,
    ]);

    $response->assertOk();
    $response->assertJsonPath('message.is_image', true);

    $message = ChatMessage::first();
    expect($message->attachment_path)->toStartWith('chat-attachments/');
    Storage::disk('s3')->assertExists($message->attachment_path);
});

test('sending a message with neither text nor an image fails validation', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();
    completedAppointmentForPatient($doctor, $patient);

    $this->actingAs($patient)->post(route('patient.messages.store', $doctor), [])
        ->assertSessionHasErrors('body');
});

test('a patient cannot send a message to a doctor who has not treated them', function () {
    $patient = User::factory()->patient()->create();
    $stranger = User::factory()->doctor()->create();

    $this->actingAs($patient)
        ->post(route('patient.messages.store', $stranger), ['body' => 'Hi'])
        ->assertForbidden();
});

test('polling only returns messages newer than the given id and marks them read', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();
    completedAppointmentForPatient($doctor, $patient);
    $conversation = Conversation::create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);

    $old = ChatMessage::factory()->create(['conversation_id' => $conversation->id, 'sender_id' => $doctor->id, 'body' => 'Old message']);
    $new = ChatMessage::factory()->create(['conversation_id' => $conversation->id, 'sender_id' => $doctor->id, 'body' => 'New message']);

    $response = $this->actingAs($patient)->get(route('patient.messages.poll', $doctor).'?after='.$old->id);

    $response->assertOk();
    $response->assertJsonCount(1, 'messages');
    $response->assertJsonPath('messages.0.body', 'New message');

    expect($new->fresh()->read_at)->not->toBeNull();
});

test('a patient cannot poll a conversation with a doctor who has not treated them', function () {
    $patient = User::factory()->patient()->create();
    $stranger = User::factory()->doctor()->create();

    $this->actingAs($patient)
        ->get(route('patient.messages.poll', $stranger))
        ->assertForbidden();
});

test('a doctor shows online in the chat only when their availability is set to available', function () {
    $patient = User::factory()->patient()->create();
    $availableDoctor = User::factory()->doctor()->create(['availability_status' => 'available']);
    $unavailableDoctor = User::factory()->doctor()->create(['availability_status' => 'not_available']);
    completedAppointmentForPatient($availableDoctor, $patient);
    completedAppointmentForPatient($unavailableDoctor, $patient);

    $response = $this->actingAs($patient)->get(route('patient.messages'));

    $response->assertViewHas('contacts', function ($contacts) use ($availableDoctor, $unavailableDoctor) {
        return $contacts->firstWhere('id', $availableDoctor->id)->is_online === true
            && $contacts->firstWhere('id', $unavailableDoctor->id)->is_online === false;
    });
});

test('the doctor online indicator on an open conversation reflects their availability status', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create(['availability_status' => 'available']);
    completedAppointmentForPatient($doctor, $patient);

    $response = $this->actingAs($patient)->get(route('patient.messages', $doctor));

    $response->assertOk();
    $response->assertViewHas('activeContact', fn ($contact) => $contact->is_online === true);
});
