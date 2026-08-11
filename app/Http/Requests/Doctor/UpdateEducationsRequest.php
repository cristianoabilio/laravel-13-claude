<?php

namespace App\Http\Requests\Doctor;

use App\Models\Education;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEducationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Drop stub rows added via "Add New Education" that were never filled in,
     * and make sure every row belongs to the authenticated doctor.
     */
    protected function prepareForValidation(): void
    {
        // Keys are intentionally preserved (no values()/reindex): they're how the
        // controller correlates each row back to its uploaded logo file.
        $educations = collect($this->input('educations', []))
            ->filter(fn (array $education) => $this->isFilled($education))
            ->all();

        $this->merge(['educations' => $educations]);
    }

    /**
     * @param  array<string, mixed>  $education
     */
    protected function isFilled(array $education): bool
    {
        $fields = ['institution', 'course', 'no_of_years', 'description', 'start_date', 'end_date'];

        foreach ($fields as $field) {
            if (filled($education[$field] ?? null)) {
                return true;
            }
        }

        return filled($education['id'] ?? null);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'educations' => ['array'],
            'educations.*.id' => [
                'nullable',
                Rule::exists(Education::class, 'id')->where('doctor_id', $this->user()->id),
            ],
            'educations.*.logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
            'educations.*.institution' => ['nullable', 'string', 'max:255'],
            'educations.*.course' => ['nullable', 'string', 'max:255'],
            'educations.*.start_date' => ['required', 'date_format:d/m/Y'],
            'educations.*.end_date' => ['required', 'date_format:d/m/Y', 'after_or_equal:educations.*.start_date'],
            'educations.*.no_of_years' => ['required', 'digits_between:1,2'],
            'educations.*.description' => ['required', 'string'],
        ];
    }
}
