<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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
		//TODO
		//return check auth of user
	}

	public function __construct()
    {
	    try {
		    if (isset($_COOKIE['token']))
		    {
			    $vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'],$_COOKIE['token']);

			    $this->userinfo = $vk->api('users.get', array(
				    'fields'    => 'uid,first_name,last_name,photo_100',
			    ));
		    }
		    else {
		    $vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret']);

		    if (!isset($_REQUEST['code'])) {
			    $authorize_url = $vk->getAuthorizeURL(
				    $this->vk_config['api_settings'], $this->vk_config['callback_url']);
			    echo '<a href="' . $authorize_url . '">Sign in with VK</a>';
		    } else {
			    $access_token = $vk->getAccessToken($_REQUEST['code'], $this->vk_config['callback_url']);

			    setcookie('token',$access_token['access_token'],time()+$access_token['expires_in']);
			    header("Location: ".$this->vk_config['callback_url']);
		    }
		    }
	    } catch (VKException $error) {
		    echo $error->getMessage();
	    }
    }

    public function getIntro($user_id = false)
    {
	    try {
		    if (isset($_COOKIE['token']))
		    {
			    $vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'],$_COOKIE['token']);

			    //WALLS
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
				    $user_walls = array_merge($user_walls,$walls_tmp);
				    $offset += $count;
			    }while( $count == $limit );

			    //WALLS COUNT,likes

			    $likesCount = 0;
			    foreach ( $user_walls as $user_wall)
			    {
				    $likesCount += $user_wall['likes']['count'];
			    }


			    //USER INFO
				$user = $vk->api('users.get', array('user_ids' => $user_id ? $user_id : '', 'fields' => 'uid,first_name,last_name,photo_100'));
				$user = $user['response'][0];

				$user['likescount'] = $likesCount;
				$user['wallcount'] = count($user_walls);

			    return $user;
		    }
		    else {
			    header("Location: " . $this->vk_config['callback_url']);
		    }
	    }
	    catch (VKException $error) {
		    echo $error->getMessage();
	    }
    }

    public function getTopRatedWall($user_id)
    {
    	//TODO
	    //get all wall and return wall with max likes
    }

    public function getFirstWall($user_id)
    {
    	//TODO
	    //get wall count and return first wall
    }

    public function getWallStats($user_id)
    {
    	//TODO
	    //get all wall and return array format Y-m-d | likes count per day
    }

    public function getFriendList($user_id = false)
    {
	    try {
		    if (isset($_COOKIE['token']))
		    {
			    $vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret'],$_COOKIE['token']);

			    $user_friends = $vk->api('friends.get',array(
			    	'uid' => $user_id ? $user_id : '0',
			    	'fields'  => 'uid,first_name,last_name,photo_100',
			        'order'   => 'fields')
			    );
				return $user_friends['response'];
		    }
		    else {
			    header("Location: " . $this->vk_config['callback_url']);
		    }
	    }
	    catch (VKException $error) {
		    echo $error->getMessage();
	    }
    }
}
