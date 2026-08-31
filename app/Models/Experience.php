<?php

namespace App\Models;

use App\Enums\EmploymentType;
use App\Models\Concerns\ResolvesStorageUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'doctor_id', 'hospital_logo', 'title', 'hospital', 'years_of_experience',
    'location', 'employment_type', 'job_description', 'start_date', 'end_date', 'currently_working',
])]
class Experience extends Model
{
    /** @use HasFactory<\Database\Factories\ExperienceFactory> */
    use HasFactory, ResolvesStorageUrl;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'currently_working' => 'boolean',
            'employment_type' => EmploymentType::class,
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * The publicly resolvable URL for the hospital logo, whether it's a
     * bundled template asset (public/backend/...) or an upload on the S3/MinIO disk.
     */
    protected function hospitalLogoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->hospital_logo),
        );
    }

    /**
     * "Jan 2020 - Present" or "Jan 2020 - Mar 2022".
     */
    protected function dateRangeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->start_date->format('M Y').' - '.(
                $this->currently_working ? 'Present' : ($this->end_date?->format('M Y') ?? 'Present')
            ),
        );
    }

    /**
     * "2 Years 3 Months" spanning the start date to the end date (or now, if current).
     */
    protected function durationLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $end = $this->currently_working ? now() : ($this->end_date ?? now());
                $diff = $this->start_date->diff($end);

                return trim(sprintf(
                    '%d %s %d %s',
                    $diff->y, Str::plural('Year', $diff->y),
                    $diff->m, Str::plural('Month', $diff->m),
                ));
            },
        );
    }
}
