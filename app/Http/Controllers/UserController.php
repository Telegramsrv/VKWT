<?php

namespace App\Http\Controllers;

use App\Token;
use App\Users;

use Illuminate\Http\Request;

class UserController extends Controller
{
	private $_serviceVkConfig;

	public function __construct()
	{
		$this->_serviceVkConfig = App('service.vkconfig');
	}

	public function auth( Request $request)
	{
		$data = array(
			'title' => 'VKWT | Главная страница',
			'url'   => $this->_serviceVkConfig->getAuthorizeURL()
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

//		$FriendStats = $User->friends()->get()->map(function ($item){return $item->user()->statistics();});
		$data = array(
			'title' => 'Пользователь '.$User->last_name.' '.$User->first_name,
			'Owner' => $User
		);
		return view('pages.user',$data);
	}
}
