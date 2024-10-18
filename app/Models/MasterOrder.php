<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function excelSheet()
    {
        return $this->belongsTo(ExcelSheet::class);
    }

    // A MasterOrder belongs to a Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // A MasterOrder belongs to a User (Tailor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A MasterOrder belongs to a Size
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    // A MasterOrder has many Orders
    public function orders()
    {
        return $this->hasMany(Order::class);
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
