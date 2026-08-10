<?php

namespace App\Http\Requests\Doctor\Concerns;

trait NormalizesKnownLanguages
{
    /**
     * Turn the comma-separated known languages tag input into an array.
     *
     * The tagsinput field submits an empty string when cleared, which the
     * ConvertEmptyStringsToNull middleware turns into null before this runs.
     */
    protected function normalizeKnownLanguages(): void
    {
        if (! $this->has('known_languages')) {
            return;
        }

        $languages = $this->known_languages;

        if (is_string($languages)) {
            $languages = array_filter(array_map('trim', explode(',', $languages)));
        }

        $this->merge(['known_languages' => array_values($languages ?? [])]);
    }
}
