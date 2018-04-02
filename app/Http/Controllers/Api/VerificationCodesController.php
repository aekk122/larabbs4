<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\VerificationCodeRequest;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request, EasySms $easysms) {
    	$phone = $request->phone;

    	if (!app()->environment('production')) {
    		$code = '1234';
    	} else {
    		// 生成随机四位数，左侧补 0
	    	$code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

	    	try {
	    		$result = $easysms->send($phone, [
	    			'content' => "【Lbbs社区】您的验证码是{$code}。如非本人操作，请忽略本短信",
	    		]);
	    	} catch (\GuzzleHttp\Exception\ClientException $exception) {
	    		$response = $exception->getResponse();
	    		$result = json_decode($response->getBody()->getContents(), true);
	    		return $this->response->errorInternal($result['msg'] ?? '短信发送异常');
	    	}
    	}

    	$key = 'verificationCode_' . str_random(15);

    	$expireAt = now()->addMinutes(10);
    	// 缓存验证码，10 分钟过期
    	\Cache::put($key, ['phone' => $phone, 'code' => $code], $expireAt);

    	return $this->response->array([
    		'key' => $key,
    		'expired_at' => $expireAt->toDateTimeString(),
    	])->setStatusCode(201);
    }
}
