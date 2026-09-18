<?php

use App\Models\User;

test('the homepage defaults to english when no locale has been chosen', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('<html lang="en"', false);
    $response->assertSee('Home');
});

test('switching to a supported locale persists it in session and redirects back', function () {
    $response = $this->from(route('home'))->get(route('locale.switch', 'pt_BR'));

    $response->assertRedirect(route('home'));
    $this->assertSame('pt_BR', session('locale'));
});

test('switching to an unsupported locale is rejected', function () {
    $this->get(route('locale.switch', 'fr'))->assertNotFound();

    $this->assertNull(session('locale'));
});

test('the homepage renders in portuguese once the locale is switched', function () {
    $this->get(route('locale.switch', 'pt_BR'));

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('<html lang="pt-BR"', false);
    $response->assertSee('Início');
    $response->assertDontSee('>Home<', false);
});

test('the locale choice persists across subsequent requests without switching again', function () {
    $this->get(route('locale.switch', 'pt_BR'));

    $first = $this->get(route('home'));
    $second = $this->get(route('doctor.all.speciality', \App\Models\Speciality::factory()->create()));

    $first->assertSee('Início');
    $second->assertSee('Filtrar');
});

test('validation error messages are translated to portuguese once the locale is switched', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    \App\Models\Appointment::factory()->create([
        'doctor_id' => $doctor->id,
        'patient_id' => $patient->id,
        'status' => \App\Enums\AppointmentStatus::Completed,
    ]);

    $this->get(route('locale.switch', 'pt_BR'));

    $response = $this->actingAs($patient)->post(route('doctor.reviews.store', $doctor->id), []);

    $response->assertSessionHasErrors(['rating', 'comment']);
    $errors = session('errors')->getBag('default');
    expect($errors->first('rating'))->toContain('obrigatório');
    expect($errors->first('comment'))->toContain('obrigatório');
});

test('validation error messages stay in english by default', function () {
    $patient = User::factory()->patient()->create();

    $response = $this->actingAs($patient)->post(route('doctor.reviews.store', User::factory()->doctor()->create()->id), []);

    $response->assertSessionHasErrors();
    $errors = session('errors')->getBag('default');
    expect($errors->first('rating'))->toContain('required');
});
