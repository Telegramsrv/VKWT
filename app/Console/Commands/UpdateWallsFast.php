<?php

namespace App\Console\Commands;

use Cache;
use App\Users;
use VK\VK;
use App\Token;
use App\Walls;
use Illuminate\Console\Command;

class UpdateWallsFast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:walls_fast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update last 100 walls for all users';

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
	    $timestart = time();
	    foreach ( $UserList as $User )
	    {
		    $vk = new VK( $VKConfig->get_config('app_id'), $VKConfig->get_config('api_secret'), $User->token);

		    $FriendList = $User->user()->friends()->get();
		    $rows = intval($FriendList->count()/25) + 1;
		    for ($i = 0; $i < $rows;$i++)
		    {
			    if ($i == $rows - 1)
				    $cols = $FriendList->count() % 25;
			    else $cols = 25;

			    $code = 'return {"returned": [';
			    for ($j = 0; $j < $cols; $j++) {
				    $code .= 'API.wall.get({"owner_id": "' . $FriendList[$j+$i*25]->friend_id . '","filter": "owner","count": "100"}),';
			    }
			    $code .= ']};';
			    while (!isset($resp['response'])) {
				    $resp = $vk->api('execute', ['code' => $code]);
			    }
			    $resp = $resp['response']['returned'];
			    foreach ($resp as $walls)//update 100 walls
			    {
			    	unset($walls[0]);
				    foreach ($walls as $wall)
				    {
					    $Wall = Walls::where('user_id', $wall['to_id'])->where('wall_id', $wall['id'])->first();
					    if (!$Wall) {
						    $Wall = new Walls;
					    }
					    $Wall->user_id = $wall['to_id'];
					    $Wall->wall_id = $wall['id'];
					    $Wall->date = $wall['date'];
					    $Wall->likes = $wall['likes']['count'];
					    $Wall->save();
				    }
				    $friend = Users::where( 'user_id', $wall['to_id'])->first();
				    Cache::put($friend->user_id, [$friend->statistics(), $friend->toArray()], 24 * 60);
				    Users::where('user_id',$friend->user_id)->update(['status' => 'done']);
				    $this->info('User id'.$friend->user_id.' walls updated');
			    }
		    }


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
		    Cache::put( $User->user_id, [ $User->user()->statistics(), $User->user()->toArray()], 24*60);
		    Users::where('user_id',$User->user_id)->update([ 'status' => 'done']);
		    $this->info('Update user '.$User->user_id);
	    }
	    $this->info('Time :'.(time()-$timestart));
	    $this->info('Walls info successful updated(just 100 walls for user)');
    }
}
