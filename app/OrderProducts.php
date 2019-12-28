<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class OrderProducts extends Authenticatable
{
    use Notifiable;

	protected $table = 'order_products';
	
	
	public function product() {
		return $query = $this->belongsTo('\App\Products','product_id')->with('Catgory','variants');
	}
	
	public function shipping() {
		return $query = $this->hasOne('\App\ProductShipping','product_id','product_id');
	}
	
	public function order() {
		return $this->belongsTo('\App\Orders','order_id');
	}

	public function order_customer() {
		return $this->belongsTo('\App\Orders','order_id')->with('customer');
	}

	public function productAddress() {
		//return $this->hasOne('\App\OrderAddress','order_id','order_id');
		return $this->hasMany('\App\OrderAddress','order_id','order_id');
	}
	
	public function orderProductOptions() {
		return $this->hasMany('\App\OrderProductOptions','order_product_id');
	}
	
	public function orderProductFiles() {
		return $this->hasMany('\App\OrderFiles','order_product_id');
	}
	
	public function designer() {
		return $this->belongsTo('\App\Designers','designer_id');
	}
	
	public function vendor() {
		$query =  $this->belongsTo('\App\Vendors','vendor_id');
		//$query->select(DB::raw("concat(vendors.fname, ' ', vendors.lname) as vendor_name"));
		return $query;
	}
	
	public function notes() {
		return $this->hasMany('\App\Notes','order_id','item_id');
	}
	
	public function orderProductAddress(){
		return $this->hasOne('\App\OrderAddress','order_product_id');
	}
	
	public function orderPOAddress(){
		return $this->hasOne('\App\OrderPOAddress','order_product_id');
	}
	public function orderProductPoDetails(){
		return $this->hasOne('\App\OrderPoDetails','order_product_id')->with(['PoOption','PoAddress']);
	}
}
