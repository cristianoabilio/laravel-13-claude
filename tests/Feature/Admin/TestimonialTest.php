<?php

use App\Models\Admin;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the manage testimonials page', function () {
    $this->get(route('admin.testimonials.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the manage testimonials page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.testimonials.index'))
        ->assertRedirect(route('admin.login'));
});

test('admin sees the seeded default testimonials in order', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.testimonials.index'));

    $response->assertOk();
    $response->assertSee('Deny Hendrawan');
    $response->assertSee('Johnson DWayne');
    $response->assertSee('Rayan Smith');
    $response->assertSee('Sofia Doe');
    $response->assertViewHas('testimonials', fn ($testimonials) => $testimonials->count() === 4);
});

test('admin can add a new testimonial', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->post(route('admin.testimonials.store'), [
        'title' => 'Great Care',
        'quote' => 'The whole team was attentive and made me feel at ease throughout my visit.',
        'patient_name' => 'Alex Turner',
        'patient_country' => 'Canada',
        'rating' => 4,
        'sort_order' => 5,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('testimonials', [
        'title' => 'Great Care',
        'patient_name' => 'Alex Turner',
        'patient_country' => 'Canada',
        'rating' => 4,
        'sort_order' => 5,
    ]);
});

test('a new testimonial without an explicit order is appended after the current highest order', function () {
    $admin = Admin::factory()->create();
    Testimonial::query()->delete();
    Testimonial::factory()->create(['sort_order' => 3]);

    $this->actingAs($admin, 'admin')->post(route('admin.testimonials.store'), [
        'title' => 'New Testimonial',
        'quote' => 'A brand new testimonial.',
        'patient_name' => 'New Patient',
        'patient_country' => 'Australia',
    ]);

    expect(Testimonial::firstWhere('title', 'New Testimonial')->sort_order)->toBe(4);
});

test('a new testimonial without an explicit rating defaults to 5', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.testimonials.store'), [
        'title' => 'Default Rating Testimonial',
        'quote' => 'No explicit rating supplied.',
        'patient_name' => 'Some Patient',
        'patient_country' => 'Brazil',
    ]);

    expect(Testimonial::firstWhere('title', 'Default Rating Testimonial')->rating)->toBe(5);
});

test('title, quote, patient name and patient country are required to add a testimonial', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.testimonials.store'), [])
        ->assertSessionHasErrors(['title', 'quote', 'patient_name', 'patient_country']);
});

test('rating must be between 1 and 5', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.testimonials.store'), [
        'title' => 'Bad Rating',
        'quote' => 'Rating out of range.',
        'patient_name' => 'Some Patient',
        'patient_country' => 'Brazil',
        'rating' => 9,
    ])->assertSessionHasErrors('rating');
});

test('admin can upload a testimonial image which gets resized to 300x300', function () {
    $admin = Admin::factory()->create();
    $image = UploadedFile::fake()->image('patient.jpg', 1200, 1200);

    $response = $this->actingAs($admin, 'admin')->post(route('admin.testimonials.store'), [
        'title' => 'With Image',
        'quote' => 'A testimonial with an uploaded photo.',
        'patient_name' => 'Photo Patient',
        'patient_country' => 'United Kingdom',
        'image' => $image,
    ]);

    $response->assertRedirect();
    $testimonial = Testimonial::firstWhere('title', 'With Image');
    expect($testimonial->image)->toStartWith('testimonials/');
    Storage::disk('s3')->assertExists($testimonial->image);

    $contents = Storage::disk('s3')->get($testimonial->image);
    $dimensions = getimagesizefromstring($contents);
    expect($dimensions[0])->toBe(300);
    expect($dimensions[1])->toBe(300);
});

test('admin can update a testimonial', function () {
    $admin = Admin::factory()->create();
    $testimonial = Testimonial::factory()->create(['title' => 'Old Title']);

    $response = $this->actingAs($admin, 'admin')->put(route('admin.testimonials.update', $testimonial), [
        'title' => 'New Title',
        'quote' => $testimonial->quote,
        'patient_name' => $testimonial->patient_name,
        'patient_country' => $testimonial->patient_country,
        'sort_order' => 9,
    ]);

    $response->assertRedirect();
    expect($testimonial->fresh()->title)->toBe('New Title');
    expect($testimonial->fresh()->sort_order)->toBe(9);
});

test('uploading a new image on update deletes the previous one', function () {
    $admin = Admin::factory()->create();
    $oldImage = UploadedFile::fake()->image('old.jpg', 400, 400)->store('testimonials', 's3');
    $testimonial = Testimonial::factory()->create(['image' => $oldImage]);

    $newImage = UploadedFile::fake()->image('new.jpg', 400, 400);

    $this->actingAs($admin, 'admin')->put(route('admin.testimonials.update', $testimonial), [
        'title' => $testimonial->title,
        'quote' => $testimonial->quote,
        'patient_name' => $testimonial->patient_name,
        'patient_country' => $testimonial->patient_country,
        'image' => $newImage,
    ]);

    Storage::disk('s3')->assertMissing($oldImage);
    expect($testimonial->fresh()->image)->not->toBe($oldImage);
});

test('admin can delete a testimonial', function () {
    $admin = Admin::factory()->create();
    $testimonial = Testimonial::factory()->create();

    $response = $this->actingAs($admin, 'admin')->delete(route('admin.testimonials.destroy', $testimonial));

    $response->assertRedirect();
    $this->assertModelMissing($testimonial);
});

test('deleting a testimonial removes its uploaded image from storage', function () {
    $admin = Admin::factory()->create();
    $image = UploadedFile::fake()->image('patient.jpg', 400, 400)->store('testimonials', 's3');
    $testimonial = Testimonial::factory()->create(['image' => $image]);

    $this->actingAs($admin, 'admin')->delete(route('admin.testimonials.destroy', $testimonial));

    Storage::disk('s3')->assertMissing($image);
});
