<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Znck\Eloquent\Relations\BelongsToThrough;

class Distribution extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    function village(): BelongsToThrough
    {
        return $this->belongsToThrough(Village::class, Recipient::class);
    }
}
