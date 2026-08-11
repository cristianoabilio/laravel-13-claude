<?php

use App\Jobs\ProcessClinicGalleryImage;
use App\Models\Clinic;
use App\Models\ClinicImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the doctor clinics page', function () {
    $this->get(route('doctor.clinics'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor clinics page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.clinics'))
        ->assertRedirect(route('dashboard'));
});

test('doctor can view their own clinics with gallery images', function () {
    $doctor = User::factory()->doctor()->create();
    $clinic = Clinic::factory()->create(['doctor_id' => $doctor->id]);
    $image = ClinicImage::factory()->create(['clinic_id' => $clinic->id]);

    $response = $this->actingAs($doctor)->get(route('doctor.clinics'));

    $response->assertOk();
    $response->assertViewHas('doctor', function ($viewDoctor) use ($clinic, $image) {
        return $viewDoctor->clinics->pluck('id')->contains($clinic->id)
            && $viewDoctor->clinics->firstWhere('id', $clinic->id)->images->pluck('id')->contains($image->id);
    });
});

test('doctor can create a new clinic with a logo processed synchronously', function () {
    $doctor = User::factory()->doctor()->create();
    $logo = UploadedFile::fake()->image('clinic.jpg', 1000, 600);

    $response = $this->actingAs($doctor)->put(route('doctor.clinics.update'), [
        'clinics' => [
            [
                'logo' => $logo,
                'name' => 'Downtown Clinic',
                'location' => 'New York',
                'address' => '123 Main St',
            ],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $clinic = Clinic::firstWhere('name', 'Downtown Clinic');
    expect($clinic)->not->toBeNull();
    expect($clinic->doctor_id)->toBe($doctor->id);
    Storage::disk('s3')->assertExists($clinic->logo);

    [$width, $height] = getimagesize(Storage::disk('s3')->path($clinic->logo));
    expect($width)->toBe(360);
    expect($height)->toBe(360);
});

test('logo and gallery uploads only accept jpg and png', function () {
    $doctor = User::factory()->doctor()->create();
    $gif = UploadedFile::fake()->image('clinic.gif', 500, 500);

    $this->actingAs($doctor)->put(route('doctor.clinics.update'), [
        'clinics' => [
            [
                'logo' => $gif,
                'name' => 'GIF Clinic',
                'location' => 'New York',
                'address' => '123 Main St',
            ],
        ],
    ])->assertSessionHasErrors(['clinics.0.logo']);

    $this->actingAs($doctor)->put(route('doctor.clinics.update'), [
        'clinics' => [
            [
                'name' => 'GIF Gallery Clinic',
                'location' => 'New York',
                'address' => '123 Main St',
                'gallery' => [$gif],
            ],
        ],
    ])->assertSessionHasErrors(['clinics.0.gallery.0']);

    expect(Clinic::count())->toBe(0);
});

test('name, location, and address are required once a clinic row has any data', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.clinics.update'), [
            'clinics' => [
                ['name' => 'Only Name Filled'],
            ],
        ])
        ->assertSessionHasErrors(['clinics.0.location', 'clinics.0.address']);

    expect(Clinic::count())->toBe(0);
});

test('completely blank stub rows are ignored instead of failing validation', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.clinics.update'), [
            'clinics' => [
                ['name' => '', 'location' => '', 'address' => ''],
            ],
        ])
        ->assertSessionHasNoErrors();

    expect(Clinic::count())->toBe(0);
});

test('doctor can update an existing clinic', function () {
    $doctor = User::factory()->doctor()->create();
    $clinic = Clinic::factory()->create(['doctor_id' => $doctor->id, 'name' => 'Old Clinic']);

    $this->actingAs($doctor)->put(route('doctor.clinics.update'), [
        'clinics' => [
            [
                'id' => $clinic->id,
                'name' => 'New Clinic',
                'location' => 'Boston',
                'address' => '456 Elm St',
            ],
        ],
    ])->assertSessionHasNoErrors();

    expect(Clinic::count())->toBe(1);
    expect($clinic->fresh()->name)->toBe('New Clinic');
});

test('a doctor cannot update another doctors clinic', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $clinic = Clinic::factory()->create(['doctor_id' => $other->id]);

    $this->actingAs($doctor)->put(route('doctor.clinics.update'), [
        'clinics' => [
            [
                'id' => $clinic->id,
                'name' => 'Hijacked',
                'location' => 'Nowhere',
                'address' => 'n/a',
            ],
        ],
    ])->assertSessionHasErrors(['clinics.0.id']);

    expect($clinic->fresh()->name)->not->toBe('Hijacked');
});

test('doctor can delete their own clinic including its logo and gallery files', function () {
    $doctor = User::factory()->doctor()->create();
    $logo = UploadedFile::fake()->image('clinic.jpg')->store('clinics', 's3');
    $clinic = Clinic::factory()->create(['doctor_id' => $doctor->id, 'logo' => $logo]);
    $galleryPath = UploadedFile::fake()->image('gallery.jpg')->store('clinics/gallery', 's3');
    $image = ClinicImage::factory()->create(['clinic_id' => $clinic->id, 'image' => $galleryPath]);

    $this->actingAs($doctor)
        ->delete(route('doctor.clinics.destroy', $clinic))
        ->assertRedirect();

    $this->assertModelMissing($clinic);
    $this->assertModelMissing($image);
    Storage::disk('s3')->assertMissing($logo);
    Storage::disk('s3')->assertMissing($galleryPath);
});

test('a doctor cannot delete another doctors clinic', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $clinic = Clinic::factory()->create(['doctor_id' => $other->id]);

    $this->actingAs($doctor)
        ->delete(route('doctor.clinics.destroy', $clinic))
        ->assertForbidden();

    $this->assertModelExists($clinic);
});

test('doctor can remove just the clinic logo', function () {
    $doctor = User::factory()->doctor()->create();
    $logo = UploadedFile::fake()->image('clinic.jpg')->store('clinics', 's3');
    $clinic = Clinic::factory()->create(['doctor_id' => $doctor->id, 'logo' => $logo]);

    $this->actingAs($doctor)
        ->delete(route('doctor.clinics.logo.destroy', $clinic))
        ->assertRedirect();

    expect($clinic->fresh()->logo)->toBeNull();
    Storage::disk('s3')->assertMissing($logo);
    $this->assertModelExists($clinic);
});

test('doctor can remove a single gallery image', function () {
    $doctor = User::factory()->doctor()->create();
    $clinic = Clinic::factory()->create(['doctor_id' => $doctor->id]);
    $path = UploadedFile::fake()->image('gallery.jpg')->store('clinics/gallery', 's3');
    $image = ClinicImage::factory()->create(['clinic_id' => $clinic->id, 'image' => $path]);

    $this->actingAs($doctor)
        ->delete(route('doctor.clinics.images.destroy', $image))
        ->assertRedirect();

    $this->assertModelMissing($image);
    Storage::disk('s3')->assertMissing($path);
    $this->assertModelExists($clinic);
});

test('a doctor cannot remove another doctors gallery image', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $clinic = Clinic::factory()->create(['doctor_id' => $other->id]);
    $image = ClinicImage::factory()->create(['clinic_id' => $clinic->id]);

    $this->actingAs($doctor)
        ->delete(route('doctor.clinics.images.destroy', $image))
        ->assertForbidden();

    $this->assertModelExists($image);
});

test('gallery uploads are staged and queued instead of processed during the request', function () {
    Queue::fake();

    $doctor = User::factory()->doctor()->create();
    $galleryFiles = [
        UploadedFile::fake()->image('one.jpg', 1000, 800),
        UploadedFile::fake()->image('two.jpg', 900, 700),
    ];

    $response = $this->actingAs($doctor)->put(route('doctor.clinics.update'), [
        'clinics' => [
            [
                'name' => 'Gallery Clinic',
                'location' => 'New York',
                'address' => '123 Main St',
                'gallery' => $galleryFiles,
            ],
        ],
    ]);

    $response->assertSessionHasNoErrors();

    $clinic = Clinic::firstWhere('name', 'Gallery Clinic');
    expect($clinic)->not->toBeNull();

    // Nothing is processed synchronously: no ClinicImage rows yet.
    expect(ClinicImage::count())->toBe(0);

    Queue::assertPushed(ProcessClinicGalleryImage::class, 2);
    Queue::assertPushed(function (ProcessClinicGalleryImage $job) use ($clinic) {
        return $job->clinicId === $clinic->id
            && Storage::disk('s3')->exists($job->tempPath);
    });
});
