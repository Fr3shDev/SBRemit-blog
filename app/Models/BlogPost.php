<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
