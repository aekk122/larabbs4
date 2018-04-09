<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

    use HasRoles;

    use Traits\ActiveUserHelper;

    use Traits\LastActivedAtHelper;

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
        'name', 'email', 'password', 'introduction', 'avatar', 'phone', 'weixin_openid', 'weixin_unionid','registration_id'
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

    public function setPasswordAttribute($value) {
        // 如果值的长度等于 60，即认为是已经加过密
        if(strlen($value) != 60) {
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($value) {
        // 如果不是 'http' 子串开头，那就是后台上传，需要补全 URL
        if (!starts_with($value, 'http')) {
            // 拼接完整的 URL
            $value = config('app.url') . "/uploads/images/avatars/$value";
        }

        $this->attributes['avatar'] = $value;
    }

    // Reset omitted for brevity
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
}
