<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description', 'sort_order'])]
class HomeBookUsFaq extends Model
{
    /** @use HasFactory<\Database\Factories\HomeBookUsFaqFactory> */
    use HasFactory;
}
