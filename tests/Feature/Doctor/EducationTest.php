<?php

use App\Models\Education;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the doctor education page', function () {
    $this->get(route('doctor.education'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor education page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.education'))
        ->assertRedirect(route('dashboard'));
});

test('doctor can view their own educations', function () {
    $doctor = User::factory()->doctor()->create();
    $education = Education::factory()->create(['doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->get(route('doctor.education'));

    $response->assertOk();
    $response->assertViewHas('doctor', fn ($viewDoctor) => $viewDoctor->educations->pluck('id')->contains($education->id));
});

test('doctor can create a new education', function () {
    $doctor = User::factory()->doctor()->create();
    $logo = UploadedFile::fake()->image('university.jpg', 1000, 600);

    $response = $this->actingAs($doctor)->put(route('doctor.educations.update'), [
        'educations' => [
            [
                'logo' => $logo,
                'institution' => 'Harvard Medical School',
                'course' => 'MD',
                'start_date' => '01/08/2005',
                'end_date' => '01/06/2011',
                'no_of_years' => '6',
                'description' => 'Graduated with honors.',
            ],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $education = Education::firstWhere('institution', 'Harvard Medical School');
    expect($education)->not->toBeNull();
    expect($education->doctor_id)->toBe($doctor->id);
    expect($education->course)->toBe('MD');
    expect($education->start_date->format('Y-m-d'))->toBe('2005-08-01');
    expect($education->end_date->format('Y-m-d'))->toBe('2011-06-01');
    Storage::disk('s3')->assertExists($education->logo);

    [$width, $height] = getimagesize(Storage::disk('s3')->path($education->logo));
    expect($width)->toBe(360);
    expect($height)->toBe(360);
});

test('institution and course are optional', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.educations.update'), [
        'educations' => [
            [
                'start_date' => '01/08/2005',
                'end_date' => '01/06/2011',
                'no_of_years' => '6',
                'description' => 'Graduated with honors.',
            ],
        ],
    ])->assertSessionHasNoErrors();

    expect(Education::count())->toBe(1);
});

test('end date must be on or after the start date', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.educations.update'), [
        'educations' => [
            [
                'institution' => 'Harvard Medical School',
                'start_date' => '01/08/2011',
                'end_date' => '01/06/2005',
                'no_of_years' => '6',
                'description' => 'Graduated with honors.',
            ],
        ],
    ])->assertSessionHasErrors(['educations.0.end_date']);
});

test('dates must match the d/m/Y mask', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.educations.update'), [
        'educations' => [
            [
                'institution' => 'Harvard Medical School',
                'start_date' => '2005-08-01',
                'end_date' => '01/06/2011',
                'no_of_years' => '6',
                'description' => 'Graduated with honors.',
            ],
        ],
    ])->assertSessionHasErrors(['educations.0.start_date']);
});

test('no of years must be a 1-2 digit number', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.educations.update'), [
        'educations' => [
            [
                'institution' => 'Harvard Medical School',
                'start_date' => '01/08/2005',
                'end_date' => '01/06/2011',
                'no_of_years' => '6 years',
                'description' => 'Graduated with honors.',
            ],
        ],
    ])->assertSessionHasErrors(['educations.0.no_of_years']);

    $this->actingAs($doctor)->put(route('doctor.educations.update'), [
        'educations' => [
            [
                'institution' => 'Harvard Medical School',
                'start_date' => '01/08/2005',
                'end_date' => '01/06/2011',
                'no_of_years' => '100',
                'description' => 'Graduated with honors.',
            ],
        ],
    ])->assertSessionHasErrors(['educations.0.no_of_years']);
});

test('completely blank stub rows are ignored instead of failing validation', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.educations.update'), [
            'educations' => [
                ['institution' => '', 'course' => '', 'no_of_years' => '', 'description' => '', 'start_date' => '', 'end_date' => ''],
            ],
        ])
        ->assertSessionHasNoErrors();

    expect(Education::count())->toBe(0);
});

test('doctor can update an existing education', function () {
    $doctor = User::factory()->doctor()->create();
    $education = Education::factory()->create(['doctor_id' => $doctor->id, 'institution' => 'Old University']);

    $this->actingAs($doctor)->put(route('doctor.educations.update'), [
        'educations' => [
            [
                'id' => $education->id,
                'institution' => 'New University',
                'course' => 'MS',
                'start_date' => $education->start_date->format('d/m/Y'),
                'end_date' => now()->format('d/m/Y'),
                'no_of_years' => '4',
                'description' => 'Updated description.',
            ],
        ],
    ])->assertSessionHasNoErrors();

    expect(Education::count())->toBe(1);
    expect($education->fresh()->institution)->toBe('New University');
});

test('a doctor cannot update another doctors education', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $education = Education::factory()->create(['doctor_id' => $other->id]);

    $this->actingAs($doctor)->put(route('doctor.educations.update'), [
        'educations' => [
            [
                'id' => $education->id,
                'institution' => 'Hijacked',
                'start_date' => '01/01/2010',
                'end_date' => '01/01/2014',
                'no_of_years' => '4',
                'description' => 'n/a',
            ],
        ],
    ])->assertSessionHasErrors(['educations.0.id']);

    expect($education->fresh()->institution)->not->toBe('Hijacked');
});

test('doctor can delete their own education', function () {
    $doctor = User::factory()->doctor()->create();
    $logo = UploadedFile::fake()->image('university.jpg')->store('educations', 's3');
    $education = Education::factory()->create(['doctor_id' => $doctor->id, 'logo' => $logo]);

    $this->actingAs($doctor)
        ->delete(route('doctor.educations.destroy', $education))
        ->assertRedirect();

    $this->assertModelMissing($education);
    Storage::disk('s3')->assertMissing($logo);
});

test('a doctor cannot delete another doctors education', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $education = Education::factory()->create(['doctor_id' => $other->id]);

    $this->actingAs($doctor)
        ->delete(route('doctor.educations.destroy', $education))
        ->assertForbidden();

    $this->assertModelExists($education);
});

test('doctor can remove just the logo', function () {
    $doctor = User::factory()->doctor()->create();
    $logo = UploadedFile::fake()->image('university.jpg')->store('educations', 's3');
    $education = Education::factory()->create(['doctor_id' => $doctor->id, 'logo' => $logo]);

    $this->actingAs($doctor)
        ->delete(route('doctor.educations.logo.destroy', $education))
        ->assertRedirect();

    expect($education->fresh()->logo)->toBeNull();
    Storage::disk('s3')->assertMissing($logo);
    $this->assertModelExists($education);
});
