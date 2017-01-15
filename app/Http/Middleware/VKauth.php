<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\Response;
use VK\VK;

class VKauth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

	protected $vk_config = array(
		'app_id'        => '5809395',
		'api_secret'    => 'uhK1NhUTKDEXbwk9v0ZS',
		'callback_url'  => 'http://laravel.loc/',
		'api_settings'  => 'wall,friends'
	);

	protected $userinfo = [];

    public function handle($request, Closure $next)
    {
	    try {
		    if ($request->hasCookie('token')) {
			    $vk = new VK( $this->vk_config['app_id'], $this->vk_config['api_secret'], $request->cookie('token'));
			    while (true) {
				    $this->userinfo = $vk->api(
					    'users.get',
					    array(
						    'fields' => 'uid,first_name,last_name,photo_100',
					    )
				    );

				    if (isset($this->userinfo['response'][0])) {
					    $this->userinfo = $this->userinfo['response'][0];
					    return $next($request);//Auth true
				    }
				    if ($this->userinfo['error']['error_code'] == 5){
					    return redirect('/auth')->withCookie( cookie( 'token', 0, -1));//Auth false
				    }
			    }
		    }
		    else {
			    $vk = new VK($this->vk_config['app_id'], $this->vk_config['api_secret']);

			    if (!$request->code) {
				    $url = $vk->getAuthorizeURL(
					    $this->vk_config['api_settings'], $this->vk_config['callback_url']);
				    return redirect('/auth')->withCookie( cookie( 'url', $url, 1));//Get auth
			    } else {
				    $access_token = $vk->getAccessToken( $request->code, $this->vk_config['callback_url']);
				    return redirect('/auth')->withCookie( cookie( 'token', $access_token['access_token'], $access_token['expires_in']));//Auth true
			    }
		    }
	    } catch (VKException $error) {
		    echo $error->getMessage();
	    }
        return $next($request);
    }
}
