<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
	protected $primaryKey = 'user_id';

	public function statistics()
	{
		$buffer = [];
		$walls = $this->wallPosts()->get();

		foreach ($walls as $wall) {
			$wall->date  = date('Y-m-d', $wall->date);
			if (array_key_exists($wall->date,$buffer)){
				$buffer[$wall->date] += $wall->likes;
			}
			else {
				$buffer[$wall->date] = $wall->likes;
			}
		}
		return $buffer;
	}

	public function qtyWallPosts(){
		return $this->wallPosts()->count();
	}

	public function qtyLikes(){
		return $this->wallPosts()->sum('likes');
	}

	public function token(){
		return $this->hasOne('App\Token','user_id');
	}

	public function wallPosts(){
		return $this->hasMany('App\Walls','user_id');
	}

	public function friends(){
		return $this->hasMany('App\Friends','user_id');
	}

	public function getTopRatedPost(){
		return $this->wallPosts()->orderBy('likes','desc')->first();
	}

	public function getFirstPost(){
		return $this->wallPosts()->orderBy('date','asc')->first();
	}
}
