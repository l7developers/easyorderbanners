<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Messages extends Authenticatable
{
    use Notifiable;

	protected $table = 'messages';
	
	public function from_detail(){
		return $this->belongsTo('\App\User','from_id');
	}
	
	public function to_detail(){
		return $this->belongsTo('\App\User','to_id');
	}
}
