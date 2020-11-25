<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    //
    public function orders(){
        return $this->hasMany(Orders::class, 'business_id');
    }

    public function auto_setup_packages(){
        return $this->hasMany(SetupAutoOrders::class,'business_id');
    }
}
