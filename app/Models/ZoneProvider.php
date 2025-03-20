<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneProvider extends Model
{
    use HasFactory;

    protected $table = 'zone_provider';
    protected $fillable = ['zone_id', 'user_id'];
}
