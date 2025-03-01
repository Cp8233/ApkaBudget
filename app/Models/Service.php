<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';
    protected $fillable = ['category_id','subcategory_id','sub_subcategory_id','service_name','image','price','time'];

    public function subSubCategory()
    {
        return $this->belongsTo(SubSubCategory::class, 'sub_subcategory_id', 'id');
    }
}
