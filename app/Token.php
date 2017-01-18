<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
	protected $foreignKey = 'user_id';

	public function user()
	{
		return $this->belongsTo('App\Users','user_id');
	}
}
