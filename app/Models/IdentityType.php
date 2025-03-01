<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityType extends Model
{
    use HasFactory;

    protected $table = 'identity_types';
    protected $fillable = ['identity', 'status'];
}
