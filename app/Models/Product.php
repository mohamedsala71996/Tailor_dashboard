<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use App\Models\Size;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];


    ###### Start  Relations ########

    public function Sizes() {
        return $this->belongsToMany(Size::class,'product_sizes' ,'product_id','size_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    ###### End Relations ########


    protected static function booted()
    {
        static::addGlobalScope(new StoreScope);

        // Automatically set store_id when creating a new record
        static::creating(function ($model) {
            $model->store_id = auth()->user()->store_id;
        });

        // Automatically set store_id when updating a record
        static::updating(function ($model) {
            $model->store_id = auth()->user()->store_id;
        });
    }
}

