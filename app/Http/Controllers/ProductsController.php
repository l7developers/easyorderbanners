<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use App\Products;
use App\Category;
use App\ProductVariant;
use App\ProductOptions;
use App\Reviews;
use \App\Helpers\UPSShipping;
use \App\Helpers\CalculateShippingWeight;

class ProductsController extends Controller
{
    public function __construct(){
		
        $this->middleware('auth',['except' => ['detail','send_quote','get_shipping','shop']]);
    }
	
    public function detail($slug=null,Request $request){
		//pr(session()->get('cart'));die;
		//session()->forget('cart');
		$product=Products::where('slug',$slug)->with('Catgory','Images')->first();
		
		$reviews = Reviews::where('product_id',$product->id);
		
		$related_products = Products::where('id','!=',$product->id)->where('category_id',$product->category_id)->with('product_image')->get();
		$options = ProductOptions::where('product_id',$product->id)->with('CustomOption')->get();
		$options_array = array();
		$i = 1;
		foreach($options as $val){
			$values = json_decode($val->CustomOption->option_keys,true);
			$options_array[$val->CustomOption->field_group][$i]['id'] = $val->CustomOption->id;
			$options_array[$val->CustomOption->field_group][$i]['name'] = $val->CustomOption->name;
			$options_array[$val->CustomOption->field_group][$i]['type'] = $val->CustomOption->option_type;
			$options_array[$val->CustomOption->field_group][$i]['description'] = $val->CustomOption->description;
			$options_array[$val->CustomOption->field_group][$i]['free'] = $val->CustomOption->free;
			$options_array[$val->CustomOption->field_group][$i]['values'] = $values;
			$i++;
		}
		$pageTitle = $product->name;		
		return view('product/detail',compact('pageTitle','product','options_array','related_products'));
    }
	
	public function send_quote(Request $request){
		$res['status'] = '';
		$res['msg'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			$object = $data['object'];
			$product_form = '';
			parse_str($data['product_form'], $product_form);
			parse_str($data['form'], $data);
			//pr($product_form);
			//pr($object);
			//pr($data);die;
			
			$validArr = [
						'email' => 'required|string|email|max:255',
						'name' => 'required|string|max:255',
						//'message' => 'required',
						];
			$validation = Validator::make($data, $validArr);
			
			$validation->sometimes(['zipcode','shipping_option'], 'required', function($input){
				return $input->quote_type == 'shipping';
			});
			
			if ($validation->passes()){
				$str = 'Product Name : <a href="'.url($product_form['product_slug']).'">'.$object['product_name'].'</a><br/><br/>';
				$str .= 'Quantity : '.$object['quantity'].'<br/>';
				if($object['show_width_height'] == 1){
					$str .= 'Width : '.$object['width'].'(Feet)<br/>';
					$str .= 'Height : '.$object['height'].'(Feet)<br/>';
				}
				if(!empty($object['custom_option'])){
					$str .= 'Product Options : <ul>';
					if(array_key_exists('variants',$product_form)){
						foreach($product_form['variants'] as $key1=>$variant){
							$detail = ProductVariant::where('id',$key1)->with('variantValues')->first();
							//pr($detail);
							foreach($detail->variantValues as $value){
								if($value->id == $variant){
									$str .= "<li>".ucwords($detail->name).": ".ucwords($value->value)."</li>";
								}
							}
						}
					}
					foreach($object['custom_option'] as $key=>$val){
						if($key != 'turnaround_time'){
							$value = explode('__',$val['value']);
							$str .= '<li>'.$val['name'].' : '.$value[0].'</li>';
						}else{
							$str .= '<li>Turnaround Time : '.$val['value'].'</li>';
						}
					}
					$str .= '</ul>';
				}
				
				$detail = array();
				$shipping_rate = 0;
				$check = true;
				if($data['quote_type'] == 'shipping'){
					$shipping_weight = new CalculateShippingWeight();
					$detail = $shipping_weight->product_weight($product_form,$object);
					//pr($detail);
					if($detail['product_shipping']){
						$shipping = new UPSShipping();
						$validate = $shipping->validateAddress($data['zipcode']);
						if($validate['status']){
							$rate_detail = $shipping->RateCalculate($data['zipcode'],$detail['weight'],$data['shipping_option']);
							//pr($rate_detail);
							$shipping_rate = number_format($rate_detail['res']->MonetaryValue,2,'.','');
						}else{
							$check = false;
							$res['msg'] = $validate['msg'];
						}
					}else{
						if($detail['product_shipping_type'] == 'flat'){
							if($detail['product_reduce_price'] == 1){
								$qty = $detail['product_qty'] - 1;
								$price = $detail['product_shipping_price']+($qty*$detail['product_additional_qty_price']);
								$shipping_rate = number_format($price,2,'.','');
							}else{
								$shipping_rate = $detail['product_qty']*(number_format($detail['product_shipping_price'],2,'.',''));
							}
						}
					}
					$str .= 'Sub Total : $'.$object['gross_total'].'<br/>';
					if($object['qty_discount'] > 0){
						$str .= 'Quantity Discount : $'.$object['qty_discount'].'('.$object['discount_per'].'%)<br/>';
					}
					if($detail['product_shipping_type'] == 'free'){
						$str .= 'Shipping : Free Shipping<br/>';
					}
					elseif($detail['product_shipping_type'] == 'free'){
						$str .= 'Shipping : $'.$shipping_rate.'<br/>';
					}
					else{
						$str .= 'Shipping : $'.$shipping_rate.'('.config('constants.Shipping_option.'.$data['shipping_option']).')<br/>';
					}
				}else{
					if($object['qty_discount'] > 0){
						$str .= 'Sub Total : $'.$object['gross_total'].'<br/>';
						$str .= 'Quantity Discount : $'.$object['qty_discount'].'('.$object['discount_per'].'%)<br/>';
					}
				}
				
				$str .= 'Total : $'.($object['total']+$shipping_rate).'<br/>';
				//echo $str;die;
				$params = array('slug'=>'estimate_from_eob_to_user',
							'to'=>$data['email'],
							//'cc'=>[config('constants.ADMIN_MAIL')],
							'bcc'=>config('constants.store_email'),
							'params'=>array(
										'{{name}}'=>$data['name'],
										'{{message}}'=>$data['message'],
										'{{detail}}'=>$str,
										'{{store_name}}'=>config('constants.store_name'),
										'{{store_phone_number}}'=>config('constants.store_phone_number'),
										'{{store_email}}'=>config('constants.store_email'),
										));
				if($check){
					$mail = parent::sendMail($params);
					$res['status'] = 'success';
				}
			}else{
				$res['errors'] = $validation->errors();
			}
		}
		return json_encode($res);
	}
	
	public function shop(Request $request){
		$pageTitle = "Shop";
		
		$categories = [];
		$categoriesData = Category::where('parent_id', 0)->where('status', 1)->with('child')->orderBy('name','ASC')->get();		
		foreach($categoriesData as $val){
			$categories[$val->id] = $val->name;
			if(count($val->child) > 0){
				foreach($val->child as $child){
					$categories[$child->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child->name;

					if(count($child->child) > 0){
						foreach($child->child as $child2){
							$categories[$child2->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child2->name;	
						}
					}	
				}
			}
		}
		
		
		$productList = Products::where('status',1)->select('id','name','image','image_title')->get();
		
		$limit = config('constants.FRONT_PAGE_LIMIT');
		
		$db = Category::where('parent_id', 0)->where('status', 1)->with('child');		
		
		$db->with('products');
		
		$db->with('childProducts');
		
		$db->orderBy('weight','ASC');
		
		if($request->isMethod('get')){
			$data = $request->all();
			//pr($data);die;
			if(isset($data['search']) && !empty($data['search'])){
				$catIds = [];
				
				$categoriesSearch = Category::select('id')->orWhere('name','like','%'.$data['search'].'%')->get();
				foreach($categoriesSearch as $val){
					$catIds[] = $val->id;
				}
				
				$productsSearch = Products::select('category_id')->orWhere('name','like','%'.$data['search'].'%')->get();
				foreach($productsSearch as $val){
					$catIds[] = $val->category_id;
				}
				
				$db->whereIn('id',$catIds);
			}else if(isset($data['category']) && !empty($data['category']) && $data['category'] != 0){
				$catIds = [];
				$catIds[] = $data['category'];
				/* $cat = Category::where('id',$data['category'])->with('child')->first();
				$catIds[] = $cat->id;
				if(count($cat->child) > 0){
					foreach($cat->child as $child){
						$catIds[] = $child->id;
						if(count($child->child) > 0){
							foreach($child->child as $child2){
								$catIds[] = $child2->id;
							}
						}	
					}
				} */
				//pr($catIds);die;
				$db->whereIn('id',$catIds);
			}
		}
		
		$categoryList = $db->paginate($limit);
		
		//pr($categoryList->toArray());die;
		
		return view('product/shop',compact('pageTitle','categoryList','productList','categories'));
	}
	
	public function shop_old(Request $request){
		$pageTitle = "Shop";
		
		$categories = [];
		$categoriesData = Category::where('parent_id', 0)->where('status', 1)->with('child')->orderBy('name','ASC')->get();		
		foreach($categoriesData as $val){
			$categories[$val->id] = $val->name;
			if(count($val->child) > 0){
				foreach($val->child as $child){
					$categories[$child->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child->name;

					if(count($child->child) > 0){
						foreach($child->child as $child2){
							$categories[$child2->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child2->name;	
						}
					}	
				}
			}
		}
		
		
		$productList = Products::where('status',1)->select('id','name','image','image_title')->get();
		
		$limit = config('constants.FRONT_PAGE_LIMIT');
		
		$db = Category::where('status',1);
		
		$db->with('products');
		
		$db->orderBy('name','ASC');
		
		if($request->isMethod('get')){
			$data = $request->all();
			//pr($data);die;
			if(isset($data['search']) && !empty($data['search'])){
				$catIds = [];
				
				$categoriesSearch = Category::select('id')->orWhere('name','like','%'.$data['search'].'%')->get();
				foreach($categoriesSearch as $val){
					$catIds[] = $val->id;
				}
				
				$productsSearch = Products::select('category_id')->orWhere('name','like','%'.$data['search'].'%')->get();
				foreach($productsSearch as $val){
					$catIds[] = $val->category_id;
				}
				
				$db->whereIn('id',$catIds);
			}else if(isset($data['category']) && !empty($data['category']) && $data['category'] != 0){
				$catIds = [];
				$cat = Category::where('id',$data['category'])->with('child')->first();
				$catIds[] = $cat->id;
				if(count($cat->child) > 0){
					foreach($cat->child as $child){
						$catIds[] = $child->id;
						if(count($child->child) > 0){
							foreach($child->child as $child2){
								$catIds[] = $child2->id;
							}
						}	
					}
				}
				
				$db->whereIn('id',$catIds);
			}
		}
		
		$categoryList = $db->paginate($limit);
		
		//pr($productList->toArray());die;
		
		return view('product/shop',compact('pageTitle','categoryList','productList','categories'));
	}

    /**
     * @param Request $request
     * @return string
     */
	public function get_shipping(Request $request)
    {
		$res['status'] = '';
		$res['msg'] = '';
		if($request->isMethod('post')) {
			$data = $request->all();
			$object = $data['object'];
			$product_form = '';
			
			parse_str($data['product_form'], $product_form);
			parse_str($data['form'], $data);
			
			$validArr = [
                'zipcode' => 'required',
                'shipping_option' => 'required',
            ];

			$validation = Validator::make($data, $validArr);
			
			if ($validation->passes()) {
				
				$detail = array();
				$shipping_rate = 0;
				$check = true;
				
				$shipping_weight = new CalculateShippingWeight();
				$detail = $shipping_weight->product_weight($product_form, $object);

				//pr($detail);die();
				$res['weight'] = $detail['weight'];
				
				if($detail['product_shipping']){
					$shipping = new UPSShipping();
					$validate = $shipping->validateAddress($data['zipcode']);
					if($validate['status']){
						$rate_detail = $shipping->RateCalculate($data['zipcode'],$detail['weight'],$data['shipping_option']);
						//pr($rate_detail);die();
						if(isset($rate_detail['res']))
						{	
							//$rate = $rate_detail['res']->MonetaryValue * $object['quantity'];
							$rate = $rate_detail['res']->MonetaryValue;

							$shipping_rate = priceFormat($rate);

							$res['status'] = 'success';
						}
						else
						{
							$check = false;
							//$res['msg'] = $rate_detail['msg'];
							$res['status'] = 'fail';							
							$res['msg'] = 'Please enter a valid location.';
							$res['detail'] = $rate_detail['msg'];
							return json_encode($res);
						}
					}else{
						$check = false;
						//$res['msg'] = $validate['msg'];
						$res['status'] = 'fail';
						$res['msg'] = 'Please enter a valid location.';
						$res['detail'] = 'Please enter a valid location.';						
						return json_encode($res);
					}
				}else{
					if($detail['product_shipping_type'] == 'flat'){
						if($detail['product_reduce_price'] == 1){
							$qty = $detail['product_qty'] - 1;
							$price = $detail['product_shipping_price']+($qty*$detail['product_additional_qty_price']);
							$shipping_rate = priceFormat($price);
						}else{
							$shipping_rate = $detail['product_qty']*(priceFormat($detail['product_shipping_price']));
						}
						$res['status'] = 'success';
					}
				}

				$productData = Products::where('id',$product_form['product_id'])->first(); 

				//pr($detail);die;
				if($detail['product_shipping_type'] == 'free'){
					$res['detail'] = '<b>Shipping Fee</b> : Free Shipping ('.$productData->turnaround_time.')';
					$res['status'] = 'success';
				}
				elseif($detail['product_shipping_type'] == 'flat'){
					$res['detail'] = '<b>Shipping Fee</b> : $'.$shipping_rate.' Flat Rate Shipping ('.$productData->turnaround_time.')';
					$res['status'] = 'success';
				}
				else{
					$res['detail'] = '<b>Shipping Fee</b> : $'.$shipping_rate.'('.config('constants.Shipping_option.'.$data['shipping_option']).')';
					$res['status'] = 'success';
				}
				
			}else{
				$res['errors'] = $validation->errors();
			}
		}
		return json_encode($res);
	}
	
	public function test(Request $request){
		$data = $request->all();
		pr($data);
	}
}
