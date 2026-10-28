<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\District;

class Country extends Model
{
    protected $guarded = ['id'];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class)->orderBy('name');
    }
}
