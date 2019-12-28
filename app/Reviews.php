<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Reviews extends Authenticatable
{
    use Notifiable;

	protected $table = 'reviews';
	
	public function user(){
		return $this->belongsTo('\App\User','user_id');
	}

	public function product(){
		return $this->belongsTo('\App\Products','product_id')->select(array('id', 'name','slug'));;
	}
}
