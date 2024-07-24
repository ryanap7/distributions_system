<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Village extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function toArray()
    {
        $array = parent::toArray();

        unset($array['slug']);
        unset($array['district_id']);
        unset($array['created_at']);
        unset($array['updated_at']);

        return $array;
    }
}
