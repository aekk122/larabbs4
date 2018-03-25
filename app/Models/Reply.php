<?php

namespace App\Models;

class Reply extends Model
{
    protected $fillable = ['content'];

    public function belongsToTopic() {
    	return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function belongsToUser() {
    	return $this->belongsTo(User::class, 'user_id');
    }
}
