<?php

use App\Models\Admin;
use App\Models\HomeService;
use App\Models\User;

test('guests are redirected away from the manage services page', function () {
    $this->get(route('admin.home.services.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the manage services page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.home.services.index'))
        ->assertRedirect(route('admin.login'));
});

test('admin sees the seeded default services in order', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.home.services.index'));

    $response->assertOk();
    $response->assertSee('Multi Speciality Treatments & Doctors');
    $response->assertSee('Home Care Services');
    $response->assertViewHas('homeServices', fn ($services) => $services->count() === 7);
});

test('admin can add a new service', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->post(route('admin.home.services.store'), [
        'title' => 'Emergency Care',
        'url' => 'https://example.com/emergency',
        'sort_order' => 5,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('home_services', [
        'title' => 'Emergency Care',
        'url' => 'https://example.com/emergency',
        'sort_order' => 5,
    ]);
});

test('a new service without an explicit order is appended after the current highest order', function () {
    $admin = Admin::factory()->create();
    HomeService::query()->delete();
    HomeService::factory()->create(['sort_order' => 3]);

    $this->actingAs($admin, 'admin')->post(route('admin.home.services.store'), [
        'title' => 'New Service',
    ]);

    expect(HomeService::firstWhere('title', 'New Service')->sort_order)->toBe(4);
});

test('title is required to add a service', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.home.services.store'), [])
        ->assertSessionHasErrors('title');
});

test('the url must be well formed if provided', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.home.services.store'), [
        'title' => 'Bad Link Service',
        'url' => 'not-a-url',
    ])->assertSessionHasErrors('url');
});

test('admin can update a service', function () {
    $admin = Admin::factory()->create();
    $service = HomeService::factory()->create(['title' => 'Old Title']);

    $response = $this->actingAs($admin, 'admin')->put(route('admin.home.services.update', $service), [
        'title' => 'New Title',
        'sort_order' => 9,
    ]);

    $response->assertRedirect();
    expect($service->fresh()->title)->toBe('New Title');
    expect($service->fresh()->sort_order)->toBe(9);
});

test('admin can delete a service', function () {
    $admin = Admin::factory()->create();
    $service = HomeService::factory()->create();

    $response = $this->actingAs($admin, 'admin')->delete(route('admin.home.services.destroy', $service));

    $response->assertRedirect();
    $this->assertModelMissing($service);
});
