<?php

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the medical records page', function () {
    $this->get(route('patient.medical_appointments'))
        ->assertRedirect(route('login'));
});

test('doctors cannot access the patient medical records page', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->get(route('patient.medical_appointments'))
        ->assertRedirect(route('doctor.dashboard'));
});

test('patient sees only their own medical records', function () {
    $patient = User::factory()->patient()->create();
    $other = User::factory()->patient()->create();

    $own = MedicalRecord::factory()->create(['patient_id' => $patient->id]);
    MedicalRecord::factory()->create(['patient_id' => $other->id]);

    $response = $this->actingAs($patient)->get(route('patient.medical_appointments'));

    $response->assertOk();
    $response->assertViewHas('medicalRecords', function ($records) use ($own) {
        return $records->count() === 1 && $records->first()->is($own);
    });
});

test('patient can add a medical record with a file', function () {
    $patient = User::factory()->patient()->create();
    $file = UploadedFile::fake()->create('lab-result.pdf', 100, 'application/pdf');

    $response = $this->actingAs($patient)->post(route('patient.medical_records.store'), [
        'title' => 'Glucose Test',
        'record_for' => 'Self',
        'record_date' => '15/03/2026',
        'comments' => 'Take good rest',
        'file' => $file,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('medical_records', [
        'patient_id' => $patient->id,
        'title' => 'Glucose Test',
        'record_for' => 'Self',
        'comments' => 'Take good rest',
    ]);

    $record = MedicalRecord::first();
    Storage::disk('s3')->assertExists($record->file_path);
    expect($record->record_date->format('Y-m-d'))->toBe('2026-03-15');
});

test('adding a medical record requires all fields', function () {
    $patient = User::factory()->patient()->create();

    $response = $this->actingAs($patient)->post(route('patient.medical_records.store'), []);

    $response->assertSessionHasErrors(['title', 'record_for', 'record_date', 'comments', 'file']);
    $this->assertDatabaseCount('medical_records', 0);
});

test('patient can update their own medical record', function () {
    $patient = User::factory()->patient()->create();
    $record = MedicalRecord::factory()->create(['patient_id' => $patient->id, 'title' => 'Old Title']);
    $newFile = UploadedFile::fake()->create('new-result.pdf', 50, 'application/pdf');

    $response = $this->actingAs($patient)->put(route('patient.medical_records.update', $record), [
        'title' => 'Updated Title',
        'record_for' => 'Self',
        'record_date' => '20/03/2026',
        'comments' => 'Updated comments',
        'file' => $newFile,
    ]);

    $response->assertRedirect();
    expect($record->fresh()->title)->toBe('Updated Title');
    expect($record->fresh()->comments)->toBe('Updated comments');
    Storage::disk('s3')->assertExists($record->fresh()->file_path);
});

test('patient can update a medical record without replacing the file', function () {
    $patient = User::factory()->patient()->create();
    $record = MedicalRecord::factory()->create(['patient_id' => $patient->id, 'file_path' => 'medical-records/original.pdf']);

    $response = $this->actingAs($patient)->put(route('patient.medical_records.update', $record), [
        'title' => 'Updated Title',
        'record_for' => $record->record_for,
        'record_date' => $record->record_date->format('d/m/Y'),
        'comments' => $record->comments,
    ]);

    $response->assertRedirect();
    expect($record->fresh()->file_path)->toBe('medical-records/original.pdf');
});

test('a patient cannot update another patients medical record', function () {
    $patient = User::factory()->patient()->create();
    $other = User::factory()->patient()->create();
    $record = MedicalRecord::factory()->create(['patient_id' => $other->id]);

    $this->actingAs($patient)->put(route('patient.medical_records.update', $record), [
        'title' => 'Hacked',
        'record_for' => 'Self',
        'record_date' => '20/03/2026',
        'comments' => 'x',
    ])->assertForbidden();

    expect($record->fresh()->title)->not->toBe('Hacked');
});

test('patient can delete their own medical record', function () {
    $patient = User::factory()->patient()->create();
    $record = MedicalRecord::factory()->create(['patient_id' => $patient->id, 'file_path' => 'medical-records/to-delete.pdf']);
    Storage::disk('s3')->put('medical-records/to-delete.pdf', 'content');

    $response = $this->actingAs($patient)->delete(route('patient.medical_records.destroy', $record));

    $response->assertRedirect();
    $this->assertDatabaseMissing('medical_records', ['id' => $record->id]);
    Storage::disk('s3')->assertMissing('medical-records/to-delete.pdf');
});

test('a patient cannot delete another patients medical record', function () {
    $patient = User::factory()->patient()->create();
    $other = User::factory()->patient()->create();
    $record = MedicalRecord::factory()->create(['patient_id' => $other->id]);

    $this->actingAs($patient)->delete(route('patient.medical_records.destroy', $record))
        ->assertForbidden();

    $this->assertDatabaseHas('medical_records', ['id' => $record->id]);
});
