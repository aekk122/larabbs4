<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class User extends Authenticatable
{
    use Notifiable {
        notify as protected laravelNotify;
    }

    public function notify($instance) {
        // 如果要通知的是当前用户
        if (Auth::id() === $this->id) {
            return;
        }

        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    public function markAsRead() {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function hasManyTopics() {
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model) {
        return $this->id === $model->user_id;
    }

    public function hasManyReplies() {
        return $this->hasMany(Reply::class, 'user_id');
    }
}
