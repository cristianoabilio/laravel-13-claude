<?php

use App\Enums\DayOfWeek;
use App\Models\BusinessHour;
use App\Models\User;

test('guests are redirected away from the doctor business hours page', function () {
    $this->get(route('doctor.business'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor business hours page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.business'))
        ->assertRedirect(route('dashboard'));
});

test('a doctor with no saved hours sees sensible unsaved defaults for every day', function () {
    $doctor = User::factory()->doctor()->create();

    $response = $this->actingAs($doctor)->get(route('doctor.business'));

    $response->assertOk();
    $response->assertViewHas('businessHours', function ($businessHours) {
        return $businessHours->count() === 7
            && $businessHours->every(fn ($hour) => ! $hour->exists)
            && $businessHours->firstWhere('day', DayOfWeek::Monday)->is_open === true
            && $businessHours->firstWhere('day', DayOfWeek::Saturday)->is_open === false;
    });
});

test('doctor sees their own persisted hours mixed with defaults for untouched days', function () {
    $doctor = User::factory()->doctor()->create();
    BusinessHour::factory()->create([
        'doctor_id' => $doctor->id,
        'day' => DayOfWeek::Sunday,
        'is_open' => true,
        'from_time' => '10:00:00',
        'to_time' => '14:00:00',
    ]);

    $response = $this->actingAs($doctor)->get(route('doctor.business'));

    $response->assertViewHas('businessHours', function ($businessHours) {
        $sunday = $businessHours->firstWhere('day', DayOfWeek::Sunday);

        return $sunday->exists && $sunday->is_open === true;
    });
});

test('doctor can save business hours for every day in one request', function () {
    $doctor = User::factory()->doctor()->create();

    $payload = [
        'business_hours' => [
            'monday' => ['is_open' => '1', 'from' => '09:00 AM', 'to' => '06:00 PM'],
            'tuesday' => ['is_open' => '1', 'from' => '09:00 AM', 'to' => '06:00 PM'],
            'wednesday' => ['is_open' => '1', 'from' => '09:00 AM', 'to' => '06:00 PM'],
            'thursday' => ['is_open' => '1', 'from' => '09:00 AM', 'to' => '06:00 PM'],
            'friday' => ['is_open' => '1', 'from' => '09:00 AM', 'to' => '05:00 PM'],
            'saturday' => ['is_open' => '1', 'from' => '10:00 AM', 'to' => '02:00 PM'],
            'sunday' => ['is_open' => '0'],
        ],
    ];

    $this->actingAs($doctor)
        ->put(route('doctor.business.update'), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect(BusinessHour::where('doctor_id', $doctor->id)->count())->toBe(7);

    $monday = BusinessHour::where('doctor_id', $doctor->id)->where('day', DayOfWeek::Monday)->first();
    expect($monday->is_open)->toBeTrue();
    expect($monday->from_time->format('H:i'))->toBe('09:00');
    expect($monday->to_time->format('H:i'))->toBe('18:00');

    $sunday = BusinessHour::where('doctor_id', $doctor->id)->where('day', DayOfWeek::Sunday)->first();
    expect($sunday->is_open)->toBeFalse();
    expect($sunday->from_time)->toBeNull();
    expect($sunday->to_time)->toBeNull();
});

test('saving again updates the same 7 rows instead of creating duplicates', function () {
    $doctor = User::factory()->doctor()->create();
    foreach (DayOfWeek::cases() as $day) {
        BusinessHour::factory()->create(['doctor_id' => $doctor->id, 'day' => $day]);
    }

    $this->actingAs($doctor)->put(route('doctor.business.update'), [
        'business_hours' => [
            'monday' => ['is_open' => '1', 'from' => '08:00 AM', 'to' => '04:00 PM'],
        ],
    ])->assertSessionHasNoErrors();

    expect(BusinessHour::where('doctor_id', $doctor->id)->count())->toBe(7);
    $monday = BusinessHour::where('doctor_id', $doctor->id)->where('day', DayOfWeek::Monday)->first();
    expect($monday->from_time->format('H:i'))->toBe('08:00');
});

test('from and to are required when a day is marked open', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.business.update'), [
            'business_hours' => [
                'monday' => ['is_open' => '1'],
            ],
        ])
        ->assertSessionHasErrors(['business_hours.monday.from', 'business_hours.monday.to']);
});

test('to must be after from', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.business.update'), [
            'business_hours' => [
                'monday' => ['is_open' => '1', 'from' => '06:00 PM', 'to' => '09:00 AM'],
            ],
        ])
        ->assertSessionHasErrors(['business_hours.monday.to']);
});

test('from and to are not required when a day is closed', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.business.update'), [
            'business_hours' => [
                'monday' => ['is_open' => '0'],
            ],
        ])
        ->assertSessionHasNoErrors();
});

test('a doctor only sees and affects their own business hours', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    BusinessHour::factory()->create(['doctor_id' => $other->id, 'day' => DayOfWeek::Monday]);

    $this->actingAs($doctor)->put(route('doctor.business.update'), [
        'business_hours' => [
            'monday' => ['is_open' => '1', 'from' => '09:00 AM', 'to' => '05:00 PM'],
        ],
    ]);

    expect(BusinessHour::where('doctor_id', $doctor->id)->count())->toBe(7);
    expect(BusinessHour::where('doctor_id', $other->id)->count())->toBe(1);
});
