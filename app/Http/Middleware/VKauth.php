<?php

namespace App\Http\Middleware;


use App\Users;
use App\Token;
use Closure;
use Illuminate\Http\Response;
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

	protected $userinfo = [];

    public function handle($request, Closure $next)
    {
    	$VKConfig = App('service.vkconfig');
	    try {
		    if ($request->hasCookie('token')) {
			    $vk = new VK( $VKConfig->get_config('app_id'), $VKConfig->get_config('api_secret'), $request->cookie('token'));
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
			    $vk = new VK($VKConfig->get_config('app_id'), $VKConfig->get_config('api_secret'));

			    if (!$request->code) {
				    return redirect('/auth');//Get auth
			    } else {
				    $access_token = $vk->getAccessToken( $request->code, $VKConfig->get_config('callback_url'));
					//Check token at DB and Update/Add
				    $UserToken = Token::where('user_id',$access_token['user_id'])->first();
				    if (!$UserToken){
				    	$UserToken = new Token;
				    }
				    $UserToken->token = $access_token['access_token'];
				    $UserToken->user_id = $access_token['user_id'];
				    $UserToken->save();
				    return redirect('/')->withCookie( cookie( 'token', $access_token['access_token'], $access_token['expires_in']));//Auth true
			    }
		    }
	    } catch (VKException $error) {
		    echo $error->getMessage();
	    }
        return $next($request);
    }
}
