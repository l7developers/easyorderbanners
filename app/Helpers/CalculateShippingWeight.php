<?php
namespace App\Helpers;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use DB;
use App\Products;
use App\ProductVariant;
use App\ProductVariantValues;
use App\ProductVariantPrice;
use App\ProductShipping;
use App\State;
use App\UserAddress;
use App\Orders;

class CalculateShippingWeight{
    
	public function product_weight($product_form, $data)
    {
		$total_weight = 0;
		$product_shipping = array();
		$ship_detail = ProductShipping::where('product_id',$product_form['product_id'])->first();

		if(count($ship_detail) > 0) {
			if($ship_detail->type == 'free_value' and $data['total'] < $ship_detail->min_value){
				$product_shipping['product_shipping_type'] = 'free_value';
				$product_shipping['product_shipping'] = true;
			} else if($ship_detail->type == 'paid') {
				$product_shipping['product_shipping_type'] = 'paid';
				$product_shipping['product_shipping'] = true;
			} else if($ship_detail->type == 'flat') {
				$product_shipping['product_shipping_type'] = 'flat';
				$product_shipping['product_shipping_price'] = $data['shipping_price'];
				$product_shipping['product_shipping'] = false;
			} else if($ship_detail->type == 'free') {
				$product_shipping['product_shipping_type'] = 'free';
				$product_shipping['product_shipping'] = false;
			}
			
			$product_shipping['product_reduce_price'] = $ship_detail->reduce_price;
			$product_shipping['product_additional_qty_price'] = $ship_detail->additional_qty_price;
			$product_shipping['product_qty'] = $data['quantity'];
		}else{
			$product_shipping['product_shipping_type'] = 'free';
			$product_shipping['product_shipping'] = false;
		}
		
		$weight = 0;
		if(!empty($data['product_weight'])){
			$weight = number_format($data['product_weight'],2,'.','');
		}
		else{
			if($product_form['product_id'] != config('constants.CUSTOM_PRODUCT_ID'))
				$weight = 1;
		}

		if (array_key_exists('variants',$product_form) and !empty($product_form['variants'])) {
			$db = ProductVariantPrice::where('product_id',$product_form['product_id']);
			$j = 1;
			foreach ($product_form['variants'] as $key => $val) {
				$db->where('varient_id'.$j, $val);
				$j++;
			}

			$variantPrice = $db->first();
		
			if (!empty($variantPrice->shipping_weight)){
				$weight += number_format($variantPrice->shipping_weight,2,'.','');
			}

			if (!empty($variantPrice->shipping_price)) {
                $product_shipping['product_shipping_price'] += $variantPrice->shipping_price;
            }
		}

		if ($data['show_width_height'] == 1) {
			$weight = $weight * ($data['width']*$data['height']);
		}

		foreach($data['custom_option'] as $option) {
			if(array_key_exists('weight',$option)) {
				$wt = 0;
				if($option['price_type'] == 'area')
					$wt = $option['weight']*($data['width']*$data['height']);
				else if($option['price_type'] == 'parimeter')
					$wt = $option['weight']*(($data['width']+$data['height'])*2);
				else if($option['price_type'] == 'item')
					$wt = $option['weight'];

				$product_shipping['product_shipping_price'] += $option['flat_rate_additional_price'];
				$weight += number_format($wt,2,'.','');
			}
		}
		
		$weight = $weight * $data['quantity'];
		$product_shipping['weight'] = $weight;

		return $product_shipping;
	}
	
	public function weight($data,$add_type = 'front'){
		//pr(session()->get('cart'));//die;
		
		$multiple = false;
		if($add_type == 'admin'){
			$detail = $this->admin_address($data);
			$multiple = $detail['multiple'];
			$address = $detail['address'];
		}else{
			$detail = $this->front_address($data);
			$multiple = $detail['multiple'];
			$address = $detail['address'];
		}
		
		$total_weight = 0;
		$product_weight = array();
		$product_shipping_amounts = array();
		$product_shipping = false;
		$session_total = 0;
		//pr(session()->get('cart'));
		
		foreach(session()->get('cart') as $cartkey=>$product){
			$session_total += $product['total'];
			$product_shipping_amounts[$cartkey] = 0;
			
			$product_weight[$cartkey]['product_shipping'] = false;
			
			if($product['product_id'] == config('constants.CUSTOM_PRODUCT_ID')){
				$ship_detail = null;
				
				$product_weight[$cartkey]['product_shipping_type'] = 'CUSTOM_PRODUCT';
				$product_weight[$cartkey]['product_qty'] = $product['quantity'];
				$product_weight[$cartkey]['product_shipping_price'] = $product['shipping_price'];
				if($multiple){
					$product_weight[$cartkey]['address'] = $address[$cartkey];
				}
			}else{
				$ship_detail = ProductShipping::where('product_id',$product['product_id'])->first();
				
				$product_weight[$cartkey]['product_shipping_type'] = $product['object_data']['shipping_type'];
			}
			//pr($ship_detail);
			
			if(count($ship_detail) > 0){
				
				if($ship_detail->type == 'free_value' and $product['total'] < $ship_detail->min_value){
					if($product_weight[$cartkey]['product_shipping_type'] != 'free'){
						$product_weight[$cartkey]['product_shipping'] = true;
						$product_shipping = true;
					}
				}
				else if($ship_detail->type == 'paid'){
					if($product_weight[$cartkey]['product_shipping_type'] != 'free'){
						$product_weight[$cartkey]['product_shipping'] = true;
						$product_shipping = true;
					}
				}else if($ship_detail->type == 'flat'){
					$product_weight[$cartkey]['product_shipping_price'] = $product['object_data']['shipping_price'];

					foreach((array)@$product['object_data']['custom_option'] as $option){
						if(array_key_exists('flat_rate_additional_price',$option)){
							
							$additional_shipping = number_format($option['flat_rate_additional_price'],2,'.','');
							$product_weight[$cartkey]['product_shipping_price'] += $additional_shipping;
						}
					}
				}
				
				$product_weight[$cartkey]['product_reduce_price'] = $ship_detail->reduce_price;
				$product_weight[$cartkey]['product_additional_qty_price'] = $ship_detail->additional_qty_price;
				$product_weight[$cartkey]['product_qty'] = $product['quantity'];
				
				if($multiple){
					$product_weight[$cartkey]['address'] = $address[$cartkey];
				}
			}
			
			$weight = 0;
			if(!empty($product['object_data']['product_weight'])){
				$weight = number_format($product['object_data']['product_weight'],2,'.','');
			}else{
				if($product['product_id'] != config('constants.CUSTOM_PRODUCT_ID'))
					$weight = 1;
			}
			//echo $weight;die();
			
			if(array_key_exists('variants',$product) and !empty($product['variants'])){
				$db = ProductVariantPrice::select('shipping_weight','shipping_price')->where('product_id',$product['product_id']);
				$j = 1;
				foreach($product['variants'] as $key=>$val){
					$db->where('varient_id'.$j,$val);
					$j++;
				}

				$variant_weight = $db->first();				
				
				if(!empty($variant_weight->shipping_weight)){
					$weight += number_format($variant_weight->shipping_weight,2,'.','');
				}
				
				if(!empty($variant_weight->shipping_price) and $product_weight[$cartkey]['product_shipping_type'] == 'flat'){
					$additional_shipping = number_format($variant_weight->shipping_price,2,'.','');
					$product_weight[$cartkey]['product_shipping_price'] += $additional_shipping;
				}
			}

			
			
			if($product['object_data']['show_width_height'] == 1){
				$weight = $weight * ($product['object_data']['width']*$product['object_data']['height']);
			}
			
			foreach((array)@$product['object_data']['custom_option'] as $option){
				if(array_key_exists('weight',$option)){
					$wt = 0;
					if($option['price_type'] == 'area')
						$wt = $option['weight']*($product['object_data']['width']*$product['object_data']['height']);
					else if($option['price_type'] == 'parimeter')
						$wt = $option['weight']*(($product['object_data']['width']+$product['object_data']['height'])*2);
					else if($option['price_type'] == 'item')
						$wt = $option['weight'];
					
					$weight += number_format($wt,2,'.','');
				}
			}
			
			$weight = $weight * $product['quantity'];
			
			$product_weight[$cartkey]['weight'] = $weight;			
			
			if($product_weight[$cartkey]['product_shipping'])
				$total_weight += $product_weight[$cartkey]['weight'];
		}
		
		return array('total_weight'=>$total_weight,'product_weight'=>$product_weight,'product_shipping'=>$product_shipping,'product_shipping_amounts'=>$product_shipping_amounts,'session_total'=>$session_total,'multiple'=>$multiple,'address'=>$address);
	}

	public function OrderPendingWeight($data,$add_type = 'front'){
		$multiple = false;
		$detail = $this->front_address($data);
		$multiple = $detail['multiple'];
		$address = $detail['address'];
		
		$total_weight = 0;
		$order_products = array();
		$product_weight = array();
		$product_shipping_amounts = array();
		$product_shipping = false;
		$session_total = 0;
		
		$order = Orders::where('id',$data['order_id'])->with(['Products','orderProductOptions'])->first();
		//pr($order);die;

		foreach($order->Products as $product){
			$width = 0;
			$height = 0;
			$custome_options = array();
			$variant_options = array();
			
			$order_products[$product->id]['product_id'] = $product->product_id;
			$order_products[$product->id]['qty'] = $product->qty;
			$order_products[$product->id]['total'] = $product->total;
			$order_products[$product->id]['product_weight'] = $product->product_weight;
			$order_products[$product->id]['product_shipping'] = $product->product_shipping;

			foreach($order->orderProductOptions as $options){
				if($options->product_id == $product->product_id){
					if($options->custom_option_name == 'Width'){
						$width = $options->value;
					}else if($options->custom_option_name == 'Height'){
						$height = $options->value;
					}
					
					if($options->type == 2){
						foreach($product->product->variants as $variant){
							foreach($variant->variantValues as $value){
								if($value->value == $options->value && $product->id == $options->order_product_id)
									$variant_options[] = $value->id;
							}
						}
					}
					if(isset($options->optionDetail)){
						$option_keys = json_decode($options->optionDetail->option_keys,true);
						foreach($option_keys as $val){
							//pr($options->value);
							//pr($val);
							if(array_key_exists('value',$val) && $options->value == $val['value']){
								$val['price_type'] = $options->optionDetail->price_formate;
								$custome_options[$options->optionDetail->id] = $val;
							}
						}
					}
				}
			}//pr($custome_options);die;
			
			$session_total += $product->total;
			$product_shipping_amounts[$product->id] = 0;
			
			if($product->product_id == config('constants.CUSTOM_PRODUCT_ID')){
				$product->shipping = null;
				
				$product_weight[$product->id]['product_shipping_type'] = 'CUSTOM_PRODUCT';
				$product_weight[$product->id]['product_shipping'] = false;
				$product_weight[$product->id]['product_shipping_price'] = $product->product_shipping;
				
				$product_shipping_amounts[$product->id] = $product->product_shipping;
				
				if($multiple){
					$product_weight[$product->id]['address'] = $address[$product->id];
				}
			}
			//pr($ship_detail);
			
			if(count($product->shipping) > 0){
				$product_weight[$product->id]['product_shipping_type'] = $product->shipping->type;
				$product_weight[$product->id]['product_shipping'] = false;
				
				if($product->shipping->type == 'free_value' and $product['total'] < $product->shipping->min_value){
					if($product_weight[$product->id]['product_shipping_type'] != 'free'){
						$product_weight[$product->id]['product_shipping'] = true;
						$product_shipping = true;
					}
				}
				else if($product->shipping->type == 'paid'){
					if($product_weight[$product->id]['product_shipping_type'] != 'free'){
						$product_weight[$product->id]['product_shipping'] = true;
						$product_shipping = true;
					}
				}else if($product->shipping->type == 'flat'){
					if($product->product_shipping !="" && $product->product_shipping != 0)
						$product_weight[$product->id]['product_shipping_price'] = number_format($product->product_shipping / $product->qty,2,'.','');
					else
						$product_weight[$product->id]['product_shipping_price'] = $product->shipping->price;

					foreach($custome_options as $option){
						if(array_key_exists('flat_rate_additional_price',$option)){
							
							$additional_shipping = number_format($option['flat_rate_additional_price'],2,'.','');
							$product_weight[$product->id]['product_shipping_price'] += $additional_shipping;
						}
					}
				}
				
				$product_weight[$product->id]['product_reduce_price'] = $product->shipping->reduce_price;
				$product_weight[$product->id]['product_additional_qty_price'] = $product->shipping->additional_qty_price;
				$product_weight[$product->id]['product_qty'] = $product->qty;
				
				if($multiple){
					$product_weight[$product->id]['address'] = $address[$product->id];
				}
			}
			
			$weight = 0;
			if(!empty($product->product->shipping_weight)){
				$weight = number_format($product->product->shipping_weight,2,'.','');
			}else{
				if($product->product_id != config('constants.CUSTOM_PRODUCT_ID'))
					$weight = 1;
			}
			
			if(isset($product->product->variants) and !empty($product->product->variants)){
				
				$db = ProductVariantPrice::select('shipping_weight','shipping_price')->where('product_id',$product->product_id);
				$j = 1;
				foreach($variant_options as $val){
					$db->where('varient_id'.$j,$val);
					$j++;
				}

				$variant_weight = $db->first();				
				
				if(!empty($variant_weight->shipping_weight)){
					$weight += number_format($variant_weight->shipping_weight,2,'.','');
				}
				
				if(!empty($variant_weight->shipping_price) and $product_weight[$product->id]['product_shipping_type'] == 'flat'){
					$additional_shipping = number_format($variant_weight->shipping_price,2,'.','');
					$product_weight[$product->id]['product_shipping_price'] += $additional_shipping;
				}
			}
			
			
			if($product->product->show_width_height == 1){
				$weight = $weight * ($width*$height);
			}
			
			foreach($custome_options as $option){
				if(array_key_exists('weight',$option)){
					$wt = 0;
					if($option['price_type'] == 'area')
						$wt = $option['weight']*($width*$height);
					else if($option['price_type'] == 'parimeter')
						$wt = $option['weight']*(($width+$height)*2);
					else if($option['price_type'] == 'item' or $option['price_type'] == 'line_item')
						$wt = $option['weight'];
					
					$weight += number_format($wt,2,'.','');
				}
			}
			
			$weight = $weight * $product->qty;
			
			$product_weight[$product->id]['weight'] = $weight;
			
			if(array_key_exists('product_shipping',$product_weight[$product->id]) && $product_weight[$product->id]['product_shipping'])
				$total_weight += $product_weight[$product->id]['weight'];
		}
		/* echo $total_weight;
		pr($product_weight);
		die; */
		
		return array('total_weight'=>$total_weight,'product_weight'=>$product_weight,'product_shipping'=>$product_shipping,'product_shipping_amounts'=>$product_shipping_amounts,'session_total'=>$session_total,'multiple'=>$multiple,'address'=>$address,'order_products'=>$order_products);
	}
	
	public function edit_weight($product){
		$total_weight = 0;
		$product_weight = array();
		$product_shipping = false;
		$session_total = 0;
		
		$ship_detail = ProductShipping::where('product_id',$product['order_product_id'])->first();
		
		if(count($ship_detail) > 0){
			if($ship_detail->type == 'free_value' and $product['object_data']['total'] < $ship_detail->min_value){
				$product_weight[$product['order_product_table_id']]['product_shipping_type'] = 'free_value';
				$product_weight[$product['order_product_table_id']]['product_shipping'] = true;
			}
			else if($ship_detail->type == 'paid'){
				$product_weight[$product['order_product_table_id']]['product_shipping_type'] = 'paid';
				$product_weight[$product['order_product_table_id']]['product_shipping'] = true;
			}else if($ship_detail->type == 'flat'){
				$product_weight[$product['order_product_table_id']]['product_shipping_type'] = 'flat';
				$product_weight[$product['order_product_table_id']]['product_shipping_price'] = $product['object_data']['shipping_price'];
				
				foreach((array)@$product['object_data']['custom_option'] as $option){
					if(array_key_exists('flat_rate_additional_price',$option)){
						
						$additional_shipping = number_format($option['flat_rate_additional_price'],2,'.','');
						$product_weight[$product['order_product_table_id']]['product_shipping_price'] += $additional_shipping;
					}
				}
				
				$product_weight[$product['order_product_table_id']]['product_shipping'] = false;
			}else if($ship_detail->type == 'free'){
				$product_weight[$product['order_product_table_id']]['product_shipping_type'] = 'free';
				$product_weight[$product['order_product_table_id']]['product_shipping'] = false;
			}else{
				$product_weight[$product['order_product_table_id']]['product_shipping'] = false;
				$product_weight[$product['order_product_table_id']]['product_shipping_type'] = '';
			}
			
			$product_weight[$product['order_product_table_id']]['product_reduce_price'] = $ship_detail->reduce_price;
			$product_weight[$product['order_product_table_id']]['product_additional_qty_price'] = $ship_detail->additional_qty_price;
			$product_weight[$product['order_product_table_id']]['product_qty'] = $product['quantity'];
		}else{
			$product_weight[$product['order_product_table_id']]['product_shipping'] = false;
			$product_weight[$product['order_product_table_id']]['product_shipping_type'] = '';
		}
		
		$weight = 0;
		if(!empty($product['object_data']['product_weight'])){
			$weight = number_format($product['object_data']['product_weight'],2,'.','');
		}else{
			$weight = 1;
		}
		
		if(array_key_exists('variants',$product) and !empty($product['variants'])){
			$db = ProductVariantPrice::select('shipping_weight','shipping_price')->where('product_id',$product['order_product_id']);
			$j = 1;
			foreach($product['variants'] as $key=>$val){
				$db->where('varient_id'.$j,$val);
				$j++;
			}

			$variant_weight = $db->first();				
		
			if(!empty($variant_weight->shipping_weight)){
				$weight += number_format($variant_weight->shipping_weight,2,'.','');
			}
			
			if(!empty($variant_weight->shipping_price) and $product_weight[$product['order_product_table_id']]['product_shipping_type'] == 'flat'){
				$additional_shipping = number_format($variant_weight->shipping_price,2,'.','');
				$product_weight[$product['order_product_table_id']]['product_shipping_price'] += $additional_shipping;
			}
		}
		
		if($product['object_data']['show_width_height'] == 1){
			$weight = $weight * ($product['object_data']['width']*$product['object_data']['height']);
		}
		
		foreach($product['object_data']['custom_option'] as $option){
			if(array_key_exists('weight',$option)){
				$wt = 0;
				if($option['price_type'] == 'area')
					$wt = $option['weight']*($product['object_data']['width']*$product['object_data']['height']);
				else if($option['price_type'] == 'parimeter')
					$wt = $option['weight']*(($product['object_data']['width']+$product['object_data']['height'])*2);
				else if($option['price_type'] == 'item')
					$wt = $option['weight'];
				
				$weight += number_format($wt,2,'.','');
			}
		}
		
		$weight = $weight * $product['quantity'];
		
		$product_weight[$product['order_product_table_id']]['weight'] = $weight;
		
		if($product_weight[$product['order_product_table_id']]['product_shipping'])
			$total_weight += $product_weight[$product['order_product_table_id']]['weight'];
	
		return array('total_weight'=>$total_weight,'product_weight'=>$product_weight);
	}
	
	public function admin_address($data){
		$multiple = false;
		$states = State::where('status',1)->pluck('stateName','stateCode')->all();
		$address = array();
		if($data['customer'] == 0 && ($data['same_as_billing'] == 1 or $data['same_as_billing'] == '1')){
			$address['zipcode'] = $data['billing_zipcode'];
			$address['stateCode'] = $data['billing_state'];
			$address['state'] = $states[$data['billing_state']];
		}else if($data['customer'] != 0 && ($data['same_as_billing'] == 1 or $data['same_as_billing'] == '1')){
			if($data['billing_add_option'] == 0 or $data['billing_add_option'] == '0'){
				$address['zipcode'] = $data['billing_zipcode'];
				$address['stateCode'] = $data['billing_state'];
				$address['state'] = $states[$data['billing_state']];
			}else{
				$detail = UserAddress::where('id',$data['billing_add_option'])->first();
				$address['zipcode'] = $detail->zipcode;
				$address['stateCode'] = $detail->state;
				$address['state'] = $states[$detail->state];
			}
		}else{
			if($data['multiple_shipping'] != 0){
				foreach($data['multi'] as $key=>$val){
					if($val['option'] != 0){
						$detail = UserAddress::where('id',$val['option'])->first();
						$address[$key]['zipcode'] = $detail->zipcode;
						$address[$key]['stateCode'] = $detail->state;
						$address[$key]['state'] = $states[$detail->state];
					}else{
						$address[$key]['zipcode'] = $val['shipping_zipcode'];
						$address[$key]['stateCode'] = $val['shipping_state'];
						$address[$key]['state'] = $states[$val['shipping_state']];
					}
					$address[$key]['product_id'] = $key;
				}
				$multiple = true;
			}else{
				if($data['shipping_add_option'] == 0){
					$address['zipcode'] = $data['shipping_zipcode'];
					$address['stateCode'] = $data['shipping_state'];
					$address['state'] = $states[$data['shipping_state']];
				}else{
					$detail = UserAddress::where('id',$data['shipping_add_option'])->first();
					$address['zipcode'] = $detail->zipcode;
					$address['stateCode'] = $detail->state;
					$address['state'] = $states[$detail->state];
				}
			}
		}
		return array('multiple'=>$multiple,'address'=>$address);
	}
	
	public function front_address($data){
		$multiple = false;
		$states = State::where('status',1)->pluck('stateName','stateCode')->all();
		$address = array();
		$i = 1;
		if($data['same_as_billing'] == 1 or $data['same_as_billing'] == '1'){
			if($data['billing_type'] == 0 or $data['billing_type'] == '0'){
				$address['zipcode'] = $data['billing_zipcode'];
				$address['stateCode'] = $data['billing_state'];
				$address['state'] = $states[$data['billing_state']];
			}else{
				$detail = UserAddress::where('id',$data['billing_type'])->first();
				$address['zipcode'] = $detail->zipcode;
				$address['stateCode'] = $detail->state;
				$address['state'] = $states[$detail->state];
			}
		}else{
			if($data['shipping_type'] == 'multiple'){
				foreach($data['multi'] as $key=>$val){
					if($val['shippingAddress'] != 0 && !empty($val['shippingAddress']) && $val['shippingAddress'] != ''){
						$detail = UserAddress::where('id',$val['shippingAddress'])->first();
						$address[$key]['zipcode'] = $detail->zipcode;
						$address[$key]['stateCode'] = $detail->state;
						$address[$key]['state'] = $states[$detail->state];
					}else{
						$address[$key]['zipcode'] = $val['zipcode'];
						$address[$key]['stateCode'] = $val['state'];
						$address[$key]['state'] = $states[$val['state']];
					}
					
					$address[$key]['product_id'] = $key;
				}
				$multiple = true;
			}
			else if($data['shipping_type'] == '0'){
				$address['zipcode'] = $data['shipping_zipcode'];
				$address['stateCode'] = $data['shipping_state'];
				$address['state'] = $states[$data['shipping_state']];
			}else if(!empty($data['selectAddress'])){
				$detail = UserAddress::where('id',$data['selectAddress'])->first();
				$address['zipcode'] = $detail->zipcode;
				$address['stateCode'] = $detail->state;
				$address['state'] = $states[$detail->state];
			}
		}
		return array('multiple'=>$multiple,'address'=>$address);
	}
}