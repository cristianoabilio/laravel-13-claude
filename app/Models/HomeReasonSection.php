<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['badge_text', 'heading'])]
class HomeReasonSection extends Model
{
    /** @use HasFactory<\Database\Factories\HomeReasonSectionFactory> */
    use HasFactory;
}
