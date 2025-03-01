<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $guard = 'admin';
    protected $fillable = ['role', 'name','email','mobile_no','image','password','temp_password'];
    protected $hidden = ['password', 'remember_token'];
}
