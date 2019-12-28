<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use DB;
use \App\Products;

class ProductVariant extends Authenticatable
{
    use Notifiable;

	protected $table = 'product_variant';
	
	public function variantValues() {		
		return $this->hasMany('\App\ProductVariantValues','variant_id')->orderBy('id','ASC');
	}

	public function variantCombinantion() {		
		//return $this->hasMany('\App\ProductVariantValues','variant_id')->orderBy('id','ASC');
		$asd = $this->query()->get();
		//echo "jitu";
		//pr($asd->toArray());die;
		//return $asd;
	}
	
	public function detail($id){
		return DB::table('product_variant')->where('id', $id)->first();
	}
		
}
