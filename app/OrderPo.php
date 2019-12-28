<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class OrderPo extends Model{
	use Notifiable;

	protected $table = 'order_po';
	
	public function agent(){
		return $this->belongsTo('\App\User','agent_id');
	}
	
	public function vendor(){
		return $this->belongsTo('\App\Vendors','vendor_id');
	}
	
	public function po_details(){
		$query = $this->hasMany('\App\OrderPoDetails','po_id','po_id');
		$query->leftjoin('order_products as orderProducts', function($join){
			$join->on('order_po_details.order_product_id','=','orderProducts.id');
		});
		$query->select('order_po_details.*','orderProducts.art_work_status');
		return $query;
	}
	
	public function PoDetails(){
		return $this->hasMany('\App\OrderPoDetails','po_id','po_id')->with('PoOption');
	}
}
