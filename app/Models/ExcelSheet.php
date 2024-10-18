<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelSheet extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function masterOrders()
    {
        return $this->hasMany(MasterOrder::class);
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
