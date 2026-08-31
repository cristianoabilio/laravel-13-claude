<?php

namespace App\Http\Requests\Doctor;

use App\Http\Requests\Doctor\Concerns\NormalizesKnownLanguages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileRequest extends FormRequest
{
    use NormalizesKnownLanguages;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeKnownLanguages();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            // Nullable to match how the rest of the app already treats these -
            // every other page falls back to the doctor's first/last name when
            // unset, so requiring them here would just block a doctor who
            // hasn't filled them in yet from saving anything else on this form.
            'display_name' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user()->id],
            'known_languages' => ['nullable', 'array'],
            'known_languages.*' => ['string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],

            'memberships' => ['nullable', 'array'],
            'memberships.*.title' => ['required_with:memberships.*.description', 'nullable', 'string', 'max:255'],
            'memberships.*.description' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Friendlier field names for validation messages, so a membership row
     * error reads as "The title field is required..." instead of the raw
     * "memberships.0.title" array key.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'memberships.*.title' => 'title',
            'memberships.*.description' => 'description',
        ];
    }
}
