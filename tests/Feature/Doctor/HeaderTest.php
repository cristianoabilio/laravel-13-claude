<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Renders the header partial in isolation (not the full page), matching the
 * approach in SidebarTest - keeps assertions from being muddied by unrelated
 * static content elsewhere in the layout (the megamenu, notification/cart
 * dropdowns, etc., none of which are backed by real data).
 */
function renderHeaderAs(User $doctor): string
{
    Auth::login($doctor);

    return view('doctor.body.header')->render();
}

test('header user menu shows the authenticated doctor\'s own data', function () {
    $doctor = User::factory()->doctor()->create([
        'display_name' => 'Dr Jane Somers',
        'role' => 'doctor',
    ]);

    $html = renderHeaderAs($doctor);

    expect($html)->toContain('Dr Jane Somers');
    expect($html)->toContain('Doctor');
    expect($html)->not->toContain('Dr Edalin Hendry');
});

test('header falls back to first and last name when display name is blank', function () {
    $doctor = User::factory()->doctor()->create([
        'display_name' => null,
        'first_name' => 'Alex',
        'last_name' => 'Rivera',
    ]);

    expect(renderHeaderAs($doctor))->toContain('Alex Rivera');
});

test('header shows the uploaded profile photo when one exists, and the bundled default otherwise', function () {
    Storage::fake('s3');

    $doctorWithoutPhoto = User::factory()->doctor()->create(['profile_photo' => null]);
    expect(renderHeaderAs($doctorWithoutPhoto))
        ->toContain('backend/assets/img/doctors-dashboard/doctor-profile-img.jpg');

    $path = UploadedFile::fake()->image('avatar.jpg')->store('doctors', 's3');
    $doctorWithPhoto = User::factory()->doctor()->create(['profile_photo' => $path]);
    expect(renderHeaderAs($doctorWithPhoto))->toContain($path);
});

test('header user menu profile settings link points to the real route', function () {
    $doctor = User::factory()->doctor()->create();
    $html = renderHeaderAs($doctor);

    // Scoped to the user-menu dropdown specifically: the unrelated static
    // megamenu (Doctors > Profile Settings) still legitimately links to
    // doctor-profile-settings.html and is out of scope for this fix.
    $userMenu = substr($html, strpos($html, 'logged-item'));

    expect($userMenu)->toContain(route('doctor.profile'));
    expect($userMenu)->toContain(route('doctor.dashboard'));
    expect($userMenu)->not->toContain('doctor-profile-settings.html');
});

test('header renders on the dashboard page, which passes no doctor variable of its own', function () {
    $doctor = User::factory()->doctor()->create(['display_name' => 'Dr Jane Somers']);

    $this->actingAs($doctor)
        ->get(route('doctor.dashboard'))
        ->assertOk()
        ->assertSee('Dr Jane Somers');
});
