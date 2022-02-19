<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    use HasFactory;
    use hasUuid;


    protected $guarded = [];

    protected $casts = [
        'states' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function getDaysToExpireAttribute()
    {
        return now()->diffInDays($this->expires);
    }
}
