<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    protected $fillable = ['otp_code', 'token', 'used', 'user_id', 'phone_number'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
