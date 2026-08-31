<?php

namespace App\Services\Frontend;

use App\Enums\DayOfWeek;
use App\Models\BusinessHour;
use App\Models\Experience;
use App\Models\User;
use App\Services\Doctor\DoctorBusinessHourService;
use App\Services\Doctor\DoctorServiceManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DoctorProfileService
{
    public function __construct(
        protected DoctorServiceManager $doctorServices,
        protected DoctorBusinessHourService $businessHours,
    ) {}

    /**
     * Everything the public doctor-details page needs, derived entirely from
     * data the doctor has actually entered - nothing here is fabricated.
     *
     * @return array<string, mixed>
     */
    public function present(User $doctor): array
    {
        $experiences = $doctor->experiences;
        $educations = $doctor->educations;
        $serviceGroups = $this->doctorServices->forDoctor($doctor);
        $services = $serviceGroups->flatMap(fn (array $group) => $group['services']);
        $businessHours = $this->businessHours->forDoctor($doctor);
        $currentExperience = $experiences->firstWhere('currently_working', true) ?? $experiences->first();
        $todayKey = strtolower(now()->format('l'));

        return [
            'currentExperience' => $currentExperience,
            'yearsInPractice' => $this->yearsInPractice($experiences),
            'credentialsLine' => $this->credentialsLine($doctor, $educations),
            'bio' => $this->bio($doctor, $experiences, $serviceGroups),
            'specialities' => $serviceGroups->pluck('speciality')->filter()->unique('id')->values(),
            'serviceGroups' => $serviceGroups,
            'services' => $services,
            'priceRange' => $this->priceRange($services),
            'primaryClinic' => $doctor->clinics->first(),
            'businessHours' => $businessHours,
            'todayBusinessHour' => $businessHours->first(fn (BusinessHour $hour) => $hour->day->value === $todayKey),
            'availabilitySlots' => $this->upcomingAvailability($businessHours),
        ];
    }

    /**
     * Whole years since the doctor's earliest recorded experience, or null
     * when they haven't added any (rounds down, so a brand-new entry
     * doesn't misleadingly read as "1 Year").
     *
     * @param  Collection<int, Experience>  $experiences
     */
    protected function yearsInPractice(Collection $experiences): ?int
    {
        if ($experiences->isEmpty()) {
            return null;
        }

        $years = (int) $experiences->min('start_date')->diffInYears(now());

        return $years > 0 ? $years : null;
    }

    /**
     * "MBBS, MD - Cardiologist" style line built from the doctor's own
     * education courses and designation - each part is dropped if unset.
     *
     * @param  Collection<int, \App\Models\Education>  $educations
     */
    protected function credentialsLine(User $doctor, Collection $educations): ?string
    {
        $courses = $educations->pluck('course')->filter()->unique()->values();

        $parts = array_filter([
            $courses->isNotEmpty() ? $courses->implode(', ') : null,
            $doctor->designation,
        ]);

        return $parts === [] ? null : implode(' - ', $parts);
    }

    /**
     * A short bio composed from real profile fields (designation, years of
     * experience, specialities, languages) since there is no free-text bio
     * column to display verbatim.
     *
     * @param  Collection<int, Experience>  $experiences
     * @param  Collection<int, array{speciality: \App\Models\Speciality, services: Collection}>  $serviceGroups
     */
    protected function bio(User $doctor, Collection $experiences, Collection $serviceGroups): string
    {
        $name = 'Dr. '.($doctor->display_name ?: trim($doctor->first_name.' '.$doctor->last_name));
        $years = $this->yearsInPractice($experiences);
        $specialities = $serviceGroups->pluck('speciality.name')->filter()->unique()->values();
        $languages = collect($doctor->known_languages ?? [])->filter()->values();

        $sentence = $name.($doctor->designation ? ' is a '.$doctor->designation : ' is a healthcare provider on our platform');

        if ($years) {
            $sentence .= ' with '.$years.' '.Str::plural('year', $years).' of experience';
        }

        if ($specialities->isNotEmpty()) {
            $sentence .= ', specializing in '.$specialities->implode(', ');
        }

        $sentence .= '.';

        if ($languages->isNotEmpty()) {
            $sentence .= ' Speaks '.$languages->implode(', ').'.';
        }

        return $sentence;
    }

    /**
     * "$50.00" or "$50.00 - $120.00" from the doctor's own service prices, or
     * null when they haven't priced any services yet.
     *
     * @param  Collection<int, \App\Models\DoctorService>  $services
     */
    protected function priceRange(Collection $services): ?string
    {
        if ($services->isEmpty()) {
            return null;
        }

        $min = (float) $services->min('price');
        $max = (float) $services->max('price');

        return $min === $max
            ? '$'.number_format($min, 2)
            : '$'.number_format($min, 2).' - $'.number_format($max, 2);
    }

    /**
     * The next open business-hour days (date + time range), walking forward
     * from today until enough are found or a month has passed with none.
     *
     * @param  Collection<int, BusinessHour>  $businessHours
     * @return Collection<int, array{date: \Illuminate\Support\Carbon, from: \Illuminate\Support\Carbon, to: \Illuminate\Support\Carbon}>
     */
    protected function upcomingAvailability(Collection $businessHours, int $days = 8, int $searchWindow = 30): Collection
    {
        $byDay = $businessHours->keyBy(fn (BusinessHour $hour) => $hour->day->value);
        $slots = collect();

        for ($i = 0; $i < $searchWindow && $slots->count() < $days; $i++) {
            $date = now()->copy()->addDays($i);
            $hour = $byDay->get(DayOfWeek::from(strtolower($date->format('l')))->value);

            if ($hour?->is_open && $hour->from_time && $hour->to_time) {
                $slots->push(['date' => $date, 'from' => $hour->from_time, 'to' => $hour->to_time]);
            }
        }

        return $slots;
    }
}
