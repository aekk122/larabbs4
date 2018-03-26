<?php

namespace App\Observers;

use App\Models\Reply;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        //
    }

    public function updating(Reply $reply)
    {
        //
    }

    public function saving(Reply $reply) {
    	$reply->content = clean($reply->content, 'user_topic_body');
    }

    public function created(Reply $reply) {
    	$reply->belongsToTopic->increment('reply_count');
    }
}