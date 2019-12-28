<?php

namespace App\Http\Controllers;

use App\Jobs\UploadArtwork;
use App\OrderFiles;
use App\UploadQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\UserAddress;
use App\Products;
use App\ProductOptions;
use App\Orders;
use App\OrderProducts;
use App\OrderProductOptions;
use App\OrderAddress;
use App\ProductVariant;
use App\State;
use App\UserCards;
use App\ProductShipping;
use Form;
use \App\Helpers\PaymentAuthorize;
use \App\Helpers\FunctionsHelper;
use \App\Helpers\UPSShipping;
use \App\Helpers\CalculateShippingWeight;
use \App\Helpers\TflowHelper;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Illuminate\Support\Facades\Artisan;


class CheckoutsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except' => ['Authorizepayment','invoice_receipt']]);
    }
	
	public function checkout(Request $request){
		$pageTitle = "Checkout";
		if($request->session()->has('cart')){
			if(count(session()->get('cart')) > 0){
				$pageTitle = 'View Cart';
				$states = State::where('status',1)->pluck('stateName','stateCode')->all();
				
				$user_cards = UserCards::where('user_id',Auth::user()->id)->get();
				$card_options = array();
				$card_detail = array();
				foreach($user_cards as $card){
					$number =  $card->card_number;
					$masked =  str_pad(substr($number, -4), strlen($number), '*', STR_PAD_LEFT);
					$card_options[$card->id] = $masked;
				}
				
				$months = [
							'01' => 'January',
							'02' => 'February',
							'03' => 'March',
							'04' => 'April',
							'05' => 'May',
							'06' => 'June',
							'07' => 'July ',
							'08' => 'August',
							'09' => 'September',
							'10' => 'October',
							'11' => 'November',
							'12' => 'December',
						];
						
				$years = array_combine(range(date('Y'), date('Y')+10), range(date('Y'), date('Y')+10));
				
				$products = session()->get('cart');
				
				$userAddresses = UserAddress::where('user_id',Auth::user()->id)->where('type',2)->get();
				$userAddress = [];
				foreach($userAddresses as $val){
					if(!empty($val['address_name']))
						$userAddress[$val->id] = $val['address_name'];
					else if(!empty($val['company_name']))
						$userAddress[$val->id] = $val['company_name'];
					else if(!empty($val['fname']))
						$userAddress[$val->id] = $val['fname'];
				}
				
				return view('checkout/checkout',compact('pageTitle','products','states','months','years','card_options','card_detail','userAddresses','userAddress'));
			}else{
				session()->forget('orderAddresses');
				session()->forget('carttotal');
				session()->forget('cart');
				
				\Session::flash('error', 'Your Cart Is Empty.');
				return redirect('/');
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
	
	public function payment(Request $request){
		if($request->session()->has('cart')){
			$pageTitle = "Order Payment";
			$months = [
							'01' => 'January',
							'02' => 'February',
							'03' => 'March',
							'04' => 'April',
							'05' => 'May',
							'06' => 'June',
							'07' => 'July ',
							'08' => 'August',
							'09' => 'September',
							'10' => 'October',
							'11' => 'November',
							'12' => 'December',
						];
						
			$years = array_combine(range(date('Y'), date('Y')+10), range(date('Y'), date('Y')+10));
			
			
			//pr(session()->get('cart'));
			//pr(session()->get('shipping_option'));die;
			//pr(session()->get('orderAddresses'));die;
			return view('checkout/payment',compact('pageTitle','months','years'));
		}else{
			\Session::flash('error', 'Your Cart Is Empty.');
			return redirect('/');
		}
	}

    /**
     * @param Request $request
     * @return string
     */
	public function saveAddress(Request $request)
    {
		$responce['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die();
			if(!array_key_exists('same_as_billing',$data)){
				$data['same_as_billing'] = 0;
			}
			if(!array_key_exists('shipping_type',$data)){
				$data['shipping_type'] = null;
			}
			
			$shipping_weight = new CalculateShippingWeight();			
			$result = $shipping_weight->weight($data);
			//pr($result);die;
			
			$responce['total_weight'] = $result['total_weight'];
			$multiple = $result['multiple'];
			$weight = $result['total_weight'];
			$product_weight = $result['product_weight'];
			$product_shipping = $result['product_shipping'];
			$product_shipping_amounts = $result['product_shipping_amounts'];
			$session_total = $result['session_total'];
			$address = $result['address'];
			
			
			$session_total = number_format((float)$session_total, 2, '.', '');
			session(['orderAddresses' => $data,'selectAddress'=>$data['selectAddress'],'shipping_option'=>$data['shipping_option']]);
			
			session()->put('products_weight',$product_weight);
			
			$product_ship = array();
			$rate = 0;
			$tax = 0;
			if(session()->get('carttotal.free_shipping')){
				$responce['status'] = 'success';
			}
			else if($multiple){
				foreach($product_weight as $k1=>$v1){
					$responce['status'] = '';
					if($v1['product_shipping']){
						$shipping = new UPSShipping();
						$validate = $shipping->validateAddress($v1['address']['zipcode'],$v1['address']['stateCode'],$v1['address']['state']);
						if($validate['status']){
							$rate_detail = $shipping->RateCalculate($v1['address']['zipcode'],$v1['weight'],$data['shipping_option']);
							//pr($rate_detail);
							if($rate_detail['status']){
								//$price = priceFormat($v1['product_qty'] * $rate_detail['res']->MonetaryValue);
								$price = number_format($rate_detail['res']->MonetaryValue,2,'.','');
								//$rate += $price;
								
								$product_ship['multiple'][$k1] = $price;
								$product_shipping_amounts[$k1] = $price;
								$responce['status'] = 'success';
							}else{
								$responce['status'] = '';
								//$responce['msg'] = $rate_detail['msg'];
								$responce['msg'] = 'Please enter a valid location.';
								session()->forget('orderAddresses');
								session()->forget('selectAddress');
								session()->forget('shipping_option');
								session()->forget('products_weight');
								break;
							}
						}else{
							$responce['status'] = '';
							//$responce['msg'] = $validate['msg'];
							$responce['msg'] = 'Please enter a valid location.';
							session()->forget('orderAddresses');
							session()->forget('selectAddress');
							session()->forget('shipping_option');
							session()->forget('products_weight');
							break;
						}
					}else{
						if($v1['product_shipping_type'] == 'flat'){
							if($v1['product_reduce_price'] == 1){
								$qty = $v1['product_qty'] - 1;
								$price = $v1['product_shipping_price']+($qty*$v1['product_additional_qty_price']);
								$price = number_format($price,2,'.','');
							}else{
								$price = $v1['product_qty']*(number_format($v1['product_shipping_price'],2,'.',''));
							}
									
							$product_ship['multiple'][$k1] = $price;
							$product_shipping_amounts[$k1] = $price;
							//$rate += $price;
						}else if($v1['product_shipping_type'] == 'CUSTOM_PRODUCT'){
							$price = number_format($v1['product_shipping_price'],2,'.','');
							$product_ship['multiple'][$k1] = $price;
							$product_shipping_amounts[$k1] = $price;
						}
						$responce['status'] = 'success';
					}
				}
				$sum = array_sum($product_shipping_amounts);
				$sum =number_format($sum,2,'.','');
				$rate += $sum;
			}
			else{
				$shipping = new UPSShipping();
				$validate = $shipping->validateAddress($address['zipcode'],$address['stateCode'],$address['state']);
				
				if($validate['status']){
					if($weight > 0 and $product_shipping){
						$rate_detail = $shipping->RateCalculate($address['zipcode'],$weight,$data['shipping_option']);
						//pr($rate_detail);die;
						if($rate_detail['status']){
							$ship_amount = number_format($rate_detail['res']->MonetaryValue,2,'.','');
							//echo $rate += $ship_amount;
							$one_LBS_ship_amount = $ship_amount/$weight;
							//$one_LBS_ship_amount = number_format($one_LBS_ship_amount,2,'.','');

							foreach($product_weight as $k1=>$v1){
								if($v1['product_shipping_type'] == 'flat'){
									if($v1['product_reduce_price'] == 1){
										$qty = $v1['product_qty'] - 1;
										$price = $v1['product_shipping_price']+($qty*$v1['product_additional_qty_price']);
										$price = number_format($price,2,'.','');
									}else{
										$price = $v1['product_qty']*(number_format($v1['product_shipping_price'],2,'.',''));
									}
									$product_shipping_amounts[$k1] = $price;
									//$rate += $price;
								}else{
									if($v1['product_shipping']){
										$price = $one_LBS_ship_amount*$v1['weight'];
										//$price = priceFormat($price * $v1['product_qty']);
										$price = priceFormat($price);
										$product_shipping_amounts[$k1] = $price;
									}else if($v1['product_shipping_type'] == 'CUSTOM_PRODUCT'){
										$price = number_format($v1['product_shipping_price'],2,'.','');
										$product_shipping_amounts[$k1] = $price;
									}
								}
							}
							$sum = array_sum($product_shipping_amounts);
							$sum =number_format($sum,2,'.','');
							$rate += $sum;
							$responce['status'] = 'success';
						}else{
							//$responce['msg'] = $rate_detail['msg'];
							$responce['msg'] = 'Please enter a valid location.';
							session()->forget('orderAddresses');
							session()->forget('selectAddress');
							session()->forget('shipping_option');
							session()->forget('products_weight');
						}
					}else{
						$responce['status'] = 'success';
						foreach($product_weight as $k1=>$v1){
							if($v1['product_shipping_type'] == 'flat'){
								if($v1['product_reduce_price'] == 1){
									$qty = $v1['product_qty'] - 1;
									$price = $v1['product_shipping_price']+($qty*$v1['product_additional_qty_price']);
									$price = number_format($price,2,'.','');
								}else{
									$price = $v1['product_qty']*(number_format($v1['product_shipping_price'],2,'.',''));
								}
								$product_shipping_amounts[$k1] = $price;
								//$rate += $price;
							}else if($v1['product_shipping_type'] == 'CUSTOM_PRODUCT'){
								$price = number_format($v1['product_shipping_price'],2,'.','');
								$product_shipping_amounts[$k1] = $price;
							}
						}
						$sum = array_sum($product_shipping_amounts);
						$sum =number_format($sum,2,'.','');
						$rate += $sum;
					}
					$product_ship['single'] = $rate;
				}else{
					//$responce['msg'] = $validate['msg'];
					$responce['msg'] = 'Please enter a valid location.';
					session()->forget('orderAddresses');
					session()->forget('selectAddress');
					session()->forget('shipping_option');
					session()->forget('products_weight');
				}
			}
			
			if(\Auth::user()->tax_exempt == 0){
				if($multiple){
					// loop for product to check if state is PA or not for calculate tax
					foreach(session()->get('cart') as $key=>$val){
						if($address[$key]['stateCode'] == 'PA'){
							$temp_tax = ($val['total']*config('constants.sales_tax'))/100;
							$temp_tax = number_format($temp_tax,2,'.','');
							$tax += $temp_tax;
						}
					}
				}
				else{
					// check if shipping is for pa
					if($address['stateCode'] == 'PA'){
						$tax_payable_amount = session()->get('carttotal.gross') - session()->get('carttotal.discount');
						$tax = ($tax_payable_amount*config('constants.sales_tax'))/100;
						$tax = number_format($tax,2,'.','');
					}
				}
			}
			
			$rate = number_format($rate,2,'.','');
			//pr($product_shipping_amounts);
			//echo "Rate : ".$rate.' Tax : '.$tax;die;
			
			session()->put('carttotal.shipping', $rate);
			session()->put('carttotal.sales_tax', $tax);
			
			$total = (session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping') + session()->get('carttotal.sales_tax'));
			$total = number_format($total,2,'.','');
			session()->put('carttotal.total', $total);

			$responce['shipping'] = priceFormat(session()->get('carttotal.shipping'));
			$responce['tax'] = priceFormat(session()->get('carttotal.sales_tax'));
			$responce['total'] = $total;
			
			session()->put('product_ship',$product_ship);
			session()->put('product_shipping_amounts',$product_shipping_amounts);
		}
		//pr($responce);die;
		return json_encode($responce);
	}
	
	public function Authorizepayment(Request $request){
		if($request->isMethod('post')){
			$data = $request->all();
			
			if(!array_key_exists('save_card',$data)){
				$data['save_card'] = 0;
			}
			if(array_key_exists('save_card_option',$data) and $data['save_card_option'] != ''){
				$card_detail = UserCards::findOrFail($data['save_card_option']);
				$data['card_number'] = $card_detail->card_number;
				$date = explode('-',$card_detail->expire_date);
				$data['expire_month'] = $date[0];
				$data['expire_year'] = $date[1];
			}
			
			$cart = session()->get('cart');
			$orderAddresses = session()->get('orderAddresses');
			
			$statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
			$nextId = $statement[0]->Auto_increment;

			if(session()->has('carttotal.total')){
                $total = session()->get('carttotal.total');
                \Log::info(Auth::user()->fname . '-' . Auth::user()->lname. '-' .Auth::user()->email);
                \Log::info("Cart Total: {$total}");
			    if (session()->has('paymentLink.total')) {
			        $total = session()->get('paymentLink.total');
                    \Log::info("Payment Link Total: {$total}");
                }
			}else{
				$total = $data['amount'];
                \Log::info("Post Total: {$total}");
			}
			
			$expiry_date = $data['expire_year'].'-'.$data['expire_month'];
			
			
			$payment = new PaymentAuthorize();
			
			$shipping_add = array();
			$billing_add = array();
			
			$i = 1;
			// @params billing_type for storing already stored biiling address

			if($orderAddresses['billing_type'] == 0 or $orderAddresses['billing_type'] == '0'){
				$billing_add['fname'] = $orderAddresses['billing_fname'];
				$billing_add['lname'] = $orderAddresses['billing_lname'];
				$billing_add['address'] = $orderAddresses['billing_address1'].','.$orderAddresses['billing_address1'];
				$billing_add['company'] = Auth::user()->company_name;
				$billing_add['phone_number'] = Auth::user()->phone_number;
				$billing_add['city'] = $orderAddresses['billing_city'];
				$billing_add['state'] = $orderAddresses['billing_state'];
				$billing_add['country'] = $orderAddresses['billing_country'];
				$billing_add['zipcode'] = $orderAddresses['billing_zipcode'];
			}else{
				$detail = UserAddress::where('id',$orderAddresses['billing_type'])->first();
				
				$billing_add['fname'] = Auth::user()->fname;
				$billing_add['lname'] = Auth::user()->lname;
				$billing_add['address'] = $detail->add1.','.$detail->add2;
				$billing_add['company'] = Auth::user()->company_name;
				$billing_add['phone_number'] = Auth::user()->phone_number;
				$billing_add['city'] = $detail->city;
				$billing_add['state'] = $detail->state;
				$billing_add['country'] = $detail->country;
				$billing_add['zipcode'] = $detail->zipcode;
			}
			
			// Fetch Shipping Address here
			
			if($orderAddresses['same_as_billing'] == 1 or $orderAddresses['same_as_billing'] == '1'){
				$shipping_add = $billing_add;
				unset($shipping_add['phone_number']);
			}
			else if($orderAddresses['shipping_type'] == 'multiple'){
				
				$key = key($orderAddresses['multi']);
				
				// Below comment Code use when we use already exist shipping code
				
				/* if($orderAddresses['multi'][$key]['shipping_type'] == 0 or $orderAddresses['multi'][$key]['shipping_type'] == '0'){ */
				
					$shipping_add['fname'] = $orderAddresses['multi'][$key]['fname'];
					$shipping_add['lname'] = $orderAddresses['multi'][$key]['lname'];
					$shipping_add['address'] = $orderAddresses['multi'][$key]['add1'].','.$orderAddresses['multi'][$key]['add2'];
					$shipping_add['company'] = Auth::user()->company_name;
					$shipping_add['city'] = $orderAddresses['multi'][$key]['city'];
					$shipping_add['state'] = $orderAddresses['multi'][$key]['state'];
					$shipping_add['country'] = $orderAddresses['multi'][$key]['country'];
					$shipping_add['zipcode'] = $orderAddresses['multi'][$key]['zipcode'];
				
				/* }else{
					$detail = UserAddress::where('id',$orderAddresses['multi'][$key]['shipping_type'])->first();
					$shipping_add['fname'] = Auth::user()->fname;
					$shipping_add['lname'] = Auth::user()->lname;
					$shipping_add['address'] = $detail->add1.','.$detail->add2;
					$shipping_add['company'] = Auth::user()->company_name;
					$shipping_add['city'] = $detail->city;
					$shipping_add['state'] = $detail->state;
					$shipping_add['country'] = $detail->country;
					$shipping_add['zipcode'] = $detail->zipcode;
				} */
			}
			else if($orderAddresses['shipping_type'] == 0 or $orderAddresses['shipping_type'] == '0'){
				
				$shipping_add['fname'] = $orderAddresses['shipping_fname'];
				$shipping_add['lname'] = $orderAddresses['shipping_lname'];
				$shipping_add['address'] = $orderAddresses['shipping_address1'].','.$orderAddresses['shipping_address1'];
				$shipping_add['company'] = Auth::user()->company_name;
				$shipping_add['city'] = $orderAddresses['shipping_city'];
				$shipping_add['state'] = $orderAddresses['shipping_state'];
				$shipping_add['country'] = $orderAddresses['shipping_country'];
				$shipping_add['zipcode'] = $orderAddresses['shipping_zipcode'];
			}
			else{
				
				$detail = UserAddress::where('id',$orderAddresses['shipping_type'])->first();
				$shipping_add['fname'] = Auth::user()->fname;
				$shipping_add['lname'] = Auth::user()->lname;
				$shipping_add['address'] = $detail->add1.','.$detail->add2;
				$shipping_add['company'] = Auth::user()->company_name;
				$shipping_add['city'] = $detail->city;
				$shipping_add['state'] = $detail->state;
				$shipping_add['country'] = $detail->country;
				$shipping_add['zipcode'] = $detail->zipcode;
			}
			
			$result = $payment->payment(config('constants.Authorize_loginId'),config('constants.Authorize_transactionId'),$total,$data['card_number'],$data['cvv_number'],$expiry_date,$billing_add,$shipping_add,$nextId);
			
			if($result['status'] == 'success'){
				if($data['save_card'] == 1){
					$card = new UserCards();
					$card->user_id = Auth::user()->id;
					$card->card_number = $data['card_number'];
					$card->expire_date = date('m-Y',strtotime($expiry_date));
					$card->save();
				}
			}
		}
		//pr($result);die;
		return json_encode($result);
	}
	
	public function saveOrder(Request $request){
		$responce['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			$cart = session()->get('cart');
			$orderAddresses = session()->get('orderAddresses');
			$product_weight = session()->get('products_weight');
			$product_ship = session()->get('product_ship');
			$product_shipping_amounts = session()->get('product_shipping_amounts');

			$multiple_shipping = 0;
			if($orderAddresses['shipping_type'] == 'multiple'){
				$multiple_shipping = 1;
			}
			
			$billing_add_id = 0;
			
			if($orderAddresses['billing_type'] == 0) {
				UserAddress::where('user_id',Auth::user()->id)->where('type',1)->update(['status'=>0]);
				$user_address = new UserAddress();
				$user_address->user_id = \Auth::user()->id;
				$user_address->type = 1;
				$user_address->company_name = $orderAddresses['billing_company_name'];
				$user_address->phone_number = $orderAddresses['billing_phone_number'];
				$user_address->fname = $orderAddresses['billing_fname'];
				$user_address->lname = $orderAddresses['billing_lname'];
				$user_address->add1 = $orderAddresses['billing_address1'];
				$user_address->add2 = $orderAddresses['billing_address2'];
				$user_address->zipcode = $orderAddresses['billing_zipcode'];
				$user_address->city = $orderAddresses['billing_city'];
				$user_address->state = $orderAddresses['billing_state'];
				$user_address->country = $orderAddresses['billing_country'];
				$user_address->save();
				
				$billing_add_id = $user_address->id;
			}
			
			if($orderAddresses['same_as_billing'] == 0 && session()->get('selectAddress') == ''){
				if($orderAddresses['shipping_type'] == 'multiple'){
					foreach($cart as $key=>$product) {
						/* Save User Address */
						if(array_key_exists('option',$orderAddresses['multi'][$key]) && ($orderAddresses['multi'][$key]['option'] == 0 || $orderAddresses['multi'][$key]['option'] == '0')){
							$user_address = new UserAddress();
							$user_address->user_id = \Auth::user()->id;
							$user_address->type = 2;
							$user_address->address_name = $orderAddresses['multi'][$key]['address_name'];
							$user_address->company_name = $orderAddresses['multi'][$key]['company_name'];
							$user_address->phone_number = $orderAddresses['multi'][$key]['phone_number'];
							$user_address->fname = $orderAddresses['multi'][$key]['fname'];
							$user_address->lname = $orderAddresses['multi'][$key]['lname'];
							$user_address->add1 = $orderAddresses['multi'][$key]['add1'];
							$user_address->add2 = $orderAddresses['multi'][$key]['add2'];
							$user_address->ship_in_care = $orderAddresses['multi'][$key]['ship_in_care'];
							$user_address->zipcode = $orderAddresses['multi'][$key]['zipcode'];
							$user_address->city = $orderAddresses['multi'][$key]['city'];
							$user_address->state = $orderAddresses['multi'][$key]['state'];
							$user_address->country = $orderAddresses['multi'][$key]['country'];
							$user_address->save();
						}
					}
				}				
				else if($orderAddresses['shipping_type'] == 0 || $orderAddresses['shipping_type'] == '0'){
					$user_address = new UserAddress();
					$user_address->user_id = \Auth::user()->id;
					$user_address->type = 2;
					$user_address->address_name = $orderAddresses['shipping_address_name'];
					$user_address->company_name = $orderAddresses['shipping_company_name'];
					$user_address->phone_number = $orderAddresses['shipping_phone_number'];
					$user_address->fname = $orderAddresses['shipping_fname'];
					$user_address->lname = $orderAddresses['shipping_lname'];
					$user_address->add1 = $orderAddresses['shipping_address1'];
					$user_address->add2 = $orderAddresses['shipping_address2'];
					$user_address->ship_in_care = $orderAddresses['shipping_ship_in_care'];
					$user_address->zipcode = $orderAddresses['shipping_zipcode'];
					$user_address->city = $orderAddresses['shipping_city'];
					$user_address->state = $orderAddresses['shipping_state'];
					$user_address->country = $orderAddresses['shipping_country'];
					$user_address->save();
				}
			}
			
			$order = new Orders();
			$order->user_id = \Auth::user()->id;
			$order->sub_total = session()->get('carttotal.gross');
			$order->discount = session()->get('carttotal.discount');
			$order->discount_code = session()->get('carttotal.discount_code');
			$order->shipping_fee = session()->get('carttotal.shipping');
			$order->sales_tax = session()->get('carttotal.sales_tax');
			$order->total = session()->get('carttotal.total');
			$order->shipping_option = session()->get('shipping_option');
			$order->customer_status = 2;
			$order->payment_status = isset($data['payment_status']) ? $data['payment_status'] : Orders::PAYMENTSTATUS_RECEIVED;
			$order->status = 1;
			$order->payment_id = $data['paymentID'];
			if(array_key_exists('auth_code',$data)){
				$order->payment_method = 'authorized';
				$order->auth_code = $data['auth_code'];
			}else if(array_key_exists('payment_type',$data)){
				$order->payment_method = $data['payment_type'];
				$order->payment_status = 7;
			}else{
				$order->payment_method = 'paypal';
			}
			$order->multiple_shipping = $multiple_shipping;

			if($order->save()) {
				/* Order Product Save */
				$i = 1;
				foreach($cart as $cartkey=>$product){
					$order_product = new OrderProducts();
					$order_product->order_id = $order->id;
					$order_product->item_id = $order->id.'-'.$i;
					$order_product->product_id = $product['product_id'];
					$order_product->project_name = $product['project_name'];
					$order_product->comments = $product['comments'];
					$order_product->price_default = $product['price_default'];
					$order_product->price = $product['price'];
					$order_product->qty = $product['quantity'];
					$order_product->gross_total = $product['gross_total'];
					$order_product->qty_discount = $product['qty_discount'];					
					$order_product->total = $product['total'];
					//$order_product->payment_status = 1;
					
					$product_info = Products::findOrFail($product['product_id']);
					if($product_info->no_artwork_required){
						//$order_product->customer_status = 3;
						$order_product->art_work_status = 2;
					}else{
						//$order_product->customer_status = 2;
						$order_product->art_work_status = 1;
					}
					//$order_product->art_work_link = url('/'.$product_info->slug);
					
					if(array_key_exists($cartkey,$product_weight)){
						$order_product->product_weight = number_format(($product_weight[$cartkey]['weight']), 2, '.', '');
					}
					/* if(array_key_exists('multiple',$product_ship)){
						if(array_key_exists($product['product_id'],$product_ship['multiple'])){
							$order_product->product_shipping = $product_ship['multiple'][$product['product_id']];
						}
					} */
					
					$order_product->product_shipping = $product_shipping_amounts[$cartkey];
					
					$order_product->save();
				
					/* Order Product Variants Save */
					if(array_key_exists('variants',$product)){
						foreach($product['variants'] as $key=>$val){
							$db = ProductVariant::select('product_variant.name as name','value.value as value');
			
							$detail= $db->leftJoin('product_variant_values as value','value.variant_id','=','product_variant.id')->where('product_variant.id',$key)->where('value.id',$val)->first();
							
							$order_product_option = new OrderProductOptions();
							$order_product_option->order_id = $order->id;
							$order_product_option->product_id = $product['product_id'];
							$order_product_option->order_product_id = $order_product->id;
							$order_product_option->type = 2;
							$order_product_option->custom_option_name = ucwords($detail->name);
							$order_product_option->custom_option_field_group = 'printing';
							$order_product_option->value = $detail->value;
							$order_product_option->price = $product['price_default'];
							$order_product_option->save();
						}
					}
					
					/* Order Product Options Save */
					$option_prices = 0;
					
					foreach((array)$product['options'] as $key1=>$val){
						foreach($val as $key2=>$val2){
							foreach($val2 as $key3=>$val3){
								$order_product_option = new OrderProductOptions();
								$order_product_option->order_id = $order->id;
								$order_product_option->product_id = $product['product_id'];
								$order_product_option->order_product_id = $order_product->id;
								$order_product_option->custom_option_id = $key2;
								$order_product_option->custom_option_name = ucwords(str_replace('_',' ',$key3));
								$order_product_option->custom_option_field_group = $key1;
								$str = explode('__',$val3);
								$order_product_option->value = $str[0];
								
								if($key3 == 'width'){
									$order_product_option->type = 3;
									$order_product_option->price = ($val2['width']*$val2['height'])*$product['price_default'];
								}else if($key3 == 'height'){
									$order_product_option->type = 3;
								}
								else if(!in_array($key3,array('height','width'))){
									$order_product_option->type = 1;
									if(array_key_exists(1,$str)){ $order_product_option->price = $str[1]; }
								}
								$order_product_option->save();
							}
						}
					}
				
					/* Save Order Billing And Shipping Address */				
					
					$order_address = new OrderAddress();
					$order_address->order_id = $order->id;
					$order_address->product_id = $product['product_id'];
					$order_address->order_product_id = $order_product->id;
					$order_address->same_as_billing = $orderAddresses['same_as_billing'];
					if($orderAddresses['billing_type'] == 0){
						$order_address->billing_add_id = $billing_add_id;
						$order_address->billing_company_name = $orderAddresses['billing_company_name'];
						$order_address->billing_phone_number = $orderAddresses['billing_phone_number'];
						$order_address->billing_fname = $orderAddresses['billing_fname'];
						$order_address->billing_lname = $orderAddresses['billing_lname'];
						$order_address->billing_add1 = $orderAddresses['billing_address1'];
						$order_address->billing_add2 = $orderAddresses['billing_address2'];
						$order_address->billing_zipcode = $orderAddresses['billing_zipcode'];
						$order_address->billing_city = $orderAddresses['billing_city'];
						$order_address->billing_state = $orderAddresses['billing_state'];
						$order_address->billing_country = $orderAddresses['billing_country'];
						if($orderAddresses['same_as_billing'] != 0){
							$order_address->shipping_company_name = $orderAddresses['billing_company_name'];
							$order_address->shipping_phone_number = $orderAddresses['billing_phone_number'];
							$order_address->shipping_fname = $orderAddresses['billing_fname'];
							$order_address->shipping_lname = $orderAddresses['billing_lname'];
							$order_address->shipping_add1 = $orderAddresses['billing_address1'];
							$order_address->shipping_add2 = $orderAddresses['billing_address2'];
							$order_address->shipping_zipcode = $orderAddresses['billing_zipcode'];
							$order_address->shipping_city = $orderAddresses['billing_city'];
							$order_address->shipping_state = $orderAddresses['billing_state'];
							$order_address->shipping_country = $orderAddresses['billing_country'];
						}
					}else{
						$user_add = UserAddress::findOrFail($orderAddresses['billing_type']);
						$order_address->billing_add_id = $user_add->id;
						$order_address->billing_company_name = $user_add->company_name;
						$order_address->billing_phone_number = $user_add->phone_number;
						$order_address->billing_fname = $user_add->fname;
						$order_address->billing_lname = $user_add->lname;
						$order_address->billing_add1 = $user_add->add1;
						$order_address->billing_add2 = $user_add->add2;
						$order_address->billing_zipcode = $user_add->zipcode;
						$order_address->billing_city = $user_add->city;
						$order_address->billing_state = $user_add->state;
						$order_address->billing_country = $user_add->country;
						if($orderAddresses['same_as_billing'] != 0){
							$order_address->shipping_company_name = $user_add->company_name;
							$order_address->shipping_phone_number = $user_add->phone_number;
							$order_address->shipping_fname = $user_add->fname;
							$order_address->shipping_lname = $user_add->lname;
							$order_address->shipping_add1 = $user_add->add1;
							$order_address->shipping_add2 = $user_add->add2;
							$order_address->shipping_zipcode = $user_add->zipcode;
							$order_address->shipping_city = $user_add->city;
							$order_address->shipping_state = $user_add->state;
							$order_address->shipping_country = $user_add->country;
						}
					}
					
					if($orderAddresses['same_as_billing'] == 0){
						if(session()->has('selectAddress') && !empty(session()->get('selectAddress'))){
							$user_add = UserAddress::findOrFail(session()->get('selectAddress'));
							$order_address->shipping_company_name = $user_add->company_name;
							$order_address->shipping_phone_number = $user_add->phone_number;
							$order_address->shipping_fname = $user_add->fname;
							$order_address->shipping_lname = $user_add->lname;
							$order_address->shipping_add1 = $user_add->add1;
							$order_address->shipping_add2 = $user_add->add2;
							$order_address->shipping_ship_in_care = $user_add->ship_in_care;
							$order_address->shipping_zipcode = $user_add->zipcode;
							$order_address->shipping_city = $user_add->city;
							$order_address->shipping_state = $user_add->state;
							$order_address->shipping_country = $user_add->country;
						}
						else if($orderAddresses['shipping_type'] == 'multiple'){
							if(array_key_exists('option',$orderAddresses['multi'][$cartkey]) && ($orderAddresses['multi'][$cartkey]['option'] == 0 || $orderAddresses['multi'][$key]['option'] == '0')){
								$order_address->shipping_company_name = $orderAddresses['multi'][$cartkey]['company_name'];
								$order_address->shipping_phone_number = $orderAddresses['multi'][$cartkey]['phone_number'];
								$order_address->shipping_fname = $orderAddresses['multi'][$cartkey]['fname'];
								$order_address->shipping_lname = $orderAddresses['multi'][$cartkey]['lname'];
								$order_address->shipping_add1 = $orderAddresses['multi'][$cartkey]['add1'];
								$order_address->shipping_add2 = $orderAddresses['multi'][$cartkey]['add2'];
								$order_address->shipping_ship_in_care = $orderAddresses['multi'][$cartkey]['ship_in_care'];
								$order_address->shipping_zipcode = $orderAddresses['multi'][$cartkey]['zipcode'];
								$order_address->shipping_city = $orderAddresses['multi'][$cartkey]['city'];
								$order_address->shipping_state = $orderAddresses['multi'][$cartkey]['state'];
								$order_address->shipping_country = $orderAddresses['multi'][$cartkey]['country'];
							}else{
								$user_add = UserAddress::findOrFail($orderAddresses['multi'][$cartkey]['shippingAddress']);
								$order_address->shipping_company_name = $user_add->company_name;
								$order_address->shipping_phone_number = $user_add->phone_number;
								$order_address->shipping_fname = $user_add->fname;
								$order_address->shipping_lname = $user_add->lname;
								$order_address->shipping_add1 = $user_add->add1;
								$order_address->shipping_add2 = $user_add->add2;
								$order_address->shipping_ship_in_care = $user_add->ship_in_care;
								$order_address->shipping_zipcode = $user_add->zipcode;
								$order_address->shipping_city = $user_add->city;
								$order_address->shipping_state = $user_add->state;
								$order_address->shipping_country = $user_add->country;
							}
						}
						else if($orderAddresses['shipping_type'] == 0 or $orderAddresses['shipping_type'] == '0'){						
							$order_address->shipping_company_name = $orderAddresses['shipping_company_name'];
							$order_address->shipping_phone_number = $orderAddresses['shipping_phone_number'];
							$order_address->shipping_fname = $orderAddresses['shipping_fname'];
							$order_address->shipping_lname = $orderAddresses['shipping_lname'];
							$order_address->shipping_add1 = $orderAddresses['shipping_address1'];
							$order_address->shipping_add2 = $orderAddresses['shipping_address2'];
							$order_address->shipping_ship_in_care = $orderAddresses['shipping_ship_in_care'];
							$order_address->shipping_zipcode = $orderAddresses['shipping_zipcode'];
							$order_address->shipping_city = $orderAddresses['shipping_city'];
							$order_address->shipping_state = $orderAddresses['shipping_state'];
							$order_address->shipping_country = $orderAddresses['shipping_country'];
						}
					}
					$order_address->save();
					$i++;
				}
				
				// Below Code for check all order products files are uploaded or not //
				
				$check_status = DB::select('SELECT count(*) as art_work_status , (SELECT COUNT(*) FROM `order_products` WHERE `order_id` = '.$order->id.') as total_product FROM `order_products` WHERE `order_id` = '.$order->id.' AND art_work_status >=2');
				if($check_status[0]->art_work_status == $check_status[0]->total_product){
					Orders::where('id',$order->id)->update(['customer_status'=>3]);
				}

                $responce['status'] = 'success';
                $responce['orderId'] = $order->id;
                session(['orderSave'=>1]);

				if ($order->payment_status == Orders::PAYMENTSTATUS_DECLINED) {
                    // handle declined orders
                } else {
                    session()->forget('cart');
                    session()->forget('selectAddress');
                    session()->forget('orderAddresses');
                    session()->forget('comments');
                    session()->forget('shipping_option');
                    session()->forget('carttotal');
                    FunctionsHelper::sendOrderReceipt($order->id);
                }
			}
		}
		return json_encode($responce);
	}
	
	public function uploads(Request $request,$id){

		$pageTitle = 'Uploads Docs';
		
		$db = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress','files']);
		$order = $db->first();
		if(count($order) <= 0 || $order->user_id != Auth::user()->id) {
			\Session::flash('error', 'You are not authorized for that order.');
			return redirect('/');
		} else {
			$no_artwork_required = [];
			$orderFileUploads = [];
			foreach($order->orderProduct as $product) {
				if($product->product->no_artwork_required == 1) {
					$no_artwork_required[$product->product->id] = 1;
				} else {
				    $uploadKey = UploadsController::generateUploadKey($product);
                    $orderFileUploads[$product->id] = [
                        'url' => null,
                        'key' => $uploadKey
                    ];
					$no_artwork_required[$product->product->id] = 0;
				}
			}

			$upload_file_exist = false;
			foreach($no_artwork_required as $val) {
				if($val == 0) {
					$upload_file_exist = true;
				}
			}
			if($upload_file_exist == false) {
				if(isset($_GET['msg'])){
					return redirect('/orders?msg='.$_GET['msg']);
				}else{
					\Session::flash('error', 'No art work files required for this order.');
					return redirect('/orders');
				}
			}
		}

		return view('checkout/uploads',compact('pageTitle','id','order','no_artwork_required','orderFileUploads'));
	}

    /**
     * @param Request $request
     * @param $orderId
     * @return string
     */
	public function saveUploadFiles(Request $request, $orderId)
    {
        $order = Orders::where('id', $orderId)
            ->first();

        if(!empty($order) && $request->isMethod('post')) {
            $files = $request->get('files');
            foreach($files as $file) {
                $orderFile = new OrderFiles();
                $orderFile->order_id = $orderId;
                $orderFile->order_product_id = $file['id'];
                $orderFile->name = $file['name'];
                $orderFile->s3_key = $file['key'];
                $orderFile->created_at = Carbon::now();
                $orderFile->updated_at = Carbon::now();
                $orderFile->save();

                $orderProduct = OrderProducts::where('order_id', $orderId)
                    ->where('id', $file['id'])
                    ->first();

                $orderProduct->art_work_status = 2;
                $orderProduct->art_work_date = date('m-d-Y');
                $orderProduct->save();
            }

            $uploadQueue = new UploadQueue();
            $uploadQueue->order_id = $orderId;

            if($uploadQueue->save()) {
                $res['status'] = true;
                $res['success_msg'] = 'Your file(s) have been successfully uploaded.';
                \Session::flash('success', $res['success_msg']);

                \Log::info('#### Dispatching upload artwork queue');
                UploadArtwork::dispatch($orderId);
                $res['success'] = true;

            } else {
                $res['error_msg'] = 'There was an error uploading your file(s), please try again.';
            }
        }

        return json_encode($res);
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     */
    public function uploadArtworkFiles(Request $request, $id)
    {
        $res['status'] = false;
        $res['error_msg'] = '';
        ini_set('max_execution_time', 0);

        if($request->isMethod('post')) {
            $data = $request->all();
            if(array_key_exists('files',$data) and count($data['files']) > 0) {
                $validArr = array();
                $msg = array();

                foreach($data['files'] as $key => $files) {
                    foreach($files as $key1 => $file){
                        if(!empty($file) and count($file) > 0 and $file->getClientOriginalExtension() != 'eps'){
                            $validArr['files.'.$key.'.'.$key1] = 'sometimes|mimes:AI,ai,eps,jpg,jpeg,pdf,id,png';

                            $msg['files.'.$key.'.'.$key1.'.required'] = 'Please upload an image';
                            $msg['files.'.$key.'.'.$key1.'.mimes'] = 'Only eps,jpg,jpeg,pdf,png and id images are allowed';
                        }
                    }
                }

                $validation = Validator::make($data, $validArr,$msg);
                if ($validation->passes()) {

                    $destinationPath = public_path('/uploads/orders/');
                    $file_count = 1;
                    $artworkFiles = [];

                    foreach ($data['files'] as $key => $files) {
                        foreach($files as $k1 => $file) {
                            if(!empty($file) and count($file) > 0) {
                                $filename = $file->getClientOriginalName();
                                $extension = $file->getClientOriginalExtension();
                                $file_name = date('His').time().$file_count.$filename;

                                $qry = DB::select('SELECT id ,double_side_print, (SELECT COUNT(*) as count FROM `order_files` WHERE `order_id` = '.$id.' AND `order_product_id` = '.$key.' AND `side` = '.$k1.') as total_files FROM `products` WHERE `id` = (SELECT product_id FROM `order_products` where `order_id` = '.$id.' AND `id` = '.$key.')');

                                if($qry[0]->total_files > 1) {
                                    $detail = DB::table('order_files')->where('order_id',$id)->where('order_product_id',$key)->where('side',$k1)->first();
                                    $path = public_path('/uploads/orders/'.$detail->name);
                                    if(file_exists($path) && !empty($detail->name)){
                                        //unlink($path);
                                    }
                                }

                                $orderImages = DB::table('order_files');
                                $data_array['order_id'] = $id;
                                $data_array['order_product_id'] = $key;
                                $data_array['name'] = $file_name;
                                $data_array['created_at'] = Carbon::now();
                                $data_array['updated_at'] = Carbon::now();
                                if($orderImages->insertGetId($data_array)){
                                    $file->move($destinationPath, $file_name);
                                }

                                $file_count++;
                                $artworkFiles[md5($file_name)] = [
                                    'name' => $file_name,
                                    'path' => $destinationPath . $file_name
                                ];

                                $orderProduct = OrderProducts::where('order_id',$id)->where('id',$key)->first();
                                $orderProduct->art_work_status = 2;
                                $orderProduct->art_work_date = date('m-d-Y');
                                $orderProduct->save();
                            }
                        }
                    }

                    // Below Code for check all order products files are uploaded or not //

                    $check_status = DB::select('SELECT count(*) as art_work_status , (SELECT COUNT(*) FROM `order_products` WHERE `order_id` = '.$id.') as total_product FROM `order_products` WHERE `order_id` = '.$id.' AND art_work_status >=2');

                    if($check_status[0]->art_work_status == $check_status[0]->total_product){
                        Orders::where('id',$id)->update(['customer_status'=>3]);
                    }

                    $uploadQueue = new UploadQueue();
                    $uploadQueue->order_id = $id;

                    if($uploadQueue->save()) {
                        $res['status'] = true;
                        $res['success_msg'] = 'Your file(s) have been successfully uploaded.';
                        \Session::flash('success', $res['success_msg']);

                        // Trigger background job
                        Artisan::queue('upload:artwork', [
                            'orderId' => $id,
                            'artworkFiles' => $artworkFiles
                        ]);

                    } else {
                        $res['error_msg'] = 'There was an error uploading your file(s), please try again.';
                    }

                } else {
                    $errorArray = [];
                    foreach($data['files'] as $k => $v){
                        if(count($v) > 1){
                            foreach($v as $k1=>$v1){
                                $errorArray[$k][$k1] = $validation->errors()->first('files.'.$k.'.'.$k1);
                            }
                        }else{
                            $errorArray[$k][0] = $validation->errors()->first('files.'.$k.'.0');
                        }
                    }

                    $res['error_messages'] = $errorArray;
                    $res['error_msg'] = 'File not uploaded,please try again.';
                }

            } else {
                $res['error_msg'] = 'There was an error uploading your file(s), please try again.';
            }
        }

        return json_encode($res);
    }

	public function uploadFiels(Request $request,$id){
		$res['status'] = false;
		$res['error_msg'] = '';
		ini_set('max_execution_time', 0);
		if($request->isMethod('post')){
			$data = $request->all();
			//echo $data['files'][121][0]->getClientOriginalExtension();
			//pr($data);die;
			if(array_key_exists('files',$data) and count($data['files']) > 0){
				$validArr = array();
				$msg = array();
				foreach($data['files'] as $key => $files) {
					foreach($files as $key1 => $file){
						if(!empty($file) and count($file) > 0 and $file->getClientOriginalExtension() != 'eps'){
							$validArr['files.'.$key.'.'.$key1] = 'sometimes|mimes:AI,ai,eps,jpg,jpeg,pdf,id,png';

							$msg['files.'.$key.'.'.$key1.'.required'] = 'Please upload an image';
							$msg['files.'.$key.'.'.$key1.'.mimes'] = 'Only eps,jpg,jpeg,pdf,png and id images are allowed';
						}
					}
				}
				
				$validation = Validator::make($data, $validArr,$msg);
				if ($validation->passes()) {
					//pr($data['files']);
					//die;
					$destinationPath = public_path('/uploads/orders/');
					$file_count = 1;
					
					foreach ($data['files'] as $key => $files) {
						foreach($files as $k1=>$file){
							if(!empty($file) and count($file) > 0){
								$filename = $file->getClientOriginalName();
								$extension = $file->getClientOriginalExtension();
								$file_name = date('His').time().$file_count.$filename;
								
								//*** Below code for check old files and delete **// 
								
								$qry = DB::select('SELECT id ,double_side_print, (SELECT COUNT(*) as count FROM `order_files` WHERE `order_id` = '.$id.' AND `order_product_id` = '.$key.' AND `side` = '.$k1.') as total_files FROM `products` WHERE `id` = (SELECT product_id FROM `order_products` where `order_id` = '.$id.' AND `id` = '.$key.')');
								
								if($qry[0]->total_files > 1){
									$detail = DB::table('order_files')->where('order_id',$id)->where('order_product_id',$key)->where('side',$k1)->first();
									$path = public_path('/uploads/orders/'.$detail->name);
									if(file_exists($path) && !empty($detail->name)){
										unlink($path);
									}
								}
								
								//*** End code for check old files and delete **// 
								
								$orderImages = DB::table('order_files');
								$data_array['order_id'] = $id;
								$data_array['order_product_id'] = $key;
								$data_array['name'] = $file_name;
								$data_array['created_at'] = Carbon::now();
								$data_array['updated_at'] = Carbon::now();
								if($orderImages->insertGetId($data_array)){
									$file->move($destinationPath, $file_name);
								}
								$file_count++;
								
								$orderProduct = OrderProducts::where('order_id',$id)->where('id',$key)->first();
								$orderProduct->art_work_status = 2;
								$orderProduct->art_work_date = date('m-d-Y');
								//$orderProduct->customer_status = 3;
								$orderProduct->save();
							}
						}
					}
					
					// Below Code for check all order products files are uploaded or not //
					
					$check_status = DB::select('SELECT count(*) as art_work_status , (SELECT COUNT(*) FROM `order_products` WHERE `order_id` = '.$id.') as total_product FROM `order_products` WHERE `order_id` = '.$id.' AND art_work_status >=2');
					
					if($check_status[0]->art_work_status == $check_status[0]->total_product){
						Orders::where('id',$id)->update(['customer_status'=>3]);
					}
					
					/* $orderStatus = Orders::findOrFail($id);
					$orderStatus->customer_status = 3;
					$orderStatus->save(); */

					$return = TflowHelper::uploadToTflow($id); // file uploads to tflow		
					//pr($return);die;
					if($return['status']){
						$res['status'] = true;
						$res['success_msg'] = 'Your files uploaded successfully for order.';
						\Session::flash('success', $res['success_msg']);
					}else{
						$res['error_msg'] = $return['errorMsg'];
					}
					
					
				}else{
					$errorArray = [];
					foreach($data['files'] as $k => $v){
						if(count($v) > 1){
							foreach($v as $k1=>$v1){
								$errorArray[$k][$k1] = $validation->errors()->first('files.'.$k.'.'.$k1);
							}
						}else{
							$errorArray[$k][0] = $validation->errors()->first('files.'.$k.'.0');
						}
					}
					//pr($errorArray);die;
					$res['error_messages'] = $errorArray;
					
					$res['error_msg'] = 'File not uploaded,please try again.';
				}
			}else{
				$res['error_msg'] = 'Files not uploaded,please try again.';
			}				
		}
		
		return json_encode($res);			
	}
	
	public function multipleAddresses(Request $request){
		$responce['status'] = '';
		$responce['res'] = '';
		if($request->isMethod('post')){
			$userAddress = UserAddress::where('user_id',Auth::user()->id)->where('type',2)->pluck('address_name','id');
			$states = State::where('status',1)->pluck('stateName','stateCode')->all();
			foreach(session()->get('cart') as $key=>$value){
				
				$product = Products::select('name')->where('id',$value['product_id'])->first();
				$responce['res'] .= '<div class="col-sm-12 multi_product_add" style="margin-left:15px" data="'.$key.'"><h4>'.$product->name.'</h4><div class="form-group col-sm-12 multi_shipping_error_'.$key.' has-error hide"></div><div class="clearfix"></div>';
				
				$responce['res'] .= '<div class="form-group multiDivBox" data="'.$key.'" data-key="'.$key.'">';
				
				$responce['res'] .= '<div class="col-xs-8">'.Form::select('multi['.$key.'][shippingAddress]',$userAddress,'',['class'=>'form-control','placeholder'=>'Select from My Saved Addresses','data'=>$key]).'</div>';
				
				$responce['res'] .= '<div class="clearfix"></div>';
				
				$responce['res'] .= '<div class="col-sm-6"><div class="radio"><label><input type="radio" class="shippingOption" name="multi['.$key.'][option]" value="0" data="'.$key.'">New Address</label></div></div>';
				
				$responce['res'] .= '</div>';
				
				$responce['res'] .= '<div class="col-sm-12 multiple_ship_div multi_div_'.$key.'" style="display:none;"><div class="form-group col-sm-6">'.Form::label('multi['.$key.'][company_name]','Company Name').Form::text('multi['.$key.'][company_name]','',['class'=>'form-control opt','placeholder'=>'Company Name']).'</div>
				<div class="form-group col-sm-6">'.Form::label('multi['.$key.'][phone_number]','Phone Number').Form::text('multi['.$key.'][phone_number]','',['class'=>'form-control  opt','placeholder'=>'Phone Number']).'</div>
				<div class="form-group col-sm-6">'.Form::label('multi['.$key.'][fname]','First Name').Form::text('multi['.$key.'][fname]','',['class'=>'form-control','placeholder'=>'First Name']).'</div><div class="form-group col-sm-6">'.Form::label('multi['.$key.'][lname]','Last Name').Form::text('multi['.$key.'][lname]','',['class'=>'form-control','placeholder'=>'Last Name']).'</div><div class="form-group col-sm-12">'.Form::label('','Street address').Form::text('multi['.$key.'][add1]','',['class'=>'form-control','placeholder'=>'Street address 1']).'
						</div>
						<div class="form-group col-sm-12">'.Form::text('multi['.$key.'][add2]','',['class'=>'form-control street2  opt','placeholder'=>'Street address 2']).'
						</div>
						<div class="form-group col-sm-12">'.Form::label('multi['.$key.'][ship_in_care]','Ship in care of').Form::text('multi['.$key.'][ship_in_care]','',['class'=>'form-control  opt','placeholder'=>'Ship in care of']).'</div>
						<div class="form-group col-sm-6">'.Form::label('multi['.$key.'][zipcode]','Zipcode').Form::number('multi['.$key.'][zipcode]','',['class'=>'form-control','placeholder'=>'Zipcode']).'</div>
						<div class="form-group col-sm-6">'.Form::label('multi['.$key.'][city]','Town / City').Form::text('multi['.$key.'][city]','',['class'=>'form-control','placeholder'=>'City']).'</div>
						<div class="form-group col-sm-6">'.Form::label('multi['.$key.'][state]','State').Form::select('multi['.$key.'][state]',$states,'',['class'=>'form-control','placeholder'=>'State']).'</div>
						<div class="form-group col-sm-6">'.Form::label('multi['.$key.'][country]','Country').Form::text('multi['.$key.'][country]','US',['class'=>'form-control','placeholder'=>'Country','readonly']).'</div><div class="form-group col-sm-12">'.Form::label('multi['.$key.'][address_name]','Name This Address').Form::text('multi['.$key.'][address_name]','',['class'=>'form-control','placeholder'=>'Provide this address with a name for quick access']).'</div></div></div>';
				
				$responce['status'] = 'success';
			}
		}
		return json_encode($responce);
	}

	public function invoice_receipt($id){
		$pageTitle = "";
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$db = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress']);
		$order = $db->first();	
		
		return view('checkout/pdf',compact('pageTitle','order','agents'));
	}
}
