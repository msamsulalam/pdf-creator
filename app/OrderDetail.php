<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    //
    public function product(){
        return $this->belongsTo(Products::class,'product_id');
    }

    public function package(){
        return $this->belongsTo(Packages::class,'package_id');
    }
}
