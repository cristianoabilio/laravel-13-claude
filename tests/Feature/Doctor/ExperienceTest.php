<?php

use App\Enums\EmploymentType;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the doctor experience page', function () {
    $this->get(route('doctor.experience'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor experience page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.experience'))
        ->assertRedirect(route('dashboard'));
});

test('doctor can view their own experiences', function () {
    $doctor = User::factory()->doctor()->create();
    $experience = Experience::factory()->create(['doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->get(route('doctor.experience'));

    $response->assertOk();
    $response->assertViewHas('doctor', fn ($viewDoctor) => $viewDoctor->experiences->pluck('id')->contains($experience->id));
});

test('doctor can create a new experience', function () {
    $doctor = User::factory()->doctor()->create();
    $logo = UploadedFile::fake()->image('hospital.jpg', 1000, 600);

    $response = $this->actingAs($doctor)->put(route('doctor.experiences.update'), [
        'experiences' => [
            [
                'hospital_logo' => $logo,
                'title' => 'Senior Surgeon',
                'hospital' => 'City Hospital',
                'years_of_experience' => '5',
                'location' => 'New York',
                'employment_type' => EmploymentType::FullTime->value,
                'job_description' => 'Led the surgery department.',
                'start_date' => '01/03/2018',
                'end_date' => '28/02/2021',
            ],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $experience = Experience::firstWhere('hospital', 'City Hospital');
    expect($experience)->not->toBeNull();
    expect($experience->doctor_id)->toBe($doctor->id);
    expect($experience->employment_type)->toBe(EmploymentType::FullTime);
    expect($experience->start_date->format('Y-m-d'))->toBe('2018-03-01');
    expect($experience->end_date->format('Y-m-d'))->toBe('2021-02-28');
    expect($experience->currently_working)->toBeFalse();
    Storage::disk('s3')->assertExists($experience->hospital_logo);

    [$width, $height] = getimagesize(Storage::disk('s3')->path($experience->hospital_logo));
    expect($width)->toBe(360);
    expect($height)->toBe(360);
});

test('currently working clears the end date even if one was submitted', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.experiences.update'), [
        'experiences' => [
            [
                'hospital' => 'City Hospital',
                'years_of_experience' => '5',
                'location' => 'New York',
                'job_description' => 'Leading the team.',
                'start_date' => '01/03/2018',
                'end_date' => '28/02/2021',
                'currently_working' => '1',
            ],
        ],
    ])->assertSessionHasNoErrors();

    $experience = Experience::firstWhere('hospital', 'City Hospital');
    expect($experience->currently_working)->toBeTrue();
    expect($experience->end_date)->toBeNull();
});

test('end date is required unless currently working', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.experiences.update'), [
        'experiences' => [
            [
                'hospital' => 'City Hospital',
                'years_of_experience' => '5',
                'location' => 'New York',
                'job_description' => 'Leading the team.',
                'start_date' => '01/03/2018',
            ],
        ],
    ])->assertSessionHasErrors(['experiences.0.end_date']);
});

test('dates must match the d/m/Y mask', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.experiences.update'), [
        'experiences' => [
            [
                'hospital' => 'City Hospital',
                'years_of_experience' => '5',
                'location' => 'New York',
                'job_description' => 'Leading the team.',
                'start_date' => '2018-03-01',
                'end_date' => '28/02/2021',
            ],
        ],
    ])->assertSessionHasErrors(['experiences.0.start_date']);
});

test('years of experience must be a 1-2 digit number', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.experiences.update'), [
        'experiences' => [
            [
                'hospital' => 'City Hospital',
                'years_of_experience' => '5 years',
                'location' => 'New York',
                'job_description' => 'Leading the team.',
                'start_date' => '01/03/2018',
                'end_date' => '28/02/2021',
            ],
        ],
    ])->assertSessionHasErrors(['experiences.0.years_of_experience']);

    $this->actingAs($doctor)->put(route('doctor.experiences.update'), [
        'experiences' => [
            [
                'hospital' => 'City Hospital',
                'years_of_experience' => '100',
                'location' => 'New York',
                'job_description' => 'Leading the team.',
                'start_date' => '01/03/2018',
                'end_date' => '28/02/2021',
            ],
        ],
    ])->assertSessionHasErrors(['experiences.0.years_of_experience']);
});

test('completely blank stub rows are ignored instead of failing validation', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.experiences.update'), [
            'experiences' => [
                ['hospital' => '', 'years_of_experience' => '', 'location' => '', 'job_description' => '', 'start_date' => '', 'end_date' => ''],
            ],
        ])
        ->assertSessionHasNoErrors();

    expect(Experience::count())->toBe(0);
});

test('doctor can update an existing experience', function () {
    $doctor = User::factory()->doctor()->create();
    $experience = Experience::factory()->create(['doctor_id' => $doctor->id, 'hospital' => 'Old Hospital']);

    $this->actingAs($doctor)->put(route('doctor.experiences.update'), [
        'experiences' => [
            [
                'id' => $experience->id,
                'hospital' => 'New Hospital',
                'years_of_experience' => '8',
                'location' => 'Boston',
                'job_description' => 'Updated role.',
                'start_date' => $experience->start_date->format('d/m/Y'),
                'end_date' => now()->format('d/m/Y'),
            ],
        ],
    ])->assertSessionHasNoErrors();

    expect(Experience::count())->toBe(1);
    expect($experience->fresh()->hospital)->toBe('New Hospital');
});

test('a doctor cannot update another doctors experience', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $experience = Experience::factory()->create(['doctor_id' => $other->id]);

    $this->actingAs($doctor)->put(route('doctor.experiences.update'), [
        'experiences' => [
            [
                'id' => $experience->id,
                'hospital' => 'Hijacked',
                'years_of_experience' => '1',
                'location' => 'Nowhere',
                'job_description' => 'n/a',
                'start_date' => '01/01/2020',
                'end_date' => '01/01/2021',
            ],
        ],
    ])->assertSessionHasErrors(['experiences.0.id']);

    expect($experience->fresh()->hospital)->not->toBe('Hijacked');
});

test('doctor can delete their own experience', function () {
    $doctor = User::factory()->doctor()->create();
    $logo = UploadedFile::fake()->image('hospital.jpg')->store('experiences', 's3');
    $experience = Experience::factory()->create(['doctor_id' => $doctor->id, 'hospital_logo' => $logo]);

    $this->actingAs($doctor)
        ->delete(route('doctor.experiences.destroy', $experience))
        ->assertRedirect();

    $this->assertModelMissing($experience);
    Storage::disk('s3')->assertMissing($logo);
});

test('a doctor cannot delete another doctors experience', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $experience = Experience::factory()->create(['doctor_id' => $other->id]);

    $this->actingAs($doctor)
        ->delete(route('doctor.experiences.destroy', $experience))
        ->assertForbidden();

    $this->assertModelExists($experience);
});

test('doctor can remove just the hospital logo', function () {
    $doctor = User::factory()->doctor()->create();
    $logo = UploadedFile::fake()->image('hospital.jpg')->store('experiences', 's3');
    $experience = Experience::factory()->create(['doctor_id' => $doctor->id, 'hospital_logo' => $logo]);

    $this->actingAs($doctor)
        ->delete(route('doctor.experiences.logo.destroy', $experience))
        ->assertRedirect();

    expect($experience->fresh()->hospital_logo)->toBeNull();
    Storage::disk('s3')->assertMissing($logo);
    $this->assertModelExists($experience);
});
