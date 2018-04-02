<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Api\UserRequest;

class UsersController extends Controller
{
    //

    public function store(UserRequest $request) {
    	$verifiData = \Cache::get($request->verification_key);

    	if (!$verifiData) {
    		return $this->response->error('验证码已经失效。', 422);
    	} 

    	if (!hash_equals($verifiData['code'], $request->verification_code)) {
    		// 返回 401
    		return $this->response->errorUnauthorized('验证码错误');
    	}

    	$user = User::create([
    		'name' => $request->name,
    		'phone' => $verifiData['phone'],
    		'password' => bcrypt($request->password),
    	]);

    	// 清除验证码缓存
    	\Cache::forget($request->verification_key);

    	return $this->response->created();
    }
}