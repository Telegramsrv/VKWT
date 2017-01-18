<?php

namespace App\Http\Controllers;

use VK\VK;
use App\Token;
use App\Users;
use App\Walls;
use App\Friends;
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


	protected $vk_config = array(
		'app_id'        => '5809395',
		'api_secret'    => 'uhK1NhUTKDEXbwk9v0ZS'
	);

	public function cron()
	{
//		$this->cron_walls();

		$NewUsers = Users::where('uploaded',0)->orWhere('updated_at','<',date('Y-m-d H:m:s',time() - 24*60*60))->get();
		foreach ( $NewUsers as $User )
		{
			$UserToken = $User->token()->first()->token;
			$vk = new VK( $this->vk_config['app_id'], $this->vk_config['api_secret'], $UserToken);
			while (true) {
				$userinfo = $vk->api(
					'users.get',
					array(
						'fields' => 'uid,first_name,last_name,photo_100',
					)
				);

				if (isset($userinfo['response'][0])) {
					$userinfo = $userinfo['response'][0];
					$User->first_name = $userinfo['first_name'];
					$User->last_name = $userinfo['last_name'];
					$User->photo = $userinfo['photo_100'];
					$User->uploaded = true;
					$User->save();
					break;
				}
				if ($userinfo['error']['error_code'] == 5){
					break;
				}
			}

			while (!isset($user_friends['response'])) {
				$user_friends = $vk->api(
					'friends.get',
					array(
						'fields' => 'uid,first_name,last_name,photo_100',
						'order'  => 'name'
					)
				);
			}
			$user_friends = $user_friends['response'];
			foreach ( $user_friends as $friend)
			{
				$NewFrind = Friends::where('user_id', $User->user_id)->where('friend_id',$friend['uid'])->first();
				if (!$NewFrind){
					$NewFrind = new Friends;
				}
				$NewFrind->user_id = $User->user_id;
				$NewFrind->friend_id = $friend['uid'];
				$NewFrind->save();
			}
		}
	}

	public function cron_walls()
	{
		$UserList = Users::all();
		foreach ($UserList as $User)
		{
			$UserToken = $User->token()->first()->token;
			$vk = new VK( $this->vk_config['app_id'], $this->vk_config['api_secret'], $UserToken);

			$error = false;
			$limit = 100;
			$offset = 0;
			$user_walls = [];
			do {
				while(true) {
					$walls_tmp = $vk->api(
						'wall.get',
						array(
							'owner_id' => $User->user_id,
							'count'    => $limit,
							'offset'   => $offset,
							'filter'   => 'owner'
						)
					);
					if (isset($walls_tmp['response'])){
						$walls_tmp = $walls_tmp['response'];
						unset($walls_tmp[0]);
						break;
					}
					if ($walls_tmp['error']['error_code'] == 5){
						$error = true;
					}
				}
				if ($error){
					break;
				}
				$count = count($walls_tmp);
				$user_walls = array_merge($user_walls, $walls_tmp);
				$offset += $count;
			}
			while ($count == $limit);

			foreach ( $user_walls as $user_wall)
			{
				$Wall = Walls::where('user_id',$user_wall['to_id'])->where('wall_id',$user_wall['id'])->first();
				if (!$Wall){
					$Wall = new Walls;
				}
				$Wall->user_id = $user_wall['to_id'];
				$Wall->wall_id = $user_wall['id'];
				$Wall->date    = $user_wall['date'];
				$Wall->likes   = $user_wall['likes']['count'];
				$Wall->save();
			}
		}
	}

	public function getUser($id)
	{
		$User = Users::find($id)->first();
		dd($User->friends()->get()->toArray());
	}
}
