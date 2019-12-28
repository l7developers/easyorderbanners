<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use DB;

class Orders extends Authenticatable
{
    use Notifiable;

    const PAYMENTSTATUS_RECEIVED = 2;
    const PAYMENTSTATUS_PENDING = 3;
    const PAYMENTSTATUS_DECLINED = 4;
    const PAYMENTSTATUS_ACCOUNT = 7;

	protected $table = 'orders';
	
	public function admin() {
		return $this->belongsTo('\App\User','admin_id');
	}
	
	public function agent() {
		return $this->belongsTo('\App\User','agent_id');
	}
	
	public function designer() {
		return $this->belongsTo('\App\Designers','designer_id');
	}
	
	public function vendor() {
		return $this->belongsTo('\App\Vendors','vendor_id');
	}
	
	public function customer() {
		return $this->belongsTo('\App\User','user_id');
	}
	
	public function orderProduct(){
		
		$query = $this->hasMany('\App\OrderProducts','order_id');
		
		$query->leftjoin('order_address as address', function($join)
				{
					$join->on('order_products.order_id','=','address.order_id');
					$join->on('order_products.product_id','=','address.product_id');
					$join->on('order_products.id','=','address.order_product_id');
				});
				
		$query->leftjoin('reviews as review', function($join){
					$join->on('order_products.product_id','=','review.product_id');
					$join->on('order_products.order_id','=','review.order_id');
				});
		
		$query->select('order_products.*','address.billing_company_name','address.billing_phone_number','address.billing_fname','address.billing_lname','address.billing_add1','address.billing_add2','address.billing_city','address.billing_state','address.billing_country','address.shipping_company_name','address.shipping_phone_number','address.shipping_fname','address.shipping_lname','address.shipping_add1','address.shipping_add2','address.shipping_ship_in_care','address.shipping_zipcode','address.shipping_city','address.shipping_state','address.shipping_country','review.id as review');
		
		$query->with('product','designer','vendor','shipping');
		
		return $query;
	}
	
	public function orderProductsDetails() {
		$query = $this->hasMany('\App\OrderProducts','order_id')->with('vendor','designer','notes');
		$query->join('products as product', function($join){
					$join->on('order_products.product_id','=','product.id');
				});
		$query->select('order_products.*','product.name as productName','product.slug as product_slug','product.no_artwork_required as product_art_work');
		return $query;
	}

    /**
     * @return Builder
     */
    public function OrderLineItems()
    {
        $query = $this->hasMany('\App\OrderProducts','order_id')
            ->with('vendor','designer','notes');

        $query->leftJoin('products as product', function($join)
        {
            $join->on('order_products.product_id','=','product.id');
        });

        $query->select(
            'order_products.*',
            'product.name as productName',
            'product.slug as product_slug',
            'product.no_artwork_required as product_art_work'
        );

        return $query;
    }
	
	public function Products() {
		$query = $this->hasMany('\App\OrderProducts','order_id')->with('product','shipping');
		
		$query->select('order_products.*','review.id as review');
		
		$query->leftjoin('reviews as review', function($join){
					$join->on('order_products.product_id','=','review.product_id');
					$join->on('order_products.order_id','=','review.order_id');
				});
		
		return $query;
	}
	
	public function files() {
		
		$query = $this->hasMany('\App\OrderFiles','order_id');
		return $query;
	}
	
	public function orderProductOptions() {
		return $this->hasMany('\App\OrderProductOptions','order_id')->with('optionDetail');
	}
	
	public function orderAddress() {
		return $this->hasOne('\App\OrderAddress','order_id');
	}
	
	public function orderPOAddress() {
		return $this->hasOne('\App\OrderPOAddress','order_id');
	}
	
	public function orderAddresses() {
		return $this->hasMany('\App\OrderAddress','order_id');
	}
}
