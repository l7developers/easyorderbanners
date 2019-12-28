<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class OrderPoDetails extends Model
{
	use Notifiable;

	protected $table = 'order_po_details';
	
	public function PoProduct() {
		return $query = $this->belongsTo('\App\OrderProducts','order_product_id')->with('product','OrderPOAddress');
	}
	
	public function PoOption(){
		return $this->hasMany('\App\OrderPoOptions','order_product_id','order_product_id');
	}
	
	public function PoAddress(){
		return $this->hasMany('\App\OrderPOAddress','order_product_id','order_product_id');
	}
}
