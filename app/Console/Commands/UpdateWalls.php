<?php

namespace App\Console\Commands;

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

	protected $vk_config = array(
		'app_id'        => '5809395',
		'api_secret'    => 'uhK1NhUTKDEXbwk9v0ZS'
	);


	/**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    $UserList = Token::where( 'updated_at', '>', date('Y-m-d H:m:s', time() - 24*60*60))->get();
	    foreach ( $UserList as $User )
	    {
		    $vk = new VK( $this->vk_config['app_id'], $this->vk_config['api_secret'], $User->token);

		    $FriendList = $User->user()->first()->friends()->get();

		    foreach ( $FriendList as $friend)
		    {
			    $FriendWallUpdateTime = Walls::where('user_id',$friend->friend_id)->first();

			    if ( $FriendWallUpdateTime && $FriendWallUpdateTime->updated_at < date('Y-m-d H:m:s', time() - 60*60)) continue;

			    $limit = 100;
			    $offset = 0;
			    $user_walls = [];
			    do {
				    while(true) {
					    $walls_tmp = $vk->api(
						    'wall.get',
						    array(
							    'owner_id' => $friend->friend_id,
							    'count'    => $limit,
							    'offset'   => $offset,
							    'filter'   => 'owner'
						    )
					    );
					    if (isset($walls_tmp['response'])){
						    $walls_tmp = $walls_tmp['response'];
						    unset($walls_tmp[0]);
						    break;
					    }

				    }
				    $count = count($walls_tmp);
				    $user_walls = array_merge($user_walls, $walls_tmp);
				    $offset += $count;
			    }
			    while ($count == $limit);

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
			    $this->info('User id'.$friend->friend_id.' walls add');
		    }

		    //update self walls
		    $limit = 100;
		    $offset = 0;
		    $user_walls = [];
		    do {
			    while(true) {
				    $walls_tmp = $vk->api(
					    'wall.get',
					    array(
						    'owner_id' => $User->user_id,
						    'count'    => $limit,
						    'offset'   => $offset,
						    'filter'   => 'owner'
					    )
				    );
				    if (isset($walls_tmp['response'])){
					    $walls_tmp = $walls_tmp['response'];
					    unset($walls_tmp[0]);
					    break;
				    }
			    }
			    $count = count($walls_tmp);
			    $user_walls = array_merge($user_walls, $walls_tmp);
			    $offset += $count;
		    }
		    while ($count == $limit);

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
	    }
	    $this->info('Walls info successful updated');
    }
}
