<?php

namespace App\Console\Commands;

use Cache;
use App\Users;
use VK\VK;
use App\Token;
use App\Walls;
use Illuminate\Console\Command;

class UpdateWalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:walls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

		    $FriendList = $User->user()->first()->friends()->get();
		    foreach ( $FriendList as $friend)
		    {
			    $FriendWallUpdateTime = Walls::where('user_id',$friend->friend_id)->first();

			    if ( $FriendWallUpdateTime && $FriendWallUpdateTime->updated_at > date('Y-m-d H:m:s', time() - 60*60)) continue;

				//upload last 2500 posts
			    $code = 'return {"returned": [';
			    for ($i = 0; $i < 25; $i++) {
				    $code .= 'API.wall.get({"owner_id": "' . $friend->friend_id . '","filter": "owner","count": "100","offset": "' . ($i * 100) . '"}),';
			    }
			    $code .= ']};';
			    while (!isset($resp['response'])) {
				    $resp = $vk->api('execute', ['code' => $code]);
			    }
			    $user_walls = [];
			    $resp = $resp['response']['returned'];
			    foreach ( $resp as $wall)   {
				    unset($wall[0]);
				    if ($wall) $user_walls = array_merge($user_walls, $wall);
			    }

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

			    Cache::put( $friend->friend_id, [ $friend->user()->statistics(), $friend->user()->toArray()], 24*60);
			    Users::where('user_id',$friend->friend_id)->update([ 'status' => 'done']);
			    $this->info('User id'.$friend->friend_id.' walls updated');
		    }

		    //update self walls
		    $code = 'return {"returned": [';
		    for ($i = 0; $i < 25; $i++) {
			    $code .= 'API.wall.get({"owner_id": "'.$User->user_id.'","filter": "owner","count": "100","offset": "' . ($i * 100) . '"}),';
		    }
		    $code .= ']};';
		    while (!isset($result['response'])) {
			    $result = $vk->api('execute', ['code' => $code]);
		    }
		    $self_walls = [];
		    foreach ($result['response']['returned'] as $wall)   {
			    unset($wall[0]);
			    if ($wall) $self_walls = array_merge($self_walls, $wall);
		    }

		    foreach ( $self_walls as $user_wall)
		    {
			    $Wall = Walls::where('user_id',$user_wall['to_id'])->where('wall_id',$user_wall['id'])->first();
			    if (!$Wall) {
				    $Wall = new Walls;
			    }
			    $Wall->user_id = $user_wall['to_id'];
			    $Wall->wall_id = $user_wall['id'];
			    $Wall->date    = $user_wall['date'];
			    $Wall->likes   = $user_wall['likes']['count'];
			    $Wall->save();
		    }

		    Cache::put( $User->user_id, [ $User->statistics(), $User->toArray()], 24*60);
		    Users::where('user_id',$User->user_id)->update([ 'status' => 'done']);
		    $this->info('Update user '.$User->user_id);
	    }
	    $this->info('Walls info successful updated');
    }
}
