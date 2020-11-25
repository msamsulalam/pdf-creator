<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageDetail extends Model
{


    public function product(){
        return $this->belongsTo(Products::class,'products_id');
    }
}
