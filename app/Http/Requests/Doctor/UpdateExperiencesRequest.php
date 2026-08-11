<?php

namespace App\Http\Requests\Doctor;

use App\Enums\EmploymentType;
use App\Models\Experience;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExperiencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Drop stub rows added via "Add New Experience" that were never filled in,
     * and make sure every row belongs to the authenticated doctor.
     */
    protected function prepareForValidation(): void
    {
        // Keys are intentionally preserved (no values()/reindex): they're how the
        // controller correlates each row back to its uploaded hospital_logo file.
        $experiences = collect($this->input('experiences', []))
            ->filter(fn (array $experience) => $this->isFilled($experience))
            ->all();

        $this->merge(['experiences' => $experiences]);
    }

    /**
     * @param  array<string, mixed>  $experience
     */
    protected function isFilled(array $experience): bool
    {
        $fields = ['hospital', 'years_of_experience', 'location', 'job_description', 'start_date', 'end_date'];

        foreach ($fields as $field) {
            if (filled($experience[$field] ?? null)) {
                return true;
            }
        }

        return filled($experience['id'] ?? null);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'experiences' => ['array'],
            'experiences.*.id' => [
                'nullable',
                Rule::exists(Experience::class, 'id')->where('doctor_id', $this->user()->id),
            ],
            'experiences.*.hospital_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'experiences.*.title' => ['nullable', 'string', 'max:255'],
            'experiences.*.hospital' => ['required', 'string', 'max:255'],
            'experiences.*.years_of_experience' => ['required', 'digits_between:1,2'],
            'experiences.*.location' => ['required', 'string', 'max:255'],
            'experiences.*.employment_type' => ['nullable', Rule::enum(EmploymentType::class)],
            'experiences.*.job_description' => ['required', 'string'],
            'experiences.*.start_date' => ['required', 'date_format:d/m/Y'],
            'experiences.*.end_date' => ['nullable', 'date_format:d/m/Y', 'required_unless:experiences.*.currently_working,1'],
            'experiences.*.currently_working' => ['nullable', 'boolean'],
        ];
    }
}
