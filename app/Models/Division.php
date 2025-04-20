<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Division extends Model
{
    use SoftDeletes;

    public function users(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
