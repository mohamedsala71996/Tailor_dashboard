<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];


    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }

    // public function user()  // Tailor in this case
    // {
    //     return $this->belongsTo(User::class, 'user_id');  // Foreign key is `user_id`
    // }

    // public function size()
    // {
    //     return $this->belongsTo(Size::class);
    // }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function masterOrder()
    {
        return $this->belongsTo(MasterOrder::class);
    }

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
