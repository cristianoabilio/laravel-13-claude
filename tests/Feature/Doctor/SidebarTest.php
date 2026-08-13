<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Renders the sidebar partial in isolation (not the full page) so assertions
 * can't be muddied by unrelated hardcoded strings elsewhere in the layout,
 * like header.blade.php's own separate "Dr Edalin Hendry" placeholder.
 */
function renderSidebarAs(User $doctor): string
{
    Auth::login($doctor);

    return view('doctor.body.sidebar')->render();
}

test('sidebar shows the authenticated doctor\'s own data', function () {
    $doctor = User::factory()->doctor()->create([
        'display_name' => 'Dr Jane Somers',
        'designation' => 'Cardiologist',
        'role' => 'doctor',
    ]);

    $html = renderSidebarAs($doctor);

    expect($html)->toContain('Dr Jane Somers');
    expect($html)->toContain('Cardiologist');
    expect($html)->toContain('Doctor');
    expect($html)->not->toContain('Dr Edalin Hendry');
    expect($html)->not->toContain('Oral & Maxillofacial Surgery');
});

test('sidebar falls back to first and last name when display name is blank', function () {
    $doctor = User::factory()->doctor()->create([
        'display_name' => null,
        'first_name' => 'Alex',
        'last_name' => 'Rivera',
    ]);

    expect(renderSidebarAs($doctor))->toContain('Alex Rivera');
});

test('sidebar hides the designation block when the doctor has not set one', function () {
    $doctor = User::factory()->doctor()->create(['designation' => null]);

    expect(renderSidebarAs($doctor))->not->toContain('class="patient-details"');
});

test('sidebar marks the correct availability option as selected', function () {
    $available = User::factory()->doctor()->create(['availability_status' => 'available']);
    $html = renderSidebarAs($available);
    expect($html)->toContain('<option value="available" selected>');
    expect($html)->not->toContain('<option value="not_available" selected>');

    $notAvailable = User::factory()->doctor()->create(['availability_status' => 'not_available']);
    $html = renderSidebarAs($notAvailable);
    expect($html)->toContain('<option value="not_available" selected>');
    expect($html)->not->toContain('<option value="available" selected>');
});

test('sidebar shows the uploaded profile photo when one exists, and the bundled default otherwise', function () {
    Storage::fake('s3');

    $doctorWithoutPhoto = User::factory()->doctor()->create(['profile_photo' => null]);
    expect(renderSidebarAs($doctorWithoutPhoto))
        ->toContain('backend/assets/img/doctors-dashboard/doctor-profile-img.jpg');

    $path = UploadedFile::fake()->image('avatar.jpg')->store('doctors', 's3');
    $doctorWithPhoto = User::factory()->doctor()->create(['profile_photo' => $path]);
    expect(renderSidebarAs($doctorWithPhoto))->toContain($path);
});

test('sidebar dashboard and logout links point to real routes', function () {
    $doctor = User::factory()->doctor()->create();
    $html = renderSidebarAs($doctor);

    expect($html)->toContain(route('doctor.dashboard'));
    expect($html)->toContain(route('logout'));
});

test('sidebar logout form actually logs the doctor out', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->post(route('logout'))
        ->assertRedirect();

    $this->assertGuest();
});

test('sidebar renders on the dashboard page, which passes no doctor variable of its own', function () {
    $doctor = User::factory()->doctor()->create(['display_name' => 'Dr Jane Somers']);

    $this->actingAs($doctor)
        ->get(route('doctor.dashboard'))
        ->assertOk()
        ->assertSee('Dr Jane Somers');
});
