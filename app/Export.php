<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Export extends Model
{
    protected $guarded = [];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
