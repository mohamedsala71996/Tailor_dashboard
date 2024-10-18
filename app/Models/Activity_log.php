<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use App\Traits\helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity_log extends Model
{
    use HasFactory;
    use helper;
    protected $table = 'activity_log';

    public $guarded = [];

    protected $casts = [
        'id'            => 'integer',
        'properties'    => 'array',
    ];

    //relations
    public function subject()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->morphTo();
    }

    public function getCreatedAtAttribute(){
        return $this->date_format($this->attributes['created_at']);
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
