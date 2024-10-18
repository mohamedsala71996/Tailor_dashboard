<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user (tailor) who created the batch.
     */
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
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
