<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ResolvesStorageUrl
{
    /**
     * Resolve the publicly accessible URL for a stored path, whether it's a bundled
     * template asset (public/backend/...) or an upload on the S3/MinIO disk.
     */
    protected function resolveStorageUrl(?string $path, string $bundledPrefix = 'backend/'): ?string
    {
        return match (true) {
            blank($path) => null,
            Str::startsWith($path, ['http://', 'https://', $bundledPrefix]) => asset($path),
            default => Storage::disk('s3')->url($path),
        };
    }
}
