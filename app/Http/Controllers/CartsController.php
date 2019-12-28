<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use App\Products;
use App\ProductOptions;
use App\Coupons;
use App\Orders;
use App\Discounts;

use \App\Helpers\FunctionsHelper;

class CartsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except' => ['add','view','update','savecomment','applyCoupon','delete','productClone']]);
    }
	
	public function add(Request $request){
		$responce['status'] = '';
		$responce['res'] = '';
		if($request->isMethod('post')){
			$request_data = $request->all();
			
			$object = null;
			if(array_key_exists('object',$request_data)){
				$object = $request_data['object'];
				parse_str($request_data['form'], $data);
			}else{
				$data = $request_data;
			}
			/* pr($object);
			pr($data);
			die; */
			
			$products = session()->get('cart');

			$cartkey = count($products);			
			
			if($data['cartkey'] != null and $cartkey != ''){
				$cartkey =  $data['cartkey'];
			}		
			
			$products[$cartkey]['product_id'] = $data['product_id'];				
			$products[$cartkey]['shipping_type'] = $object['shipping_type'];				
			$products[$cartkey]['price_default'] =  number_format((float)($data['price_default']), 2, '.', '');			
			$products[$cartkey]['price'] =  number_format((float)($object['gross_price']), 2, '.', '');
			$products[$cartkey]['quantity'] = $data['quantity'];
			$products[$cartkey]['gross_total'] =  number_format((float)($object['gross_total']), 2, '.', '');
			$products[$cartkey]['qty_discount'] =  number_format((float)($object['qty_discount']), 2, '.', '');
			$products[$cartkey]['total'] =  number_format((float)($object['total']), 2, '.', '');
			$products[$cartkey]['sales_tax'] =  0.00;
			
			if(array_key_exists('variants',$data))
			$products[$cartkey]['variants'] = $data['variants'];
			
			$products[$cartkey]['project_name'] = '';
			$products[$cartkey]['comments'] = '';
			
			$products[$cartkey]['options'] = @$data['options'];
			$products[$cartkey]['object_data'] = $object;
			
			session()->put('cart', $products);				
			
			$session_total = 0;
			foreach(session()->get('cart') as $k1=>$product){
				$session_total += $product['total'];						
			}			
			
			$session_total = number_format($session_total,2,'.','');
			
			
			session()->put('carttotal.gross', $session_total);
			session()->put('carttotal.discount', 0);
			session()->put('carttotal.discount_code', '');
			session()->put('carttotal.shipping_option', '');			
			session()->put('shipping_option', '');			
			session()->put('carttotal.shipping', 0);
			session()->put('carttotal.sales_tax', 0);
			session()->put('carttotal.free_shipping', 0);
			$total = (session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping'));
			session()->put('carttotal.total', number_format($total,2,'.',''));

			//$responce = $this->getCart();
			
			$responce['status'] = 'success';
		}
		return json_encode($responce);
	}
	
	public function productClone(Request $request){
		$res['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			
			$products = session()->get('cart');
			
			$cartkey = count($products);
			
			$products[$cartkey] = $products[$data['key']];
			session()->put('cart', $products);
			
			$session_total = 0;
			foreach(session()->get('cart') as $k1=>$product){
				$session_total += $product['total'];						
			}			
			
			$session_total = number_format($session_total,2,'.','');
			
			
			session()->put('carttotal.gross', $session_total);
			session()->put('carttotal.discount', 0);
			session()->put('carttotal.discount_code', '');
			session()->put('carttotal.shipping_option', '');			
			session()->put('shipping_option', '');			
			session()->put('carttotal.shipping', 0);
			session()->put('carttotal.sales_tax', 0);
			session()->put('carttotal.free_shipping', 0);
			$total = (session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping'));
			session()->put('carttotal.total', number_format($total,2,'.',''));
			
			
			$res['status'] = 'success';
			$res['html'] = view('cart/clone')->render();
		}
		return json_encode($res);
	}
	
	public function delete(Request $request){
		$responce['status'] = 'success';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			$products = session()->get('cart');
			unset($products[$data['key']]);
			session()->put('cart', $products);
			session()->put('carttotal.shipping', 0);
			$products = session()->get('cart');
			session()->forget('carttotal.shipping');
			session()->forget('shipping_option');
			
			$responce = $this->getCart();
		}
		//pr($responce);
		//die();
		return json_encode($responce);
	}
	
	public function getCart(){

		$responce['res'] = '';
		$responce['status'] = 'success';
		$responce['session_count'] = count(session()->get('cart'));
		$session_total = 0;
		if(session()->has('cart')){
			$i = 1;
			if(count(session()->get('cart')) > 0){
				foreach(session()->get('cart') as $k1=>$product){
					$session_total += $product['total'];
					$product_detail = Products::where('id',$product['product_id'])->with('product_image')->first();
					$img_url = url('/public/img/front/img.jpg');
					if(@getimagesize(url('public/uploads/product/'.$product_detail->image))){
						$img_url = url('/public/uploads/product/'.$product_detail->image);
					}
					$responce['res'] .= '<li><div class="product"><a href="javascript:void(0)" class="remove_product" data="'.$k1.'"><i class="fas fa-times"></i></a><span class="img"><img src="'.$img_url.'" alt=""></span><span class="right"><span class="title">'.$product_detail->name.'</span><br/><span class="cart_product_'.$k1.'">Qty : '.$product['quantity'].'</span><div class="clearfix"></div><span class="cart_product_amount_'.$k1.'">'.priceFormat($product['total']).'</span></span></div><div class="clearfix"></div></li>';
					
					$i++;
				}
			}else{
				$responce['res'] .= '<li><div class="product"><span>Cart is empty</span></div></li>';
				session()->forget('cart');
				session()->forget('orderAddresses');
				session()->forget('comments');
				session()->forget('carttotal');
				session()->forget('products_weight');
				session()->forget('product_ship');
			}
		}
		
		$session_total = number_format($session_total,2,'.','');
		session()->put('carttotal.gross', $session_total);
		
		$cart_total = (session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping'));
		$cart_total = number_format($cart_total,2,'.','');
		session()->put('carttotal.total', $cart_total);

		$session_total = priceFormat($session_total);
		
		
		$responce['res'] .= '<li class="boot_opction"><div class="top_text"><b>Subtotal:<span class="cart_total">$'.$session_total.'</span></b></div><div class="bot_btns"><ul><li><a href="'.url('cart').'" class="btn parpal_btn">View Cart</a></li><li><a href="'.url('cart').'" class="btn blue_btn">Checkout</a></li></ul></div></li>';
		
		$responce['session_total'] = $session_total;
		
		return $responce;
	}
	
	public function view(Request $request){
		
		//session()->forget('cart');
		//pr(session()->get('cart'));die;
		//FunctionsHelper::sendOrderReceipt(25);		
		
		if($request->session()->has('cart')){
			if(count(session()->get('cart')) > 0){
				$pageTitle = 'View Cart';
				$products = session()->get('cart');
				return view('cart/view',compact('pageTitle','products'));
			}else{
				session()->forget('orderAddresses');
				session()->forget('carttotal');
				session()->forget('cart');
				session()->forget('products_weight');
				session()->forget('product_ship');
				
				\Session::flash('error', 'Your Cart Is Empty.');
				//return redirect('/');
			}
		}else{
			session()->forget('cart');
			session()->forget('orderAddresses');
			session()->forget('comments');
			session()->forget('carttotal');
			session()->forget('products_weight');
			session()->forget('product_ship');
			
			\Session::flash('error', 'Your Cart Is Empty.');
			return redirect('/');
		}
	}
	
	public function update(Request $request){
		$responce['status'] = '';
		$responce['res'] = array();
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			$products = session()->get('cart');
			
			$cart_gross = 0;
			foreach($data['qty'] as $key=>$val){
				$qty = $val;
				$discount = array();
				$discount_all = Discounts::where('status',1)->orderBy('quantity','ASC')->get();
				foreach($discount_all as $value){
					if(empty($value->products)){
						$discount[$value->quantity] = $value->percent;
					}else{
						$ids = explode(',',$value->products);
						if(in_array($products[$key]['product_id'],$ids)){
							$discount[$value->quantity] = $value->percent;
						}
					}
				}
				$discount_per = 0;
				foreach($discount as $k=>$d){
					if($k <= $qty)
						$discount_per = number_format($d,2,'.','');
				}
				
				$gross_price = $products[$key]['price'];
				$gross_total = $gross_price*$qty;
				$gross_total = number_format($gross_total,2,'.','');
				$qty_discount = ($gross_total*$discount_per)/100;
				$total = $gross_total-$qty_discount;
				$total = number_format($total,2,'.','');
				
				if($products[$key]['object_data']['productMinPrice'] != 0 && $total < $products[$key]['object_data']['productMinPrice']){
					$total = number_format($products[$key]['object_data']['productMinPrice'],2,'.','');
				}
				
				$products[$key]['quantity'] = $qty;
				$products[$key]['gross_total'] = $gross_total;
				$products[$key]['qty_discount'] = $qty_discount;
				$products[$key]['total'] = $total;
				
				$products[$key]['object_data']['quantity'] = $qty;
				$products[$key]['object_data']['gross_total'] = $gross_total;
				$products[$key]['object_data']['discount_per'] = $discount_per;
				$products[$key]['object_data']['qty_discount'] = $qty_discount;
				$products[$key]['object_data']['total'] = $total;
				
				$cart_gross += $total;
				$responce['res']['qty'][$key]['qty'] = $qty;
				$responce['res']['qty'][$key]['total'] = $total;
			}
			//pr($products);die;
			session()->put('cart', $products);
			
			$cart_gross = number_format($cart_gross,2,'.','');
			session()->put('carttotal.gross', $cart_gross);
			
			$cart_total = (session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping'));
			$cart_total = number_format($cart_total,2,'.','');
			session()->put('carttotal.total', $cart_total);
			
			$responce['status'] = 'success';
			$responce['res']['cart']['sub_total'] = $cart_gross;
			$responce['res']['cart']['total'] = $cart_total;
		}
		return json_encode($responce);
	}
	
	public function savecomment(Request $request){
		$responce['status'] = '';
		$responce['res'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			$products = session()->get('cart');
			
			$products[$data['key']][$data['field']] = $data['field_value'];
			
			session()->put('cart', $products);
			
			$responce['status'] = 'success';
			$responce['res'] = $data['field_value'];
			$responce['key_id'] = $data['key_id'];
		}
		return json_encode($responce);
	}
	
	public function applyCoupon(Request $request){
		$res['status'] = '';
		$res['msg'] = '';
		$res['code_apply'] = 0;
		if($request->isMethod('post')){
			$code = $request->all()['code'];
			$gross_total = session()->get('carttotal.gross');
			$coupon_available = 1;
			
			$detail = Coupons::where('code',$code)->where('status',1)->first();
			
			if(count($detail) > 0 )
				$expire_date = strtotime(date('Y-m-d',strtotime($detail->expire_date)));
			else
				$expire_date = strtotime(date('Y-m-d'));
			
			$today = strtotime(date('Y-m-d'));
			
			$couponApply = 1;
			if(count($detail) < 1 ){
				$couponApply = 0;
				$res['msg'] = 'You enter invalid coupon code';
			}
			else if($today > $expire_date){
				$couponApply = 0;
				$res['msg'] = 'This coupon code has expired';
			}
			else if(!isset(\Auth::user()->id)){
				$couponApply = 0;
				$res['msg'] = 'Please login for use this coupon code.';
			}
			else{
				if(!empty($detail->users) and isset(\Auth::user()->id)){
					$coupon_user_ids = explode(',',$detail->users);
					if(!in_array(\Auth::user()->id,$coupon_user_ids)){
						$couponApply = 0;
						$res['msg'] .= 'This coupon code not valid for you.<br/>';
					}
				}
				if($gross_total < $detail->min_cart){
					$couponApply = 0;
					$res['msg'] .= 'This coupon code apply minimum amount $'.$detail->min_cart.' .<br/>';
				}
				
				if($detail->single_time == 1 or $detail->single_time == '1'){
					$order_detail = Orders::where('user_id',\Auth::user()->id)->where('discount_code',$code)->first();
					if(count($order_detail) > 0){
						$couponApply = 0;
						$res['msg'] .= 'You have already used this coupon code.<br/>';
					}
				}
			
				// Below code for check coupon products are available in cart or not
				
				if(!empty($detail->products)){
					$coupon_products_ids = explode(',',$detail->products);
					$product_id_exist = false;
					foreach(session()->get('cart') as $cart){
						if(in_array($cart['product_id'],$coupon_products_ids)){
							$product_id_exist = true;
						}
					}
					if(!$product_id_exist){
						$couponApply = 0;
						$res['msg'] .= 'This coupon code not available for your cart products.<br/>';
					}
				}
			}
			
			if($couponApply){
				if($detail->type == 'amount'){
					$discount = $detail->type_value;	
					$discount = number_format($discount,2,'.','');
					session()->put('carttotal.discount', $discount);
					session()->put('carttotal.discount_code', $code);
					$total = number_format((session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping')),2,'.','');
					if($total < 0){
						$total = 0.00;
					}
					session()->put('carttotal.total', $total);
					session()->put('carttotal.free_shipping', 0);
					
					$res['code_apply'] = 1;
					$res['code_type'] = 'amount';
					$res['gross'] = $gross_total;
					$res['discount_amount'] = $discount;
					$res['total'] = $total;
					$res['msg'] = 'This coupon code applied on your order.';
				}
				else if($detail->type == 'percent'){
					$discount = number_format((($gross_total*$detail->type_value)/100),2,'.','');
					
					if(!empty($detail->max_discount) && $discount > $detail->max_discount){
						$discount = $detail->max_discount;
					}
					
					$discount = number_format($discount,2,'.','');
					session()->put('carttotal.discount', $discount);	
					session()->put('carttotal.discount_code', $code);
					session()->put('carttotal.free_shipping', 0);
					session()->put('carttotal.shipping', 0.00);
					
					$total = number_format((session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping')),2,'.','');
					if($total < 0){
						$total = 0.00;
					}
					session()->put('carttotal.total', $total);
													
					$res['code_apply'] = 1;
					$res['code_type'] = 'percent';
					$res['gross'] = $gross_total;
					$res['discount_amount'] = $discount;
					$res['total'] = $total;
					$res['msg'] = 'This coupon code applied on your order.';
				}else if($detail->type == 'free_shipping'){
					$coupon_products_ids = explode(',',$detail->products);
						
					$cart_products = session()->get('cart');
					$cart_count = count(session()->get('cart'));
					
					foreach($cart_products as $key=>$product){
						if(in_array($product['product_id'],$coupon_products_ids)){
							$cart_products[$key]['object_data']['shipping_type'] = 'free';
							$cart_count--;
						}else{
							if($product['object_data']['shipping_type'] == 'free'){
								$cart_count--;
							}
						}
					}
					
					session()->put('cart', $cart_products);
					
					session()->put('carttotal.discount_code', $code);
					$total = number_format((session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping')),2,'.','');
					if($total < 0){
						$total = 0.00;
					}
					$res['code_apply'] = 1;
					$res['code_type'] = 'free_shipping';
					$res['gross'] = $gross_total;
					$res['discount_amount'] = 0;
					$res['total'] = $total;
					
					if($cart_count == 0){
						session()->put('carttotal.free_shipping', 1);
						$res['msg'] = 'Free shipping applied on your order.';
					}else{								
						session()->put('carttotal.free_shipping', 0);
						$res['msg'] = 'Free shipping applied on your cart products.';
					}
				}
				
			}else{
				session()->put('carttotal.discount_code', '');
				session()->put('carttotal.discount', 0);
			}
			$res['status'] = 'success';
		}
		return json_encode($res);
	}
}
