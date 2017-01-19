<?php

namespace App\Http\Controllers;

use App\Token;
use App\Users;

use Illuminate\Http\Request;

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
		$User = Token::where('token',$request->cookie('token'))->first()->user()->first();
		if (!$User){
			$User = new Users;
			$User->user_id = Token::where('token',$request->cookie('token'))->first()->user_id;
			$User->save();
		}
		if (!$User->uploaded){
			dd($User);//msg waiting for cron download information
			return view('pages.wait');
		}
		return redirect('/id'.$User->user_id);
	}

	public function getUser($id)
	{
		$User = Users::where('user_id',$id)->first();
		dd($User->friends()->get()->toArray());
	}
}
