<?php

namespace App\Http\Requests\Frontend;

use App\Enums\AppointmentType;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Only authenticated patients may reach this far - the "patient" role
     * middleware already blocks doctors/admins/guests before this runs, this
     * is just a defensive second check.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'patient';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $doctorId = (int) $this->route('doctorId');

        return [
            'doctor_service_ids' => ['required', 'array', 'min:1'],
            'doctor_service_ids.*' => [
                Rule::exists('doctor_services', 'id')->where('doctor_id', $doctorId),
            ],
            'appointment_type' => ['required', new Enum(AppointmentType::class)],
            'clinic_id' => [
                Rule::requiredIf($this->input('appointment_type') === AppointmentType::Clinic->value),
                'nullable',
                Rule::exists('clinics', 'id')->where('doctor_id', $doctorId),
            ],
            'home_visit_address' => [
                Rule::requiredIf($this->input('appointment_type') === AppointmentType::HomeVisit->value),
                'nullable', 'string', 'max:255',
            ],
            'appointment_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],

            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'symptoms' => ['nullable', 'string', 'max:255'],
            'reason_for_visit' => ['nullable', 'string', 'max:2000'],
            'documents' => ['nullable', 'array', 'max:5'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],

            'card_holder_name' => ['required', 'string', 'max:255'],
            'card_number' => ['required', 'string', 'regex:/^[\d\s]{13,19}$/'],
            'card_expiry' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'card_cvv' => ['required', 'digits_between:3,4'],
        ];
    }

    /**
     * Confirm the card hasn't already expired - can't express this with a
     * single built-in rule since the value is "MM/YY", not a real date.
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $expiry = $this->input('card_expiry');

            if (! $expiry || ! preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiry, $matches)) {
                return;
            }

            $expiresAt = Carbon::createFromDate(2000 + (int) $matches[2], (int) $matches[1], 1)->endOfMonth();

            if ($expiresAt->isPast()) {
                $validator->errors()->add('card_expiry', 'This card has expired.');
            }
        });
    }
}
