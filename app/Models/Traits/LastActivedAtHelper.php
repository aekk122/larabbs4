<?php

namespace App\Models\Traits;

use Redis;
use Carbon\Carbon;

trait LastActivedAtHelper {

	// 缓存相关配置
	protected $hash_prefix = "larabbs_last_actived_at";
	protected $field_prefix = 'user_';

	public function recordLastActivedAt() {
		// 获取今天的日期
		$date = Carbon::now()->toDateString();

		// Redis 哈希表命名
		$hash = $this->hash_prefix . $date;

		// 字段名称
		$field_name = $this->field_prefix . $this->id;

		// 当前时间
		$now = Carbon::now()->toDateTimeString();

		// 数据写入
		Redis::hSet($hash, $field_name, $now);
	} 

	public function syncUserActivedAt() {
		// 获取昨天的日期
		$yesterday = Carbon::yesterday()->toDateString();

		// 哈希表命名
		$hash = $this->hash_prefix . $yesterday;

		// 从 Redis 中获取信息
		$dates = Redis::hGetAll($hash);

		// 遍历，并保存数据库
		foreach ($dates as $user_id => $date) {
			// 将 user_1 转为 1
			$user_id = str_replace($this->field_prefix, '', $user_id);

			// 找寻是否还存在该用户
			if($user = $this->find($user_id)) {
				$user->last_actived_at = $date;
				$user->save();
			}
		}

		// 删除昨日缓存
		Redis::del($hash);
	}

	public function getLastActivedAtAttribute($value) {
		// 获取今日日期
		$today = Carbon::now()->toDateString();

		$hash = $this->hash_prefix . $today;

		$field_name = $this->field_prefix . $this->id;

		// 三元运算符，优先选择 Redis 的数据，否则使用数据库中
		$date = Redis::hGet($hash, $field_name) ?: $value;

		// 判断数据是否存在
		if ( !empty($date)) {
			return new Carbon($date);
		} else {
			// 否则使用用户注册时间
			return $this->created_at;
		}
	}
}