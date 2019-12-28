<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductOptions extends Model{
	
    protected $table = 'product_options';
	
	public function CustomOption() {
		return $this->belongsTo('\App\CustomOptions','option_id');
	}
}
