<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSubCategory extends Model
{
    use HasFactory;

    
    protected $table = 'sub_subcategories';
    protected $fillable = ['sub_subcategory_id', 'subcategory_id','sub_subcategory_name','image'];
    
     public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id', 'id');
    }
}
