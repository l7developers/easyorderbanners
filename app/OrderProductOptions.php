<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OrderProductOptions extends Authenticatable
{
    use Notifiable;

	protected $table = 'order_product_options';
	
	public function optionDetail(){
		return $this->belongsTo('\App\CustomOptions','custom_option_id');
	}
}
