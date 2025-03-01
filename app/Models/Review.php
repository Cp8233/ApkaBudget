<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    protected $fillable = ['reviewer_id','reviewee_id','rating','review'];

    // Reviewer ka relation (User who wrote the review)
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Reviewee ka relation (User who received the review)
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

}
