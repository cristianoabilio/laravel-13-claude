<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'appointment_number', 'patient_id', 'doctor_id', 'clinic_id', 'doctor_service_id',
    'service_name', 'duration_minutes', 'appointment_type', 'appointment_date',
    'start_time', 'end_time', 'home_visit_address', 'first_name', 'last_name',
    'phone', 'email', 'symptoms', 'reason_for_visit', 'notes', 'consultation_fee',
    'booking_fee', 'tax', 'discount', 'total_amount', 'status', 'payment_status',
])]
class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'appointment_type' => AppointmentType::class,
            'status' => AppointmentStatus::class,
            'payment_status' => PaymentStatus::class,
            'appointment_date' => 'date',
            'start_time' => 'datetime:H:i:s',
            'end_time' => 'datetime:H:i:s',
            'consultation_fee' => 'decimal:2',
            'booking_fee' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function doctorService(): BelongsTo
    {
        return $this->belongsTo(DoctorService::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * @return HasMany<AppointmentDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(AppointmentDocument::class);
    }

    /**
     * Every service line item booked as part of this appointment (a patient
     * may book more than one service from the same doctor in one visit).
     *
     * @return HasMany<AppointmentService, $this>
     */
    public function services(): HasMany
    {
        return $this->hasMany(AppointmentService::class);
    }
}
