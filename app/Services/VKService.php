<?php

namespace App\Services;

use VK\VK;

/**
 * Created by PhpStorm.
 * User: theardent
 * Date: 20.01.17
 * Time: 12:49
 */
class VKService
{
	protected $vk_config = array(
		'app_id'        => '5809395',
		'api_secret'    => 'uhK1NhUTKDEXbwk9v0ZS',
		'callback_url'  => 'http://laravel.loc/',
		'api_settings'  => 'wall,friends'
	);

	public function get_config($key)
	{
		return $this->vk_config[$key];
	}

	public function getAuthorizeURL()
	{
		$vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret']);
		$url = $vk->getAuthorizeURL(
			$this->vk_config['api_settings'], $this->vk_config['callback_url']);
		return $url;
	}
}