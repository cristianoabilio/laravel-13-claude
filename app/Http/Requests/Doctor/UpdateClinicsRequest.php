<?php

namespace App\Http\Requests\Doctor;

use App\Models\Clinic;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClinicsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Drop stub rows added via "Add New Clinic" that were never filled in,
     * and make sure every row belongs to the authenticated doctor.
     */
    protected function prepareForValidation(): void
    {
        // Keys are intentionally preserved (no values()/reindex): they're how the
        // controller correlates each row back to its uploaded logo/gallery files.
        $clinics = collect($this->input('clinics', []))
            ->filter(fn (array $clinic) => $this->isFilled($clinic))
            ->all();

        $this->merge(['clinics' => $clinics]);
    }

    /**
     * @param  array<string, mixed>  $clinic
     */
    protected function isFilled(array $clinic): bool
    {
        $fields = ['name', 'location', 'address'];

        foreach ($fields as $field) {
            if (filled($clinic[$field] ?? null)) {
                return true;
            }
        }

        return filled($clinic['id'] ?? null);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'clinics' => ['array'],
            'clinics.*.id' => [
                'nullable',
                Rule::exists(Clinic::class, 'id')->where('doctor_id', $this->user()->id),
            ],
            'clinics.*.logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'clinics.*.name' => ['required', 'string', 'max:255'],
            'clinics.*.location' => ['required', 'string', 'max:255'],
            'clinics.*.address' => ['required', 'string', 'max:255'],
            'clinics.*.gallery' => ['nullable', 'array', 'max:10'],
            'clinics.*.gallery.*' => ['image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ];
    }
}
