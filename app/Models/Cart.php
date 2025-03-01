<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    protected $fillable = ['user_id','subcategory_id','service_id','quantity','unit_price','price'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}
