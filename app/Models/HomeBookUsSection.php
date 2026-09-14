<?php

namespace App\Models;

use App\Models\Concerns\ResolvesStorageUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['badge_text', 'heading_prefix', 'heading_highlight', 'description', 'image_one', 'image_two', 'image_three'])]
class HomeBookUsSection extends Model
{
    /** @use HasFactory<\Database\Factories\HomeBookUsSectionFactory> */
    use HasFactory, ResolvesStorageUrl;

    protected function imageOneUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->image_one),
        );
    }

    protected function imageTwoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->image_two),
        );
    }

    protected function imageThreeUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveStorageUrl($this->image_three),
        );
    }
}
