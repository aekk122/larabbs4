<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Category;
use App\Models\User;
use App\Models\Link;

class CategoriesController extends Controller
{
    //
    public function show(Request $request, Category $category, Topic $topic, User $user, Link $link) {
    	$topics = $topic->withOrder($request->order)->where('category_id', $category->id)->paginate(20);
    	$links = $link->getAllCache();
    	$active_users = $user->getActiveUsers();
    	return view('topics.index', compact('topics', 'category', 'active_users', 'links'));
    }
}
