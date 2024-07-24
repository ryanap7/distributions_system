<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    public function toArray()
    {
        $array = parent::toArray();

        unset($array['slug']);
        unset($array['created_at']);
        unset($array['updated_at']);

        return $array;
    }
}
