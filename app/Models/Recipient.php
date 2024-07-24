<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Znck\Eloquent\Relations\BelongsToThrough;

class Recipient extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = ['id'];

    function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    function district(): BelongsToThrough
    {
        return $this->belongsToThrough(District::class, Village::class);
    }

    function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }
}
