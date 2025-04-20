<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purpose extends Model
{
    use SoftDeletes;

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }
}
