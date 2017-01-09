<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
	protected $primaryKey = 'user_id';

	private $user_id;

	public function wallPosts()
	{
		return Walls::where('user_id',$this->user_id)->get();
	}
}
