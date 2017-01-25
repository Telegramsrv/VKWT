<?php

namespace App\Http\Middleware;

use Illuminate\Http\Response;
use App\Token;
use Closure;

class UserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	    $User = Token::where('token',$request->cookie('token'))->first()->user()->first();
    	if ($User)
	    {
	    	if (!$User->friends()->get()->contains(function ($friend){ return $friend->user()->status == 'processing'; } ))
	    	{
	    		return $next($request);
		    }
		    else {
	    		$data = array(  'title' => 'Загрузка инфомации',
			                    'proccess' => true);
			    return new Response(view('pages.loading',$data));
		    }
	    }
	    else {
		    $data = array(  'title' => 'Загрузка инфомации',
		                    'proccess' => false);
		    return new Response(view('pages.loading',$data));
	    }
    }
}
