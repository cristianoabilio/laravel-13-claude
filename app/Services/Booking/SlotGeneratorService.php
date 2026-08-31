<?php

namespace App\Services\Booking;

use App\Enums\AppointmentStatus;
use App\Enums\DayOfWeek;
use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\User;
use App\Services\Doctor\DoctorBusinessHourService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotGeneratorService
{
    public function __construct(
        protected DoctorBusinessHourService $businessHours,
    ) {}

    /**
     * Every bookable slot for the doctor on the given date, based on their real
     * working hours and blocked out against their existing (non-cancelled)
     * appointments. Nothing is hardcoded - this reflects real schedule data.
     *
     * @return Collection<int, array{start: Carbon, end: Carbon, label: string, available: bool}>
     */
    public function forDate(User $doctor, Carbon $date, int $durationMinutes): Collection
    {
        $hour = $this->businessHours->forDoctor($doctor)
            ->first(fn (BusinessHour $businessHour) => $businessHour->day === DayOfWeek::from(strtolower($date->format('l'))));

        if (! $hour || ! $hour->is_open || ! $hour->from_time || ! $hour->to_time) {
            return collect();
        }

        $windowStart = $this->combine($date, $hour->from_time);
        $windowEnd = $this->combine($date, $hour->to_time);

        $booked = $this->bookedRanges($doctor, $date);
        $now = now();
        $slots = collect();

        for ($start = $windowStart->copy(); $start->copy()->addMinutes($durationMinutes)->lte($windowEnd); $start->addMinutes($durationMinutes)) {
            $end = $start->copy()->addMinutes($durationMinutes);

            $isPast = $date->isToday() && $start->lte($now);
            $overlapsBooking = $booked->contains(
                fn (array $range) => $start->lt($range['end']) && $end->gt($range['start'])
            );

            $slots->push([
                'start' => $start->copy(),
                'end' => $end->copy(),
                'label' => $start->format('h:i A'),
                'available' => ! $isPast && ! $overlapsBooking,
            ]);
        }

        return $slots;
    }

    /**
     * @return Collection<int, array{start: Carbon, end: Carbon}>
     */
    protected function bookedRanges(User $doctor, Carbon $date): Collection
    {
        return Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date->toDateString())
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->get(['start_time', 'end_time'])
            ->map(fn (Appointment $appointment) => [
                'start' => $this->combine($date, $appointment->start_time),
                'end' => $this->combine($date, $appointment->end_time),
            ]);
    }

    /**
     * Apply a time-of-day (hour/minute/second) onto a specific calendar date.
     */
    protected function combine(Carbon $date, Carbon $time): Carbon
    {
        return $date->copy()->setTime($time->hour, $time->minute, $time->second);
    }
}
