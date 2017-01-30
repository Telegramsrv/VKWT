<?php

namespace App\Console\Commands;

use VK\VK;
use App\Users;
use App\Token;
use App\Friends;
use Illuminate\Console\Command;

class UpdateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user info(first_name,last_name,photo) and friend list with add to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    public function handle()
    {
	    $VKConfig = App('service.vkconfig');

	    $UserList = Token::where( 'updated_at', '>', date('Y-m-d H:m:s', time() - 24*60*60))->get();
	    foreach ( $UserList as $User )
	    {
		    $vk = new VK( $VKConfig->get_config('app_id'), $VKConfig->get_config('api_secret'), $User->token);

		    //update friend list
		    while (!isset($user_friends['response'])) {
			    $user_friends = $vk->api(
				    'friends.get',
				    array(
					    'fields' => 'uid,first_name,last_name,photo_100',
					    'order'  => 'name'
				    )
			    );//NO error token false
		    }
		    $user_friends = $user_friends['response'];
		    foreach ( $user_friends as $friend)
		    {
		    	if (isset($friend['deactivated']))  continue;

			    $NewFrind = Friends::where('user_id', $User->user_id)->where('friend_id',$friend['uid'])->first();
			    if (!$NewFrind){
				    $NewFrind = new Friends;
			    }
			    $NewFrind->user_id = $User->user_id;
			    $NewFrind->friend_id = $friend['uid'];
			    $NewFrind->save();
			    $NewFrind->touch();

			    $NewUser = Users::where('user_id',$friend['uid'])->first();
			    if (!$NewUser){
				    $NewUser = new Users;
				    $NewUser->status = 'processing';
			    }
			    $NewUser->user_id    = $friend['uid'];
			    $NewUser->first_name = $friend['first_name'];
			    $NewUser->last_name = $friend['last_name'];
			    $NewUser->photo = $friend['photo_100'];
			    $NewUser->save();
		    }

		    //update urself
		    while (!isset($userinfo['response'])) {
			    $userinfo = $vk->api(
				    'users.get',
				    array(
					    'fields' => 'uid,first_name,last_name,photo_100',
				    )
			    );
		    }

		    $SelfUser = $User->user();
		    if (!$SelfUser) {
			    $SelfUser = new Users;
			    $SelfUser->status = 'processing';
		    }
		    $userinfo = $userinfo['response'][0];
		    $SelfUser->user_id    = $userinfo['uid'];
		    $SelfUser->first_name = $userinfo['first_name'];
		    $SelfUser->last_name = $userinfo['last_name'];
		    $SelfUser->photo = $userinfo['photo_100'];
		    $SelfUser->save();

		    Friends::where('user_id',$User->user_id)->where('updated_at','<',date('Y-m-d H:m:s',time() - 10*60))->get()->map(function ($user){
		    	Users::find($user->friend_id)->delete();
		    	$user->delete();
			    $this->info('User '.$user->friend_id.' deleted!');
		    } );
	    }
	    $this->info('Users and Friends info successful updated!');
    }
}
