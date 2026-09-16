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

function completedAppointment(User $doctor, User $patient): Appointment
{
    return Appointment::factory()->create([
        'doctor_id' => $doctor->id,
        'patient_id' => $patient->id,
        'status' => AppointmentStatus::Completed,
    ]);
}

test('guests are redirected away from the doctor messages page', function () {
    $this->get(route('doctor.messages'))->assertRedirect(route('login'));
});

test('patients cannot access the doctor messages page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.messages'))
        ->assertRedirect(route('dashboard'));
});

test('only patients with a completed appointment appear as chat contacts', function () {
    $doctor = User::factory()->doctor()->create();
    $treated = User::factory()->patient()->create();
    $onlyPending = User::factory()->patient()->create();

    completedAppointment($doctor, $treated);
    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $onlyPending->id, 'status' => AppointmentStatus::Pending]);

    $response = $this->actingAs($doctor)->get(route('doctor.messages'));

    $response->assertOk();
    $response->assertViewHas('contacts', function ($contacts) use ($treated, $onlyPending) {
        return $contacts->pluck('id')->contains($treated->id)
            && ! $contacts->pluck('id')->contains($onlyPending->id);
    });
});

test('a doctor cannot open a conversation with a patient they have not treated', function () {
    $doctor = User::factory()->doctor()->create();
    $stranger = User::factory()->patient()->create();

    $response = $this->actingAs($doctor)->get(route('doctor.messages', $stranger));

    $response->assertRedirect(route('doctor.messages'));
    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('conversations', ['doctor_id' => $doctor->id, 'patient_id' => $stranger->id]);
});

test('a doctor cannot open a conversation with another doctor', function () {
    $doctor = User::factory()->doctor()->create();
    $otherDoctor = User::factory()->doctor()->create();

    $response = $this->actingAs($doctor)->get(route('doctor.messages', $otherDoctor));

    $response->assertRedirect(route('doctor.messages'));
});

test('opening a conversation with a treated patient creates it and shows the empty state', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    completedAppointment($doctor, $patient);

    $response = $this->actingAs($doctor)->get(route('doctor.messages', $patient));

    $response->assertOk();
    $response->assertViewHas('activeContact', fn ($contact) => $contact->id === $patient->id);
    $this->assertDatabaseHas('conversations', ['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);
});

test('a doctor can send a text message to a treated patient', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    completedAppointment($doctor, $patient);

    $response = $this->actingAs($doctor)->post(route('doctor.messages.store', $patient), [
        'body' => 'Hello, how are you feeling today?',
    ]);

    $response->assertOk();
    $response->assertJsonPath('message.body', 'Hello, how are you feeling today?');
    $response->assertJsonPath('message.is_mine', true);

    $conversation = Conversation::where('doctor_id', $doctor->id)->where('patient_id', $patient->id)->first();
    $this->assertDatabaseHas('chat_messages', [
        'conversation_id' => $conversation->id,
        'sender_id' => $doctor->id,
        'body' => 'Hello, how are you feeling today?',
    ]);
    expect($conversation->fresh()->last_message_at)->not->toBeNull();
});

test('a doctor can send an image message which is stored on the s3 disk', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    completedAppointment($doctor, $patient);

    $image = UploadedFile::fake()->image('xray.jpg', 800, 600);

    $response = $this->actingAs($doctor)->post(route('doctor.messages.store', $patient), [
        'image' => $image,
    ]);

    $response->assertOk();
    $response->assertJsonPath('message.is_image', true);

    $message = ChatMessage::first();
    expect($message->attachment_path)->toStartWith('chat-attachments/');
    Storage::disk('s3')->assertExists($message->attachment_path);
});

test('sending a message with neither text nor an image fails validation', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    completedAppointment($doctor, $patient);

    $this->actingAs($doctor)->post(route('doctor.messages.store', $patient), [])
        ->assertSessionHasErrors('body');
});

test('a doctor cannot send a message to a patient they have not treated', function () {
    $doctor = User::factory()->doctor()->create();
    $stranger = User::factory()->patient()->create();

    $this->actingAs($doctor)
        ->post(route('doctor.messages.store', $stranger), ['body' => 'Hi'])
        ->assertForbidden();
});

test('polling only returns messages newer than the given id and marks them read', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    completedAppointment($doctor, $patient);
    $conversation = Conversation::create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);

    $old = ChatMessage::factory()->create(['conversation_id' => $conversation->id, 'sender_id' => $patient->id, 'body' => 'Old message']);
    $new = ChatMessage::factory()->create(['conversation_id' => $conversation->id, 'sender_id' => $patient->id, 'body' => 'New message']);

    $response = $this->actingAs($doctor)->get(route('doctor.messages.poll', $patient).'?after='.$old->id);

    $response->assertOk();
    $response->assertJsonCount(1, 'messages');
    $response->assertJsonPath('messages.0.body', 'New message');

    expect($new->fresh()->read_at)->not->toBeNull();
    expect($old->fresh()->read_at)->not->toBeNull();
});

test('a doctor cannot poll a conversation with a patient they have not treated', function () {
    $doctor = User::factory()->doctor()->create();
    $stranger = User::factory()->patient()->create();

    $this->actingAs($doctor)
        ->get(route('doctor.messages.poll', $stranger))
        ->assertForbidden();
});

test('unread message count is reflected in the doctor contact list', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    completedAppointment($doctor, $patient);
    $conversation = Conversation::create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);
    ChatMessage::factory()->count(3)->create(['conversation_id' => $conversation->id, 'sender_id' => $patient->id]);

    $response = $this->actingAs($doctor)->get(route('doctor.messages'));

    $response->assertViewHas('contacts', function ($contacts) use ($patient) {
        return $contacts->firstWhere('id', $patient->id)->unread_count === 3;
    });
});

test('a patient shows online in the chat only shortly after being active', function () {
    $doctor = User::factory()->doctor()->create();
    $activePatient = User::factory()->patient()->create(['last_seen_at' => now()]);
    $stalePatient = User::factory()->patient()->create(['last_seen_at' => now()->subMinutes(10)]);
    completedAppointment($doctor, $activePatient);
    completedAppointment($doctor, $stalePatient);

    $response = $this->actingAs($doctor)->get(route('doctor.messages'));

    $response->assertViewHas('contacts', function ($contacts) use ($activePatient, $stalePatient) {
        return $contacts->firstWhere('id', $activePatient->id)->is_online === true
            && $contacts->firstWhere('id', $stalePatient->id)->is_online === false;
    });
});
