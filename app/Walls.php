<?php

namespace App;

use VK\VK;
use Illuminate\Database\Eloquent\Model;

class Walls extends Model
{
	protected $foreignKey = 'walls.user_id';

	public function user()
	{
		return $this->belongsTo('App\Users');
	}

	public function getFullWallPost()
	{
		$VKConfig = App('service.vkconfig');

		$Token = $this->user()->first()->token()->first();
		if (!$Token) {
			$Friend = Friends::where('friend_id',$this->user()->first()->user_id)->first();
			$Token = Users::where('user_id',$Friend->user_id)->first()->token()->first();
		} else {
			$Token = $this->user()->first()->token()->first();
		}

		$vk = new VK( $VKConfig->get_config('app_id'), $VKConfig->get_config('api_secret'), $Token->token);
		while (!isset($wall['response'])) {
			$wall = $vk->api(
				'wall.getById',
				array(
					'posts' => $this->user_id.'_'.$this->wall_id,
				)
			);
		}

		return $wall['response'][0];
	}
}
