<?php

namespace App\Handlers;
use Image;

class ImageUploadHandler {

	// 只允许以下后缀名
	protected $allowed_ext = ['png', 'jpeg', 'gif', 'jpg'];

	public function save($file, $folder, $file_prefix, $max_width = false) {

		// 构建存储的文件夹规则，如：uploads/images/avatars/201212/10
		$folder_name = "uploads/images/$folder/" . date("Ym/d/", time());

		// 文件具体的存储路径，如：/home/vagrant/Code/public/uploads/images/avatars/201212/10
		$upload_path = public_path() . '/' . $folder_name;

		// 获取文件的后缀名
		$extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

		// 拼接文件名，并加上前缀，增加辨析度
		// 值如：1_141295_sadjkflasg.png
		$filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

		// 判断上传文件后缀是否合法
		if ( !in_array($extension, $this->allowed_ext)) {
			return false;
		}

		// 保存图片到指定目录
		$file->move($upload_path, $filename);

		// 如果限制了图片宽度，则进行裁剪
		if ($max_width) {
			// 此类中封装的函数，用于裁剪图片
			$this->reduceSize($upload_path . '/' . $filename, $max_width);
		}

		// 返回文件路径
		return [
			'path' => config('app.url') . "/$folder_name$filename",
		];
	}

	public function reduceSize($path, $max_width) {

		// 实例化，传参为图片物理路径
		$image = Image::make($path);

		// 进行大小调整，宽度为 $max_width, 高度等比例缩放
		$image->resize($max_width, null, function ($constraint) {

			// 设定宽度是 $max_width，高度等比例缩放
			$constraint->aspectRatio();

			// 防止图片裁剪时变大
			$constraint->upsize();
		});

		// 保存
		$image->save();
	}
}