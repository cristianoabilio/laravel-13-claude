<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['appointment_id', 'doctor_service_id', 'service_name', 'price', 'duration_minutes'])]
class AppointmentService extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentServiceFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctorService(): BelongsTo
    {
        return $this->belongsTo(DoctorService::class);
    }
}
