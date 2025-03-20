<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $table = 'zones';
    protected $fillable = ['name', 'boundary', 'center_lat', 'center_lng', 'perimeter', 'area', 'areas'];

    public function providers()
    {
        return $this->belongsToMany(User::class, 'zone_provider', 'zone_id', 'user_id');
    }
}
