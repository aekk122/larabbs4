<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body',  'excerpt'];

    public function belongsToCategory() {
    	return $this->belongsTo(Category::class, 'category_id');
    }

    public function belongsToUser() {
    	return $this->belongsTo(User::class, 'user_id');
    }
}
