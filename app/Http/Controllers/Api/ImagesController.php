<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use App\Transformers\ImageTransformer;
use App\Handlers\ImageUploadHandler;

class ImagesController extends Controller
{
    public function store(ImageRequest $request, ImageUploadHandler $uploader, Image $image) {
    	// 获得当前登录用户
    	$user = $this->user();

    	// 图片尺寸判断
    	$size = $request->type == 'avatar' ? 362 : 1024;
    	$result = $uploader->save($request->image, str_plural($request->type), $user->id, $size);

    	$image->path = $result['path'];
    	$image->type = $request->type;
    	$image->user_id = $user->id;
    	$image->save();

    	return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }
}
