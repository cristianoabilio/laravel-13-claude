<?php

namespace App\Services\Doctor;

use App\Enums\DayOfWeek;
use App\Models\BusinessHour;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DoctorBusinessHourService
{
    /**
     * Every day of the week for the doctor, backed by a persisted row where one exists,
     * or a sensible unsaved default (open Mon-Fri 9am-6pm, closed on weekends) otherwise.
     *
     * @return Collection<int, BusinessHour>
     */
    public function forDoctor(User $doctor): Collection
    {
        $existing = $doctor->businessHours->keyBy(fn (BusinessHour $hour) => $hour->day->value);

        return collect(DayOfWeek::cases())->map(
            fn (DayOfWeek $day) => $existing->get($day->value) ?? new BusinessHour([
                'day' => $day,
                'is_open' => ! $day->isWeekend(),
                'from_time' => '09:00:00',
                'to_time' => '18:00:00',
            ])
        );
    }

    /**
     * Create or update each day's business hours for the doctor.
     *
     * @param  array<string, array{is_open?: string|null, from?: string|null, to?: string|null}>  $businessHours
     * @return Collection<int, BusinessHour>
     */
    public function update(User $doctor, array $businessHours): Collection
    {
        return collect(DayOfWeek::cases())->map(function (DayOfWeek $day) use ($doctor, $businessHours) {
            $data = $businessHours[$day->value] ?? [];
            $isOpen = (bool) ($data['is_open'] ?? false);

            return $doctor->businessHours()->updateOrCreate(
                ['day' => $day->value],
                [
                    'is_open' => $isOpen,
                    'from_time' => $isOpen ? $this->parseTime($data['from'] ?? null) : null,
                    'to_time' => $isOpen ? $this->parseTime($data['to'] ?? null) : null,
                ]
            );
        });
    }

    protected function parseTime(?string $value): ?string
    {
        return $value ? Carbon::createFromFormat('h:i A', $value)->format('H:i:s') : null;
    }
}
