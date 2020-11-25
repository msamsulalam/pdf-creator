<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    //
    public function packages(){
        return $this->belongsToMany(Packages::class, 'package_details');
    }
//    public function order_detail_products(){
//        return $this->belongsToMany( Orders::class, 'order_details');
//    }


    public function total(){
        return $this->hasMany(OrderDetail::class,'product_id');
    }
    public function getTotalOrderedAttribute(){

        return $this->total ? $this->total->sum('qty') : 0;

    }
}
