<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Events extends Authenticatable
{
    use Notifiable;

	protected $table = 'events';
	
	public function customer(){
		$this->belongsTo('App\User','customer_id');
	}
}
