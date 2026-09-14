<?php

use App\Models\Admin;
use App\Models\HomeBanner;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the manage banner page', function () {
    $this->get(route('admin.home.banner.edit'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the manage banner page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.home.banner.edit'))
        ->assertRedirect(route('admin.login'));
});

test('admin sees sensible defaults when no banner has been saved yet', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.home.banner.edit'));

    $response->assertOk();
    $response->assertViewHas('banner', function ($banner) {
        return $banner->heading_prefix === 'Discover Health: Find Your Trusted'
            && $banner->heading_highlight === 'Doctors'
            && $banner->heading_suffix === 'Today';
    });
});

test('admin sees the previously saved banner', function () {
    $admin = Admin::factory()->create();
    HomeBanner::factory()->create(['heading_highlight' => 'Specialists']);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.home.banner.edit'));

    $response->assertOk();
    $response->assertSee('Specialists');
});

test('admin can update the banner heading text', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->put(route('admin.home.banner.update'), [
        'heading_prefix' => 'Your Health, Our Priority: Meet',
        'heading_highlight' => 'Specialists',
        'heading_suffix' => 'Now',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('home_banners', [
        'heading_prefix' => 'Your Health, Our Priority: Meet',
        'heading_highlight' => 'Specialists',
        'heading_suffix' => 'Now',
    ]);
    $this->assertDatabaseCount('home_banners', 1);
});

test('updating the banner twice updates the same row instead of creating a new one', function () {
    $admin = Admin::factory()->create();
    HomeBanner::factory()->create(['heading_highlight' => 'Doctors']);

    $this->actingAs($admin, 'admin')->put(route('admin.home.banner.update'), [
        'heading_prefix' => 'Discover Health: Find Your Trusted',
        'heading_highlight' => 'Specialists',
        'heading_suffix' => 'Today',
    ]);

    $this->assertDatabaseCount('home_banners', 1);
    expect(HomeBanner::first()->heading_highlight)->toBe('Specialists');
});

test('admin can upload a banner image which gets resized to 464x606', function () {
    $admin = Admin::factory()->create();
    $image = UploadedFile::fake()->image('banner.jpg', 1200, 1200);

    $response = $this->actingAs($admin, 'admin')->put(route('admin.home.banner.update'), [
        'heading_prefix' => 'Discover Health: Find Your Trusted',
        'heading_highlight' => 'Doctors',
        'heading_suffix' => 'Today',
        'image' => $image,
    ]);

    $response->assertRedirect();
    $banner = HomeBanner::first();
    expect($banner->image)->not->toBeNull();
    Storage::disk('s3')->assertExists($banner->image);

    $contents = Storage::disk('s3')->get($banner->image);
    $dimensions = getimagesizefromstring($contents);
    expect($dimensions[0])->toBe(464);
    expect($dimensions[1])->toBe(606);
});

test('uploading a new image deletes the previous one', function () {
    $admin = Admin::factory()->create();
    $oldImage = UploadedFile::fake()->image('old.jpg', 800, 800)->store('home-banner', 's3');
    $banner = HomeBanner::factory()->create(['image' => $oldImage]);

    $newImage = UploadedFile::fake()->image('new.jpg', 800, 800);

    $this->actingAs($admin, 'admin')->put(route('admin.home.banner.update'), [
        'heading_prefix' => $banner->heading_prefix,
        'heading_highlight' => $banner->heading_highlight,
        'heading_suffix' => $banner->heading_suffix,
        'image' => $newImage,
    ]);

    Storage::disk('s3')->assertMissing($oldImage);
    expect(HomeBanner::first()->image)->not->toBe($oldImage);
});

test('heading fields are required', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->put(route('admin.home.banner.update'), [])
        ->assertSessionHasErrors(['heading_prefix', 'heading_highlight', 'heading_suffix']);
});

test('the uploaded file must be an accepted image type', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->put(route('admin.home.banner.update'), [
        'heading_prefix' => 'Discover Health: Find Your Trusted',
        'heading_highlight' => 'Doctors',
        'heading_suffix' => 'Today',
        'image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('image');
});

test('admin can upload an svg banner image and it is stored as-is without being rasterized', function () {
    $admin = Admin::factory()->create();
    $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" width="464" height="606"><rect width="100%" height="100%" fill="blue"/></svg>';
    $svg = UploadedFile::fake()->createWithContent('banner.svg', $svgContent);

    $response = $this->actingAs($admin, 'admin')->put(route('admin.home.banner.update'), [
        'heading_prefix' => 'Discover Health: Find Your Trusted',
        'heading_highlight' => 'Doctors',
        'heading_suffix' => 'Today',
        'image' => $svg,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $banner = HomeBanner::first();
    expect($banner->image)->toEndWith('.svg');
    Storage::disk('s3')->assertExists($banner->image);
    expect(Storage::disk('s3')->get($banner->image))->toBe($svgContent);
});
