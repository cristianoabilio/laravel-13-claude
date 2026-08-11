<?php

namespace App\Http\Requests\Doctor;

use App\Enums\DayOfWeek;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessHoursRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'business_hours' => ['required', 'array'],
        ];

        foreach (DayOfWeek::cases() as $day) {
            $key = $day->value;

            $rules["business_hours.{$key}.is_open"] = ['nullable', 'boolean'];
            $rules["business_hours.{$key}.from"] = [
                'nullable', 'date_format:h:i A', "required_if:business_hours.{$key}.is_open,1",
            ];
            $rules["business_hours.{$key}.to"] = [
                'nullable', 'date_format:h:i A', "required_if:business_hours.{$key}.is_open,1", "after:business_hours.{$key}.from",
            ];
        }

        return $rules;
    }
}
