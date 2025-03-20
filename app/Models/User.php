<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'name',
        'email',
        'mobile_no',
        'otp',
        'password',
        'temp_password',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'address',
        'latitude',
        'longitude',
        'token',
        'device_id',
        'device_token',
        'device_type',
        'device_model',
        'ip_address',
        'login_at',
        'logout_at',
        'category_id',
        'experience',
        'identity_id',
        'identity_number',
        'identity_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
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
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function givenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }
    public function transactions()
{
    return $this->hasMany(Transaction::class, 'user_id');
}
  public function zones()
    {
        return $this->belongsToMany(Zone::class, 'zone_provider');
    }

}
