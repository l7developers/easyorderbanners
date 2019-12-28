<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use DB;

class Products extends Authenticatable
{
    use Notifiable;
	
	public function Catgory() {
		return $this->belongsTo('\App\Category','category_id');
	}
	
	public function product_image() {
		return $this->hasOne('\App\ProductsImages','product_id');
	}
	
	public function Images() {
		return $this->hasMany('\App\ProductsImages','product_id')->orderBy('weight','ASC');
	}

	public function Options() {		
		return $this->hasMany('\App\ProductOptions','product_id')->with(['CustomOption']);
		
	}

	public function custom() {		
		return $this->hasMany('\App\ProductTabs','product_id')->orderBy('id','ASC');
		
	}

	public function product_prices() {		
		return $this->hasMany('\App\ProductPrice','product_id')->orderBy('min_area','ASC');
	}
	
	public function variants() {		
		return $this->hasMany('\App\ProductVariant','product_id')->orderBy('id','ASC')->with('variantValues');
		
	}
	
	
	public function variantCombinantion() {		
		
		$query = $this->hasMany('\App\ProductVariantPrice','product_id');
		
		$query->leftJoin('product_variant_values as variant1', function($join)
				{
					$join->on('product_variant_price.varient_id1','=','variant1.id');
				});
		
		$query->leftJoin('product_variant_values as variant2', function($join)
				{
					$join->on('product_variant_price.varient_id2','=','variant2.id');
					//$join->orOn('product_variant_price.varient_id2','=',DB::raw("'0'"));
					/* $join->on(function($query)
						{
						 $query->orOn('product_variant_price.varient_id2','=',DB::raw("'0'"));
						}); */
				});
		
		
		$query->select('product_variant_price.*','variant1.value as variant1','variant2.value as variant2');
		
		return $query;
	}
	
	public function shipping(){
		return $this->hasOne('\App\ProductShipping','product_id');
	}
}
