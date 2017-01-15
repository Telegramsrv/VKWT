<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;
use VK\VK;

class UserController extends Controller
{
	public function auth( Request $request)
	{
		$data = array(
			'title' => 'VKWT | Главная страница',
			'url'   => $request->cookie('url')
		);
		return view('pages.login',$data);
	}

	public function index(Request $request)
	{

	}

	public function getUser($id)
	{
		dd(Users::find($id)->qtyLikes());
	}
}
