<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use App\Traits\helper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use helper;

    protected $table = 'users';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    //relations
    public function Image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function Activity_logs()
    {
        return $this->morphMany(Activity_log::class, 'causer');
    }

    //
    public function getImage(){
        if($this->Image != null){
            return url('uploads/users/' . $this->Image->src);
        } else {
            return url('uploads/users/default.jpg');
        }
    }

    public function getRole(){
        if(count($this->roles) > 0){
            return $this->roles[0]->name;
        } else {
            return null;
        }
    }

    public function getRoleId(){
        if(count($this->roles) > 0){
            return $this->roles[0]->id;
        } else {
            return null;
        }
    }

    public function getCreatedAtAttribute(){
        return $this->date_format($this->attributes['created_at']);
    }

    public function has_permission($permission){
        if($this->super == 1)
            return true;

        if($this->isAbleTo($permission))
            return true;

        return false;
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'type'       => 'user_api'
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function stores()
    {
        // Check if the user is admin or supervisor
        if ($this->role == 'admin' || $this->role == 'supervisor') {
            return Store::all(); // Admin or supervisor can access all stores
        }
    
        // Otherwise, return only the stores assigned to the user
        return $this->belongsToMany(Store::class);
    }
    
    protected static function booted()
    {
        // static::addGlobalScope(new StoreScope);

        // Automatically set store_id when creating a new record
        // static::creating(function ($model) {
        //     $model->store_id = auth()->user()->store_id;
        // });

        // // Automatically set store_id when updating a record
        // static::updating(function ($model) {
        //     $model->store_id = auth()->user()->store_id;
        // });
    }
}
