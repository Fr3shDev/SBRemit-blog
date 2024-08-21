<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }
}
