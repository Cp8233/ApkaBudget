<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    
    protected $table = 'orders';
    protected $fillable = ['user_id', 'subcategory_id','address_id','total_price','payment_method','status','booking_id','transaction_id','slot_start_time','slot_end_time'];

    // Relation with User Model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id', 'id');
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }
}
