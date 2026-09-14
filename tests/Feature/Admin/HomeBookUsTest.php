<?php

use App\Models\Admin;
use App\Models\HomeBookUsFaq;
use App\Models\HomeBookUsSection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests are redirected away from the manage book us page', function () {
    $this->get(route('admin.home.bookus.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the manage book us page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.home.bookus.index'))
        ->assertRedirect(route('admin.login'));
});

test('admin sees the seeded default section and faqs', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.home.bookus.index'));

    $response->assertOk();
    $response->assertSee('We are committed to understanding your');
    $response->assertSee('Our Vision');
    $response->assertSee('Our Mission');
    $response->assertViewHas('homeBookUsFaqs', fn ($faqs) => $faqs->count() === 2);
});

test('admin can update the section text fields', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->put(route('admin.home.bookus.section.update'), [
        'badge_text' => 'New Badge',
        'heading_prefix' => 'New heading prefix',
        'heading_highlight' => 'new highlight',
        'description' => 'New description text.',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('home_book_us_sections', [
        'badge_text' => 'New Badge',
        'heading_prefix' => 'New heading prefix',
        'heading_highlight' => 'new highlight',
        'description' => 'New description text.',
    ]);
});

test('admin can upload the three section images', function () {
    $admin = Admin::factory()->create();
    Storage::fake('s3');

    $response = $this->actingAs($admin, 'admin')->put(route('admin.home.bookus.section.update'), [
        'heading_prefix' => 'Prefix',
        'heading_highlight' => 'Highlight',
        'description' => 'Description text.',
        'image_one' => UploadedFile::fake()->image('main.jpg', 1060, 516),
        'image_two' => UploadedFile::fake()->image('secondary1.jpg', 512, 516),
        'image_three' => UploadedFile::fake()->image('secondary2.jpg', 512, 516),
    ]);

    $response->assertRedirect();
    $section = HomeBookUsSection::first();
    expect($section->image_one)->toStartWith('home-bookus/');
    expect($section->image_two)->toStartWith('home-bookus/');
    expect($section->image_three)->toStartWith('home-bookus/');
    Storage::disk('s3')->assertExists($section->image_one);
    Storage::disk('s3')->assertExists($section->image_two);
    Storage::disk('s3')->assertExists($section->image_three);
});

test('heading fields and description are required to update the section', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->put(route('admin.home.bookus.section.update'), [])
        ->assertSessionHasErrors(['heading_prefix', 'heading_highlight', 'description']);
});

test('admin can add a new faq', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->post(route('admin.home.bookus.faqs.store'), [
        'title' => 'Our Values',
        'description' => 'We value transparency and compassion in every interaction.',
        'sort_order' => 5,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('home_book_us_faqs', [
        'title' => 'Our Values',
        'sort_order' => 5,
    ]);
});

test('a new faq without an explicit order is appended after the current highest order', function () {
    $admin = Admin::factory()->create();
    HomeBookUsFaq::query()->delete();
    HomeBookUsFaq::factory()->create(['sort_order' => 3]);

    $this->actingAs($admin, 'admin')->post(route('admin.home.bookus.faqs.store'), [
        'title' => 'New FAQ',
        'description' => 'A brand new FAQ item.',
    ]);

    expect(HomeBookUsFaq::firstWhere('title', 'New FAQ')->sort_order)->toBe(4);
});

test('title is required to add a faq', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.home.bookus.faqs.store'), [
        'description' => 'Missing a title.',
    ])->assertSessionHasErrors('title');
});

test('admin can update a faq', function () {
    $admin = Admin::factory()->create();
    $faq = HomeBookUsFaq::factory()->create(['title' => 'Old Title']);

    $response = $this->actingAs($admin, 'admin')->put(route('admin.home.bookus.faqs.update', $faq), [
        'title' => 'New Title',
        'description' => $faq->description,
        'sort_order' => 9,
    ]);

    $response->assertRedirect();
    expect($faq->fresh()->title)->toBe('New Title');
    expect($faq->fresh()->sort_order)->toBe(9);
});

test('admin can delete a faq', function () {
    $admin = Admin::factory()->create();
    $faq = HomeBookUsFaq::factory()->create();

    $response = $this->actingAs($admin, 'admin')->delete(route('admin.home.bookus.faqs.destroy', $faq));

    $response->assertRedirect();
    $this->assertModelMissing($faq);
});
