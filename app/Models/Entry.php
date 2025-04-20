<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entry extends Model
{
    use SoftDeletes;

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function purposes(): BelongsTo
    {
        return $this->belongsTo(Purpose::class, 'purpose_id');
    }
}
