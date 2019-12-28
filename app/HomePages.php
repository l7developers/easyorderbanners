<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class HomePages extends Authenticatable
{
    use Notifiable;

	protected $table = 'homepages';
	
	public function images(){
		return $this->hasMany('\App\HomeImages','homepage_id')->orderBy('weight','ASC');
	}
}
