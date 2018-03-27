<?php

namespace App\Models\Traits;

use App\Models\Topic;
use App\Models\Reply;

use Carbon\Carbon;
use DB;
use Cache;

trait ActiveUserHelper {
	// 用于存放临时用户数组
	protected $users = [];

	// 配置信息
	protected $topic_weight = 4; // 话题权重
	protected $reply_weight = 1; // 回复权重
	protected $pass_days = 7; // 多少天内发表过内容
	protected $user_number = 6; // 取出多少用户

	// 缓存相关配置
	protected $cache_key = 'larabbs_active_users';
	protected $cache_expired_in_minutes = 65;

	public function getActiveUsers() {
		// 从缓存中取出数据，如若没有，则运行匿名函数中的代码计算，返回的同时会进行缓存
		return Cache::remember($this->cache_key, $this->cache_expired_in_minutes, function() {
			return $this->calculateActiveUser();
		});

	}

	public function calculateAndCacheActiveUsers() {
		// 取得活跃用户数据
		$active_users = $this->calculateActiveUser();
		// 缓存
		$this->cacheActiveUsers($active_users);
	}

	private function calculateActiveUser() {
		$this->calculateTopicScore();
		$this->calculateReplyScore();

		// 数组按照得分排序
		$users = array_sort($this->users, function($user) {
			return $user['score'];
		});

		// 倒序,并保留键值
		$users = array_reverse($users, true);

		// 获取规定数量, 保留键值
		$users = array_slice($users, 0, $this->user_number, true);

		// 新建空集合
		$active_users = collect();

		foreach ($users as $user_id => $user) {
			// 找寻下是否可以找到用户
			$user = $this->find($user_id);
			if ($user) {
				// 存在该用户
				$active_users->push($user);
			}
		}
		
		return $active_users;
	}

	private function calculateTopicScore() {
		$topics = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))->groupBy('user_id')->get();

		// 根据话题数量计算得分
		foreach ($topics as $value) {
			$this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
		}
	}

	private function calculateReplyScore() {
		$replies = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))->groupBy('user_id')->get();

		// 根据回复计算得分
		foreach ($replies as $value) {
			$reply_score = $value->reply_count * $this->reply_weight;
			// 判断键值是否存在
			if (isset($this->users[$value->user_id])) {
				$this->users[$value->user_id]['score'] += $reply_score;
			} else {
				$this->users[$value->user_id]['score'] = $reply_score;
			}
		}
	}

	private function cacheActiveUsers($active_users) {
		// 缓存
		Cache::put($this->cache_key, $active_users, $this->cache_expired_in_minutes);
	}
}