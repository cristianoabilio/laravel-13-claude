<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'image'])]
class Speciality extends Model
{
    /** @use HasFactory<\Database\Factories\SpecialityFactory> */
    use HasFactory;

    /**
     * The publicly resolvable URL for the speciality image, whether it's a
     * bundled template asset (public/admin/...) or an upload on the S3/MinIO disk.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => match (true) {
                blank($this->image) => null,
                Str::startsWith($this->image, ['http://', 'https://', 'admin/']) => asset($this->image),
                default => Storage::disk('s3')->url($this->image),
            },
        );
    }
}
