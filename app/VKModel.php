<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use VK\VK;

class VKModel extends Model
{
	protected $vk_config = array(
		'app_id'        => '5809395',
		'api_secret'    => 'uhK1NhUTKDEXbwk9v0ZS',
		'callback_url'  => 'http://laravel.loc/',
		'api_settings'  => 'wall,friends'
	);

	protected $userinfo = [];

	public function isAuth()
	{
		if (isset($_COOKIE['token']))
		{
			$vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'],$_COOKIE['token']);

			$this->userinfo = $vk->api('users.get', array(
				'fields'    => 'uid,first_name,last_name,photo_100',
			));
			if ( isset($this->userinfo['response'])){
				$this->userinfo = $this->userinfo['response'][0];
				return true;
			}
			else {
				setcookie('token', 0, 0);
				return false;
			}
		}
		else return false;
	}

	private function getValidWalls($user_id = false)
	{
		$update = false;
		$insert =  true;

		$results = DB::select('select * from wall where user_id = ?', array( $user_id ? $user_id : $this->userinfo['uid'] ));

		if ( isset($results[0]))  {
			$insert = false;
			if (( $results[0]->updated_at + ( 24*60*60)) < date('Y-m-d',time())) {
				$walls = [];
				foreach ($results as $wall) {
					$walls[] = array(
						'id'       => $wall->wall_id,
						'owner_id' => $wall->user_id,
						'likes'    => array('count' => $wall->likes),
						'date'     => $wall->date
					);
				}
				return $walls;
			}
			else {
				$update = true;
			}
		}

		if (isset($_COOKIE['token'])) {
			$vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'], $_COOKIE['token']);
			$limit = 100;
			$offset = 0;
			$user_walls = [];
			do {
				$walls_tmp = $vk->api(
					'wall.get',
					array(
						'owner_id' => $user_id ? $user_id : '0',
						'count'    => $limit,
						'offset'   => $offset,
						'filter'   => 'owner'
					)
				);
				$walls_tmp = $walls_tmp['response'];
				unset($walls_tmp[0]);
				$count = count($walls_tmp);
				$user_walls = array_merge($user_walls, $walls_tmp);
				$offset += $count;
				usleep(300000);
			}
			while ($count == $limit);

			if ($insert){
				foreach ($user_walls as $user_wall)
				{
					$values = ['user_id' => $user_wall['to_id'],
						'wall_id' => $user_wall['id'],
						'likes'   => $user_wall['likes']['count'],
						'date'    => $user_wall['date'],
						'created_at' => date('Y-m-d H:i:s',time()),
						'updated_at' => date('Y-m-d H:i:s',time())
					];
					DB::table('wall')->insert($values);
				}
			}

			if ($update){
				foreach ($user_walls as $user_wall)
				{
					if(!DB::table('wall')->where( 'user_id', $user_wall['to_id'])->where( 'wall_id', $user_wall['id'])->update(['likes' => $user_wall['likes']['count'], 'updated_at' => date('Y-m-d H:i:s',time())]));
					{
						$values = ['user_id' => $user_wall['to_id'],
						           'wall_id' => $user_wall['id'],
						           'likes'   => $user_wall['likes']['count'],
						           'date'    => $user_wall['date'],
						           'created_at' => date('Y-m-d H:i:s',time()),
						           'updated_at' => date('Y-m-d H:i:s',time())
						];
						DB::table('wall')->insert($values);
					}
				}
			}

			return $user_walls;
		}
		else {
			header("Location: " . $this->vk_config['callback_url']);
		}
	}

	private function getWallById( $user_id, $wall_id)
	{
		$post = $user_id.'_'.$wall_id;
		$vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'],$_COOKIE['token']);

		$wall = $vk->api('wall.getById', array(
			'posts'    => $post,
		));
		return $wall['response'][0];
	}

	public function getAuth($location = false)
	{
		try {
			if (isset($_COOKIE['token']))
			{
				$vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'],$_COOKIE['token']);

				$this->userinfo = $vk->api('users.get', array(
					'fields'    => 'uid,first_name,last_name,photo_100',
				));
				if ( isset($this->userinfo['response'])){
					$this->userinfo = $this->userinfo['response'][0];
					$redirect = $location ? $location : $this->userinfo['uid'];
					header("Location: {$this->vk_config['callback_url']}{$redirect}");//Auth true
					die();
				}
				else {
					setcookie('token', 0, 0);
					header("Location: {$this->vk_config['callback_url']}");//Auth false
					die();
				}
			}
			else {
				$vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret']);

				if (!isset($_REQUEST['code'])) {
					$authorize_url = $vk->getAuthorizeURL(
						$this->vk_config['api_settings'], $this->vk_config['callback_url']);
					return $authorize_url;
				} else {
					$access_token = $vk->getAccessToken($_REQUEST['code'], $this->vk_config['callback_url']);
					setcookie('token',$access_token['access_token'],time()+$access_token['expires_in']);
					$redirect = $location ? $location : $this->userinfo['uid'];
					header("Location: {$this->vk_config['callback_url']}{$redirect}");//Auth true
					die();
				}
			}
		} catch (VKException $error) {
			echo $error->getMessage();
		}
	}

	public function __construct()
    {
//	    try {
//		    if (isset($_COOKIE['token']))
//		    {
//			    $vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'],$_COOKIE['token']);
//
//			    $this->userinfo = $vk->api('users.get', array(
//				    'fields'    => 'uid,first_name,last_name,photo_100',
//			    ))['response'][0];
//		    }
//		    else {
//			    $vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret']);
//
//			    if (!isset($_REQUEST['code'])) {
//				    $authorize_url = $vk->getAuthorizeURL(
//					    $this->vk_config['api_settings'], $this->vk_config['callback_url']);
//				    echo '<a href="' . $authorize_url . '">Sign in with VK</a>';
//				    die();
//			    } else {
//				    $access_token = $vk->getAccessToken($_REQUEST['code'], $this->vk_config['callback_url']);
//				    setcookie('token',$access_token['access_token'],time()+$access_token['expires_in']);
//				    header("Location: ".$this->vk_config['callback_url']);
//			    }
//		    }
//	    } catch (VKException $error) {
//		    echo $error->getMessage();
//	    }
    }

    public function getIntro($user_id = false)
    {
	    $user_walls = $this->getValidWalls($user_id);

	    //WALLS COUNT,likes
	    $likesCount = 0;
	    foreach ( $user_walls as $user_wall)
	    {
		    $likesCount += $user_wall['likes']['count'];
	    }

	    //USER INFO
	    $vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'],$_COOKIE['token']);

		$user = $vk->api('users.get', array('user_ids' => $user_id ? $user_id : '', 'fields' => 'uid,first_name,last_name,photo_100'));

	    $user = $user['response'][0];
		$user['likescount'] = $likesCount;
		$user['wallcount'] = count($user_walls);

	    return $user;
	}


    public function getTopRatedWall($user_id = false)
    {
	    $user_walls = $this->getValidWalls($user_id);
		$maxLikes = 0;
		$topRatedWall = [];
	    foreach ( $user_walls as $user_wall) {
		    if ($maxLikes < $user_wall['likes']['count']) {
			    $maxLikes = $user_wall['likes']['count'];
			    $topRatedWall = $user_wall;
		    }
	    }
	    return $this->getWallById($topRatedWall['from_id'],$topRatedWall['id']);
    }

    public function getFirstWall($user_id = false)
    {
	    $user_walls = $this->getValidWalls($user_id);

	    $minWall = $user_walls[0];

	    foreach ( $user_walls as $user_wall)
	    {
	    	if ( $minWall['date'] > $user_wall['date'])
		    {
		    	$minWall = $user_wall;
		    }
	    }
	    return $this->getWallById($minWall['from_id'],$minWall['id']);
    }

    public function getWallStats($user_id = false)
    {
	    $user_walls = $this->getValidWalls($user_id);
	    $stats = [];
	    foreach ( $user_walls as $user_wall)
	    {
			$user_wall['date'] = date( 'Y-m-d', $user_wall['date']);
			if (array_key_exists($user_wall['date'],$stats)){
				$stats[$user_wall['date']] += $user_wall['likes']['count'];
			}
			else {
				$stats[$user_wall['date']] = $user_wall['likes']['count'];
			}
	    }
	    return $stats;
    }

    public function getFriendList($user_id = false)
    {
	    try {
		    $vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'],$_COOKIE['token']);

		    $user_friends = $vk->api('friends.get',array(
		        'uid' => $user_id ? $user_id : '0',
		        'fields'  => 'uid,first_name,last_name,photo_100',
		        'order'   => 'fields')
		    );
		    $user_friends = $user_friends['response'];

		    $user_friendsIntro = [];
		    foreach ($user_friends as $user_friend)
		    {
			    $user_friendsIntro[] = $this->getIntro($user_friend['user_id']);
		    }
			return $user_friends;

	    }
	    catch (VKException $error) {
		    echo $error->getMessage();
	    }
    }
}
