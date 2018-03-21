<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UsersController extends Controller
{
	public function __construct() {
		$this->middleware('auth', ['expect' => ['show']]);
	}

    public function show(User $user) {
    	return view('users.show', compact('user'));
    }

    public function edit(User $user) {
    	return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user) {
    	$user->update($request->all());
    	return redirect()->route('users.show', $user->id)->with('success', '更新用户资料成功');
    }
}
