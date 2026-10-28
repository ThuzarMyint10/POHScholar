<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationNumberSeq extends Model
{
    protected $table = 'application_number_seq';

    public $timestamps = false;

    protected $guarded = ['id'];
}
