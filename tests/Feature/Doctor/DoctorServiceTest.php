<?php

use App\Models\DoctorService;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;

test('guests are redirected away from the doctor specialities page', function () {
    $this->get(route('doctor.specialities'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor specialities page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.specialities'))
        ->assertRedirect(route('dashboard'));
});

test('page loads real specialities and services from the database', function () {
    $doctor = User::factory()->doctor()->create();
    $speciality = Speciality::factory()->create(['name' => 'Cardiology']);
    $service = Service::factory()->create(['speciality_id' => $speciality->id, 'name' => 'ECG']);

    $response = $this->actingAs($doctor)->get(route('doctor.specialities'));

    $response->assertOk();
    $response->assertViewHas('specialities', fn ($specialities) => $specialities->pluck('id')->contains($speciality->id));
    $response->assertViewHas('servicesBySpeciality', function ($servicesBySpeciality) use ($speciality, $service) {
        return $servicesBySpeciality->get($speciality->id)?->pluck('id')->contains($service->id);
    });
});

test('doctor sees their own service offerings grouped by speciality', function () {
    $doctor = User::factory()->doctor()->create();
    $cardiology = Speciality::factory()->create(['name' => 'Cardiology']);
    $ecg = Service::factory()->create(['speciality_id' => $cardiology->id, 'name' => 'ECG']);
    $doctorService = DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $ecg->id]);

    $response = $this->actingAs($doctor)->get(route('doctor.specialities'));

    $response->assertOk();
    $response->assertViewHas('groups', function ($groups) use ($cardiology, $doctorService) {
        $group = $groups->firstWhere(fn ($g) => $g['speciality']->is($cardiology));

        return $group && $group['services']->pluck('id')->contains($doctorService->id);
    });
});

test('a doctor only sees their own service offerings, not another doctors', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    DoctorService::factory()->create(['doctor_id' => $other->id]);

    $response = $this->actingAs($doctor)->get(route('doctor.specialities'));

    $response->assertViewHas('groups', fn ($groups) => $groups->isEmpty());
});

test('doctor can add a new service offering', function () {
    $doctor = User::factory()->doctor()->create();
    $speciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);

    $response = $this->actingAs($doctor)->put(route('doctor.services.update'), [
        'services' => [
            ['service_id' => $service->id, 'price' => '150.00', 'description' => 'Full checkup'],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $doctorService = DoctorService::where('doctor_id', $doctor->id)->where('service_id', $service->id)->first();
    expect($doctorService)->not->toBeNull();
    expect((float) $doctorService->price)->toBe(150.0);
    expect($doctorService->description)->toBe('Full checkup');
});

test('saving again updates the same offering instead of creating a duplicate', function () {
    $doctor = User::factory()->doctor()->create();
    $service = Service::factory()->create();
    DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $service->id, 'price' => 100]);

    $this->actingAs($doctor)->put(route('doctor.services.update'), [
        'services' => [
            ['service_id' => $service->id, 'price' => '200.00'],
        ],
    ])->assertSessionHasNoErrors();

    expect(DoctorService::where('doctor_id', $doctor->id)->count())->toBe(1);
    expect((float) DoctorService::where('doctor_id', $doctor->id)->first()->price)->toBe(200.0);
});

test('completely blank stub rows are ignored instead of failing validation', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.services.update'), [
            'services' => [
                ['service_id' => '', 'price' => '', 'description' => ''],
            ],
        ])
        ->assertSessionHasNoErrors();

    expect(DoctorService::count())->toBe(0);
});

test('service_id and price are required, and the service must exist', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.services.update'), [
            'services' => [
                ['service_id' => 999999, 'price' => ''],
            ],
        ])
        ->assertSessionHasErrors(['services.0.service_id', 'services.0.price']);
});

test('doctor can delete a single service offering', function () {
    $doctor = User::factory()->doctor()->create();
    $doctorService = DoctorService::factory()->create(['doctor_id' => $doctor->id]);

    $this->actingAs($doctor)
        ->delete(route('doctor.services.destroy', $doctorService))
        ->assertRedirect();

    $this->assertModelMissing($doctorService);
});

test('a doctor cannot delete another doctors service offering', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $doctorService = DoctorService::factory()->create(['doctor_id' => $other->id]);

    $this->actingAs($doctor)
        ->delete(route('doctor.services.destroy', $doctorService))
        ->assertForbidden();

    $this->assertModelExists($doctorService);
});

test('doctor can delete every offering under a speciality at once', function () {
    $doctor = User::factory()->doctor()->create();
    $speciality = Speciality::factory()->create();
    $serviceOne = Service::factory()->create(['speciality_id' => $speciality->id]);
    $serviceTwo = Service::factory()->create(['speciality_id' => $speciality->id]);
    $otherSpecialityService = Service::factory()->create();

    DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $serviceOne->id]);
    DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $serviceTwo->id]);
    $keep = DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $otherSpecialityService->id]);

    $this->actingAs($doctor)
        ->delete(route('doctor.services.speciality.destroy', $speciality))
        ->assertRedirect();

    expect(DoctorService::where('doctor_id', $doctor->id)->count())->toBe(1);
    $this->assertModelExists($keep);
});

test('deleting a speciality only affects the authenticated doctors offerings', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $speciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);
    $othersOffering = DoctorService::factory()->create(['doctor_id' => $other->id, 'service_id' => $service->id]);

    $this->actingAs($doctor)
        ->delete(route('doctor.services.speciality.destroy', $speciality))
        ->assertRedirect();

    $this->assertModelExists($othersOffering);
});
