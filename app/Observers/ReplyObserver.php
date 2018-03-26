<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

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
    	$topic = $reply->belongsToTopic;
    	$topic->increment('reply_count');

    	// 通知作者话题被回复了
    	$topic->belongsToUser->notify(new TopicReplied($reply));
    }
}