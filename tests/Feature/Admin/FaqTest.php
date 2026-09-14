<?php

use App\Models\Admin;
use App\Models\Faq;
use App\Models\User;

test('guests are redirected away from the manage faqs page', function () {
    $this->get(route('admin.faqs.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the manage faqs page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.faqs.index'))
        ->assertRedirect(route('admin.login'));
});

test('admin sees the seeded default faqs in order', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.faqs.index'));

    $response->assertOk();
    $response->assertSee('How do I book an appointment with a doctor?');
    $response->assertSee('Can I book appointments for family members or dependents?');
    $response->assertViewHas('faqs', fn ($faqs) => $faqs->count() === 5);
});

test('admin can add a new faq', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->post(route('admin.faqs.store'), [
        'question' => 'Do you accept insurance?',
        'answer' => 'Yes, we accept most major insurance providers.',
        'sort_order' => 5,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('faqs', [
        'question' => 'Do you accept insurance?',
        'sort_order' => 5,
    ]);
});

test('a new faq without an explicit order is appended after the current highest order', function () {
    $admin = Admin::factory()->create();
    Faq::query()->delete();
    Faq::factory()->create(['sort_order' => 3]);

    $this->actingAs($admin, 'admin')->post(route('admin.faqs.store'), [
        'question' => 'New question?',
        'answer' => 'A brand new answer.',
    ]);

    expect(Faq::firstWhere('question', 'New question?')->sort_order)->toBe(4);
});

test('question is required to add a faq', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.faqs.store'), [
        'answer' => 'Missing a question.',
    ])->assertSessionHasErrors('question');
});

test('answer is required to add a faq', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->post(route('admin.faqs.store'), [
        'question' => 'Missing an answer?',
    ])->assertSessionHasErrors('answer');
});

test('admin can update a faq', function () {
    $admin = Admin::factory()->create();
    $faq = Faq::factory()->create(['question' => 'Old question?']);

    $response = $this->actingAs($admin, 'admin')->put(route('admin.faqs.update', $faq), [
        'question' => 'New question?',
        'answer' => $faq->answer,
        'sort_order' => 9,
    ]);

    $response->assertRedirect();
    expect($faq->fresh()->question)->toBe('New question?');
    expect($faq->fresh()->sort_order)->toBe(9);
});

test('admin can delete a faq', function () {
    $admin = Admin::factory()->create();
    $faq = Faq::factory()->create();

    $response = $this->actingAs($admin, 'admin')->delete(route('admin.faqs.destroy', $faq));

    $response->assertRedirect();
    $this->assertModelMissing($faq);
});
