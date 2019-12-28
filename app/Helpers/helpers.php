<?php
use App\Setting;

if (! function_exists('get_setting')) {
   function get_setting($key="") 
   {
      	$setting=Setting::where('name', $key)->first(); 
		if ($setting) {
			return $setting->value;
		}
		else
		{
			return null;
		}
   }
}

if (! function_exists('ft2Cm')) {

/**
    convert feet to centimeter
    @params $ft is having the value in feet float
    @return $total integer return the new value in inches
    */
    function ft2Cm($ft=0)
    {    	
		/* $feet = intval($ft);
		$inches = ($ft - $feet)*10; 
	    $newinches = $feet * 12;
	    $total = $newinches + $inches; */
	    $total = $ft * 12;

	    return $total;
    }

}  

if (! function_exists('priceFormat')) {

	/**
    convert price $X.XX format.
    @params $price is having the value which is convert into format
    @return $price which is proper formated
    */
    function priceFormat($price=0)
    {    	
		$price = number_format($price,2);
	    return $price;
    }

}  

if (! function_exists('productMinPrice')) {

	/**
    get product min price.
    @params $id is having the value which is id of of that product
    @return $minPrice which is product min price
    */
    function productMinPrice($product)
    {    	
		$min_price = $product->price;
    	$productPrice=\App\ProductPrice::where('product_id',$product->id)->get();
    	if(!empty($productPrice))
    	{
    		foreach ($productPrice as $key => $value) {
    			if($value->price < $min_price)
    			{
    				$min_price = $value->price;
    			}
    		}
    	}
		return $min_price;
    }

}  

if (! function_exists('display_product_price')) {

/**
    convert feet to centimeter
    @params $ft is having the value in feet float
    @return $total integer return the new value in inches
    */
    function display_product_price($product)
    {   
    	$min_price = $product->price;
    	$productPrice=\App\ProductPrice::where('product_id',$product->id)->get();
    	if(!empty($productPrice))
    	{
    		foreach ($productPrice as $key => $value) {
    			if($value->price < $min_price)
    			{
    				$min_price = $value->price;
    			}
    		}
    	} 
		
		$min_price = priceFormat($min_price);
		
		$pricestr ="";
		if($product->price_sqft_area == 1){
			$pricestr ="As Low as <strong>$".$min_price."/sq.ft. </strong>";
		}elseif($product->show_width_height == 1){
			$pricestr ="Starting at <strong>$".$min_price."/sq.ft. </strong>";
		}else{
			$pricestr ="Starting at <strong>$".$min_price." </strong>";
		}
	    return $pricestr;
    }

	function display_product_price_old($product){   
    	$min_price = $product->price;
    	$productPrice=\App\ProductPrice::where('product_id',$product->id)->get();
    	if(!empty($productPrice))
    	{
    		foreach ($productPrice as $key => $value) {
    			if($value->price < $min_price)
    			{
    				$min_price = $value->price;
    			}
    		}
    	} 
		
		$min_price = priceFormat($min_price);
		
		$pricestr ="";
		if($product->price_sqft_area == 1){
			if(empty($product->min_price) || $product->min_price == 0)
				$pricestr ="As Low as <strong>$".$min_price."/sq.ft. </strong>";
			else
				$pricestr ="Starting at <strong>$".priceFormat($product->min_price)."/sq.ft. </strong>";
		}elseif($product->show_width_height == 1){
			if(empty($product->min_price) || $product->min_price == 0)
				$pricestr ="Starting at <strong>$".$min_price."/sq.ft. </strong>";
			else
				$pricestr ="Starting at <strong>$".priceFormat($product->min_price)."/sq.ft. </strong>";
		}else{
			if(empty($product->min_price) || $product->min_price == 0)
				$pricestr ="Starting at <strong>$".$min_price." </strong>";
			else
				$pricestr ="Starting at <strong>$".priceFormat($product->min_price)." </strong>";
		}
	    return $pricestr;
    }

}  