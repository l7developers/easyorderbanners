<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class OrderPoOptions extends Model
{
    use Notifiable;

	protected $table = 'order_po_options';
	
	public function orderProduct(){
		return $this->belongsTo('\App\OrderProducts','order_product_id');
	}
}
