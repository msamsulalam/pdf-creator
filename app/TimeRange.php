<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeRange extends Model
{
    protected $table = 'time_ranges';

    protected $fillable = ['title','from','to','order'];
}
