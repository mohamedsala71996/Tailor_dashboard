<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $guarded = [];



    ##### Relations ######
    public function products()
    {
        return $this->belongsToMany(Product::class,'product_sizes','size_id','product_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    ##### Relations ######

}
