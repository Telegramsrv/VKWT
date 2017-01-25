<?php

namespace App\Http\Controllers;

use App\Token;
use App\Users;

use gchart\gLineChart;
use gchart\gPieChart;
use Illuminate\Http\Request;
use VK\VK;

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
		return redirect('/id'.$User->user_id);
	}

	public function getUser($id)
	{
		$User = Users::where('user_id',$id)->first();
	//		$FriendStats = $User->friends()->get()->map(function ($item){return $item->user()->statistics();});
		$data = array(
			'title' => 'Пользователь '.$User->last_name.' '.$User->first_name,
			'Owner' => $User,
			'TopWall' => $User->getTopRatedPost()->getFullWallPost(),
			'FirstWall' => $User->getFirstPost()->getFullWallPost(),
			'Statistics' => $User->statistics()
		);
		return view('pages.user',$data);
	}

	public function getFriend(Request $request)
	{
		$User = Token::where('token',$request->cookie('token'))->first()->user()->first();

		$FriendStats = $User->friends()->get()->map(function ($item){ return array_merge($item->user()->statistics(),$item->user()->toArray());});

		if (!$User){
			$User = new Users;
			$User->user_id = Token::where('token',$request->cookie('token'))->first()->user_id;
			$User->save();
		}
		if ($User){
			$data = array(
				'title' => 'Друзья пользователя :'.$User->last_name.' '.$User->first_name,
				'Owner' => $User,
				'Statistics' => $User->statistics(),
				'FriendStats' => $FriendStats
			);
			return view('pages.friends',$data);
		}
		return view('pages.wait');//delete
	}

	public function getUploadProcess()//just for ajax
	{
		$process = Users::where('status', 'done')->get()->count() / Users::all()->count();
		echo intval($process*100);
	}



	public function test(Request $request)
	{
		$VKConfig = App('service.vkconfig');
		$vk = new VK( $VKConfig->get_config('app_id'), $VKConfig->get_config('api_secret'), $request->cookie('token'));


		$id = 15684756;
		while(true) {
			$code = 'return {"returned": [';
			for ($i = $k; $i < 25 + $k; $i++) {
				$code .= 'API.wall.get({"owner_id": "' . $id . '","count": "100","offset": "' . ($i * 100) . '"}),';
			}
			$code .= ']};';

			$resp = $vk->api('execute', ['code' => $code]);
		}
		dd($resp);//TODO
	}
}
