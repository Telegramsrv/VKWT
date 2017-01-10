<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Walls extends Model
{
	protected $foreignKey = 'walls.user_id';

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
