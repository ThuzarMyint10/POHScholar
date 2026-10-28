<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    // updated_at only - no created_at column on this table.
    const CREATED_AT = null;

    const UPDATED_AT = 'updated_at';

    protected $guarded = ['id'];
}
