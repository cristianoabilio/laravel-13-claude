<?php

namespace App\Models;

use App\Models\Concerns\ResolvesStorageUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['heading_prefix', 'heading_highlight', 'heading_suffix', 'image'])]
class HomeBanner extends Model
{
    /** @use HasFactory<\Database\Factories\HomeBannerFactory> */
    use HasFactory, ResolvesStorageUrl;

    /**
     * The publicly resolvable URL for the banner image, whether it's a
     * bundled template asset (public/backend/...) or an upload on the S3/MinIO disk.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->image),
        );
    }
}
