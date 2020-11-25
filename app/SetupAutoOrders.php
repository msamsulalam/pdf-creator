<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SetupAutoOrders extends Model
{
    protected $fillable = ['package_id','business_id','user_id','qty'];
    //
    public function packages(){
        return $this->belongsToMany(Packages::class, 'package_id');
    }


}
