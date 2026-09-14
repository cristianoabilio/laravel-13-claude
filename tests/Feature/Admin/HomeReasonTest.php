<?php

use App\Models\Admin;
use App\Models\HomeReason;
use App\Models\HomeReasonSection;
use App\Models\User;

test('guests are redirected away from the manage reasons page', function () {
    $this->get(route('admin.home.reasons.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the manage reasons page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.home.reasons.index'))
        ->assertRedirect(route('admin.login'));
});

test('admin sees the seeded default reasons in order', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.home.reasons.index'));

    $response->assertOk();
    $response->assertSee('Compelling Reasons to Choose');
    $response->assertSee('Follow-Up Care');
    $response->assertSee('Patient-Centered Approach');
    $response->assertSee('Convenient Access');
    $response->assertViewHas('homeReasons', fn ($reasons) => $reasons->count() === 3);
});

test('admin can update the section heading', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->put(route('admin.home.reasons.section.update'), [
        'badge_text' => 'New Badge',
        'heading' => 'New Heading',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('home_reason_sections', [
        'badge_text' => 'New Badge',
        'heading' => 'New Heading',
    ]);
});

test('heading is required to update the section', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->put(route('admin.home.reasons.section.update'), [
        'badge_text' => 'New Badge',
    ])->assertSessionHasErrors('heading');
});

test('admin can add a new reason', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->post(route('admin.home.reasons.store'), [
        'icon' => 'isax isax-heart',
        'icon_color' => 'text-danger',
        'title' => 'Emergency Support',
        'description' => 'Round the clock emergency support for every patient.',
        'sort_order' => 5,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('home_reasons', [
        'title' => 'Emergency Support',
        'icon' => 'isax isax-heart',
        'icon_color' => 'text-danger',
        'sort_order' => 5,
    ]);
});

test('a new reason without an explicit order is appended after the current highest order', function () {
    $admin = Admin::factory()->create();
    HomeReason::query()->delete();
    HomeReason::factory()->create(['sort_order' => 3]);

    $this->actingAs($admin, 'admin')->post(route('admin.home.reasons.store'), [
        'icon' => 'isax isax-heart',
        'icon_color' => 'text-danger',
        'title' => 'New Reason',
        'description' => 'A brand new reason.',
    ]);

    expect(HomeReason::firstWhere('title', 'New Reason')->sort_order)->toBe(4);
});

test('title is required to add a reason', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.home.reasons.store'), [
        'icon' => 'isax isax-heart',
        'icon_color' => 'text-danger',
        'description' => 'Missing a title.',
    ])->assertSessionHasErrors('title');
});

test('icon and icon color are required to add a reason', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.home.reasons.store'), [
        'title' => 'Missing Icon',
        'description' => 'Missing an icon.',
    ])->assertSessionHasErrors(['icon', 'icon_color']);
});

test('admin can update a reason', function () {
    $admin = Admin::factory()->create();
    $reason = HomeReason::factory()->create(['title' => 'Old Title']);

    $response = $this->actingAs($admin, 'admin')->put(route('admin.home.reasons.update', $reason), [
        'icon' => $reason->icon,
        'icon_color' => $reason->icon_color,
        'title' => 'New Title',
        'description' => $reason->description,
        'sort_order' => 9,
    ]);

    $response->assertRedirect();
    expect($reason->fresh()->title)->toBe('New Title');
    expect($reason->fresh()->sort_order)->toBe(9);
});

test('admin can delete a reason', function () {
    $admin = Admin::factory()->create();
    $reason = HomeReason::factory()->create();

    $response = $this->actingAs($admin, 'admin')->delete(route('admin.home.reasons.destroy', $reason));

    $response->assertRedirect();
    $this->assertModelMissing($reason);
});
