<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['icon', 'icon_color', 'title', 'description', 'sort_order'])]
class HomeReason extends Model
{
    /** @use HasFactory<\Database\Factories\HomeReasonFactory> */
    use HasFactory;
}
