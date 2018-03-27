<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Link extends Model
{
    //
    protected $fillable = ['title', 'link'];

    public $cache_key = 'larabbs_links';
    protected $cache_expired_in_minutes = 1440;

    public function getAllCache() {
    	// 尝试从缓存中取出 cache_key 对应的数据，如无，则运行匿名函数获得，并缓存
    	return Cache::remember($this->cache_key, $this->cache_expired_in_minutes, function() {
    		return $this->all();
    	});
    }
}
