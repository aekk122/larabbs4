<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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
