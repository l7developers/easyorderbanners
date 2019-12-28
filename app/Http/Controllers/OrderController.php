<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Form;
use App\User;
use App\Orders;
use App\OrderProducts;
use App\ProductOptions;
use App\Reviews;
use App\Coupons;
use App\UserCards;
use App\State;
use App\OrderAddress;
use App\UserAddress;
use \App\Helpers\PaymentAuthorize;
use \App\Helpers\FunctionsHelper;
use \App\Helpers\CalculateShippingWeight;
use \App\Helpers\UPSShipping;

class OrderController extends Controller
{
    public function __construct(){
        $this->middleware('auth',['except' => ['updatePayment','paymentLinkCoupon','Authorizepayment','saveAddress','multipleAddresses']]);
    }
	
    public function order_payment($id,Request $request){ 

		$pageTitle = 'Order Payment';
		
		$order = Orders::where('id',$id)->where('payment_status',0)->with(['customer','agent','Products','orderProductOptions','orderAddress','files'])->first();
		
		if(count($order) < 1){
			\Session::flash('error', 'Order Payment Done.');
			return redirect('/');
		}
		
		if($order->user_id != Auth::user()->id){
			//Auth::logout();
			//\Session::flash('error', 'Invalid Order!.');
			//return redirect('/home');
			//return redirect()->guest('login');
		}
		
		// Below code for remove old session and create new session //
		session()->forget('paymentLink');
		
		session()->put('paymentLink.gross', $order->sub_total);
		if($order->discount >= 1){
			session()->put('paymentLink.discount', $order->discount);
		}else{
			session()->put('paymentLink.discount', 0);
		}
		session()->put('paymentLink.discount_code', $order->discount_code);
		session()->put('paymentLink.shipping', $order->shipping_fee);
		session()->put('paymentLink.sales_tax', $order->sales_tax);
		
		$total = number_format((session()->get('paymentLink.gross') - session()->get('paymentLink.discount') + session()->get('paymentLink.shipping') + session()->get('paymentLink.sales_tax')),2,'.','');
		if($total < 0){
			$total = 0.00;
		}
		session()->put('paymentLink.total', $total);
		
		// End Below code for remove old session and create new session //
		
		$user_cards = UserCards::where('user_id',$order->customer->id)->get();
		$card_options = array();
		$card_detail = array();
		foreach($user_cards as $card){
			$number =  $card->card_number;
			$masked =  str_pad(substr($number, -4), strlen($number), '*', STR_PAD_LEFT);
			$card_options[$card->id] = $masked;
		}
		//pr($order);die;
		
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
		
		$states = State::where('status',1)->pluck('stateName','stateCode')->all();

		$userAddress = UserAddress::where('user_id',Auth::user()->id)->where('type',2)->pluck('address_name','id');
		
		$userAddresses = UserAddress::where('user_id',Auth::user()->id)->where('type',2)->get();
		$userAddress = [];
		foreach($userAddresses as $val){
			$userAddress[$val->id] = $val['address_name'];
		}
		
		return view('order/orderpayment',compact('pageTitle','id','order','years','months','states','card_options','card_detail','userAddresses','userAddress'));
    }
	
	public function saveAddress(Request $request){
		$responce['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();

			if(!array_key_exists('same_as_billing',$data)){
				$data['same_as_billing'] = 0;
			}
			if(!array_key_exists('shipping_type',$data)){
				$data['shipping_type'] = null;
			}
			
			$shipping_weight = new CalculateShippingWeight();			
			$result = $shipping_weight->OrderPendingWeight($data);	
			//pr($result);die();		
			
			$responce['total_weight'] = $result['total_weight'];
			$multiple = $result['multiple'];
			$weight = $result['total_weight'];
			$product_weight = $result['product_weight'];
			$product_shipping = $result['product_shipping'];
			$product_shipping_amounts = $result['product_shipping_amounts'];
			$session_total = $result['session_total'];
			$address = $result['address'];
			$order_products = $result['order_products'];			
			
			$session_total = number_format((float)$session_total, 2, '.', '');
			session(['paymentLink.orderAddresses' => $data,'shipping_option'=>$data['shipping_option']]);
			
			session()->put('paymentLink.products_weight',$product_weight);
			
			$product_ship = array();
			$rate = 0;
			$tax = 0;
			
			if(!empty(session()->get('paymentLink.free_shipping_ids'))){
				$responce['status'] = 'success';
			}
			else if($multiple){
				foreach($product_weight as $k1=>$v1){
					$responce['status'] = '';
					if(array_key_exists('product_shipping',$v1) and $v1['product_shipping']){
						$shipping = new UPSShipping();
						$validate = $shipping->validateAddress($v1['address']['zipcode'],$v1['address']['stateCode'],$v1['address']['state']);
						if($validate['status']){
							$rate_detail = $shipping->RateCalculate($v1['address']['zipcode'],$v1['weight'],$data['shipping_option']);
							//pr($rate_detail);
							if($rate_detail['status']){
								$price = number_format($rate_detail['res']->MonetaryValue,2,'.','');
								//$rate += $price;
								
								$product_ship['multiple'][$k1] = $price;
								$product_shipping_amounts[$k1] = $price;
								$responce['status'] = 'success';
							}else{
								$responce['msg'] = 'Please enter a valid location.';
							}
						}else{
							$responce['msg'] = $validate['msg'];
						}
					}else{
						if(array_key_exists('product_shipping',$v1) and $v1['product_shipping_type'] == 'flat'){
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
								if(array_key_exists('product_shipping',$v1) and $v1['product_shipping_type'] == 'flat'){
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
										//$price = number_format(($price * $v1['product_qty']),2,'.','');
										$price = number_format($price,2,'.','');
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
							if(isset($rate_detail['msg']) && !empty($rate_detail['msg']))
								$responce['msg'] = $rate_detail['msg'];
							else
								$responce['msg'] = 'Please enter a valid location.';
						}
					}else{
						$responce['status'] = 'success';
						foreach($product_weight as $k1=>$v1){
							if(array_key_exists('product_shipping',$v1) and $v1['product_shipping_type'] == 'flat'){
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
					$responce['msg'] = $validate['msg'];
				}
			}
			
			if($multiple){
				// loop for product to check if state is PA or not for calculate tax
				foreach($order_products as $key=>$val){
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
					$tax_payable_amount = session()->get('paymentLink.gross') - session()->get('paymentLink.discount');
					$tax = ($tax_payable_amount*config('constants.sales_tax'))/100;
					$tax = number_format($tax,2,'.','');
				}
			}
			
			$rate = number_format($rate,2,'.','');			
			
			session()->put('paymentLink.shipping', $rate);
			session()->put('paymentLink.sales_tax', $tax);
			
			$total = (session()->get('paymentLink.gross') - session()->get('paymentLink.discount') + session()->get('paymentLink.shipping') + session()->get('paymentLink.sales_tax'));
			$total = number_format($total,2,'.','');
			session()->put('paymentLink.total', $total);

			$responce['discount'] = session()->get('paymentLink.discount');
			$responce['shipping'] = session()->get('paymentLink.shipping');
			$responce['tax'] = session()->get('paymentLink.sales_tax');
			$responce['gross'] = session()->get('paymentLink.gross');
			$responce['total'] = $total;
			
			session()->put('paymentLink.product_ship',$product_ship);
			session()->put('paymentLink.product_shipping_amounts',$product_shipping_amounts);
		}		
		return json_encode($responce);
	}
	
	public function multipleAddresses(Request $request){
		$responce['status'] = '';
		$responce['res'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			$userAddress = UserAddress::where('user_id',Auth::user()->id)->where('type',2)->pluck('address_name','id');
			$order = Orders::where('id',$data['order_id'])->with('Products')->first();			
			$states = State::where('status',1)->pluck('stateName','stateCode')->all();
			foreach($order->Products as $key=>$value){
				$responce['res'] .= '<div class="col-sm-12 multi_product_add" style="margin-left:15px" data="'.$value->id.'"><h4>'.$value->product->name.'</h4><div class="form-group col-sm-12 multi_shipping_error_'.$value->id.' has-error hide"></div><div class="clearfix"></div>';
				
				$responce['res'] .= '<div class="form-group multiDivBox" data="'.$value->id.'" data-key="'.$value->id.'">';
				
				$responce['res'] .= '<div class="col-xs-8">'.Form::select('multi['.$value->id.'][shippingAddress]',$userAddress,'',['class'=>'form-control','placeholder'=>'Select from My Saved Addresses','data'=>$value->id]).'</div>';
				
				$responce['res'] .= '<div class="clearfix"></div>';
				
				$responce['res'] .= '<div class="col-sm-6"><div class="radio"><label><input type="radio" class="shippingOption" name="multi['.$value->id.'][option]" value="0" data="'.$value->id.'">New Address</label></div></div>';
				
				$responce['res'] .= '</div>';
				
				$responce['res'] .= '<div class="col-sm-12 multiple_ship_div multi_div_'.$value->id.'" style="display:none;"><div class="form-group col-sm-6">'.Form::label('multi['.$value->id.'][company_name]','Company Name').Form::text('multi['.$value->id.'][company_name]','',['class'=>'form-control companyInput opt','placeholder'=>'Company Name']).'</div>
				<div class="form-group col-sm-6">'.Form::label('multi['.$value->id.'][phone_number]','Phone Number').Form::text('multi['.$value->id.'][phone_number]','',['class'=>'form-control opt','placeholder'=>'Phone Number']).'</div>
				<div class="form-group col-sm-6">'.Form::label('multi['.$value->id.'][fname]','First Name').Form::text('multi['.$value->id.'][fname]','',['class'=>'form-control','placeholder'=>'First Name']).'</div><div class="form-group col-sm-6">'.Form::label('multi['.$value->id.'][lname]','Last Name').Form::text('multi['.$value->id.'][lname]','',['class'=>'form-control','placeholder'=>'Last Name']).'</div><div class="form-group col-sm-12">'.Form::label('','Street address').Form::text('multi['.$value->id.'][add1]','',['class'=>'form-control','placeholder'=>'Street address 1']).'
						</div>
						<div class="form-group col-sm-12">'.Form::text('multi['.$value->id.'][add2]','',['class'=>'form-control street2 opt','placeholder'=>'Street address 2']).'
						</div>
						<div class="form-group col-sm-12">'.Form::label('multi['.$value->id.'][ship_in_care]','Ship in care of').Form::text('multi['.$value->id.'][ship_in_care]','',['class'=>'form-control ship_in_care opt','placeholder'=>'Ship in care of']).'</div>
						<div class="form-group col-sm-6">'.Form::label('multi['.$value->id.'][zipcode]','Zipcode').Form::number('multi['.$value->id.'][zipcode]','',['class'=>'form-control','placeholder'=>'Zipcode']).'</div>
						<div class="form-group col-sm-6">'.Form::label('multi['.$value->id.'][city]','Town / City').Form::text('multi['.$value->id.'][city]','',['class'=>'form-control','placeholder'=>'City']).'</div>
						<div class="form-group col-sm-6">'.Form::label('multi['.$value->id.'][state]','State').Form::select('multi['.$value->id.'][state]',$states,'',['class'=>'form-control','placeholder'=>'State']).'</div>
						<div class="form-group col-sm-6">'.Form::label('multi['.$value->id.'][country]','Country').Form::text('multi['.$value->id.'][country]','US',['class'=>'form-control','placeholder'=>'Country','readonly']).'</div><div class="form-group col-sm-12">'.Form::label('multi['.$value->id.'][address_name]','Name This Address').Form::text('multi['.$value->id.'][address_name]','',['class'=>'form-control companyInput','placeholder'=>'Name This Address']).'</div></div></div>';
				
				$responce['status'] = 'success';
			}
		}
		return json_encode($responce);
	}

	public function updatePayment(Request $request){
		$responce['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();			
			
			$order = Orders::where('id',$data['order_id'])->with('Products')->first();
			
			if(session()->has('paymentLink')){
				$order->sub_total = session()->get('paymentLink.gross');
				$order->discount = session()->get('paymentLink.discount');
				$order->discount_code = session()->get('paymentLink.discount_code');
				$order->shipping_fee = session()->get('paymentLink.shipping');
				$order->sales_tax = session()->get('paymentLink.sales_tax');
				$order->total = session()->get('paymentLink.total');
			}
			$order->payment_status = 2;
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
			if($order->customer_status <= 0)
			{
				$order->customer_status = 2;			
			}	
			$order->created_at = \Carbon\Carbon::now();			
			if($order->save()){				
				if($order->skip_address == 1){
					$orderAddresses = session()->get('paymentLink.orderAddresses');
					$product_weight = session()->get('paymentLink.products_weight');
					
					$product_shipping_amounts = session()->get('paymentLink.product_shipping_amounts');
					foreach($order->Products as $product){
						$order_product = OrderProducts::where('id',$product->id)->first();
						if(array_key_exists($product->id,$product_weight)){
							$order_product->product_weight = number_format(($product_weight[$product->id]['weight']), 2, '.', '');
						}
						$order_product->product_shipping = $product_shipping_amounts[$product->id];
						$order_product->save();
					
						/* Save Order Billing And Shipping Address */				
						
						$order_address = OrderAddress::where('order_id',$order_product->order_id)->where('order_product_id',$order_product->id)->first();
						$order_address->same_as_billing = $orderAddresses['same_as_billing'];
						if($orderAddresses['billing_type'] == 0){
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
							if(isset($orderAddresses['selectAddress']) && !empty($orderAddresses['selectAddress'])){
								$user_add = UserAddress::findOrFail($orderAddresses['selectAddress']);
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
							}else if($orderAddresses['shipping_type'] == 'multiple'){
								if(array_key_exists('option',$orderAddresses['multi'][$order_product->id]) && ($orderAddresses['multi'][$order_product->id]['option'] == 0 || $orderAddresses['multi'][$order_product->id]['option'] == '0')){
									$order_address->shipping_company_name = $orderAddresses['multi'][$order_product->id]['company_name'];
									$order_address->shipping_phone_number = $orderAddresses['multi'][$order_product->id]['phone_number'];
									$order_address->shipping_fname = $orderAddresses['multi'][$order_product->id]['fname'];
									$order_address->shipping_lname = $orderAddresses['multi'][$order_product->id]['lname'];
									$order_address->shipping_add1 = $orderAddresses['multi'][$order_product->id]['add1'];
									$order_address->shipping_add2 = $orderAddresses['multi'][$order_product->id]['add2'];
									$order_address->shipping_ship_in_care = $orderAddresses['multi'][$order_product->id]['ship_in_care'];
									$order_address->shipping_zipcode = $orderAddresses['multi'][$order_product->id]['zipcode'];
									$order_address->shipping_city = $orderAddresses['multi'][$order_product->id]['city'];
									$order_address->shipping_state = $orderAddresses['multi'][$order_product->id]['state'];
									$order_address->shipping_country = $orderAddresses['multi'][$order_product->id]['country'];
								}else{
									$user_add = UserAddress::findOrFail($orderAddresses['multi'][$order_product->id]['shippingAddress']);
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
							}/* 
							else{
								$user_add = UserAddress::findOrFail($orderAddresses['shipping_type']);
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
							} */
						}
						$order_address->save();
						
						/* End Save Order Address */
					}
					
					/* Save User Address */
					
					if($orderAddresses['billing_type'] == 0){
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
					}
					
					if($orderAddresses['same_as_billing'] == 0 && $orderAddresses['selectAddress'] == ''){
						if($orderAddresses['shipping_type'] == 'multiple'){
							foreach($orderAddresses['multi'] as $key=>$address){
								if(array_key_exists('option',$address) && ($address['option'] == 0 || $address['option'] == '0')){
									$user_address = new UserAddress();
									$user_address->user_id = $order->user_id;
									$user_address->type = 2;
									$user_address->address_name = $address['address_name'];
									$user_address->company_name = $address['company_name'];
									$user_address->phone_number = $address['phone_number'];
									$user_address->fname = $address['fname'];
									$user_address->lname = $address['lname'];
									$user_address->add1 = $address['add1'];
									$user_address->add2 = $address['add2'];
									$user_address->ship_in_care = $address['ship_in_care'];
									$user_address->zipcode = $address['zipcode'];
									$user_address->city = $address['city'];
									$user_address->state = $address['state'];
									$user_address->country = $address['country'];
									$user_address->save();
								}
							}
						}				
						else{
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
					
					$updateOrder = Orders::where('id',$order->id)->first();
					
					$updateOrder->skip_address = 0;
					$updateOrder->shipping_option = session()->get('shipping_option');
					
					if($orderAddresses['shipping_type'] == 'multiple'){
						$updateOrder->multiple_shipping = 1;
					}else{
						$updateOrder->multiple_shipping = 0;
					}
					
					$updateOrder->save();
					
					/* End Save User Address */
				}
				
				$free_shipping_ids = session()->get('paymentLink.free_shipping_ids');
				if(!empty($free_shipping_ids)){
					OrderProducts::whereIn('id',$free_shipping_ids)->update(['product_shipping'=>0.00]);
				}
				
				FunctionsHelper::sendOrderReceipt($order->id,'link');
				
				$responce['status'] = 'success';
				$responce['orderId'] = $order->id;
				session()->forget('paymentLink');
			}
		}
		\Session::flash('success', $data['msg']);
		return json_encode($responce);
	}
	
	public function paymentLinkCoupon(Request $request){
		$res['status'] = '';
		$res['code_apply'] = 0;
		$res['msg'] = '';
		if($request->isMethod('post')){
			$code = $request->all()['code'];
			$detail = Coupons::where('code',$code)->where('status',1)->first();
			
			$order_detail = Orders::where('id',$request->all()['order_id'])->with('orderProduct')->first();
			$gross_total = $order_detail->sub_total;
			
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
			else{
				if(!empty($detail->users)){
					$coupon_user_ids = explode(',',$detail->users);
					if(!in_array($order_detail->user_id,$coupon_user_ids)){
						$couponApply = 0;
						$res['msg'] .= 'This coupon code not valid for this order.<br/>';
					}
				}
				if($gross_total < $detail->min_cart){
					$couponApply = 0;
					$res['msg'] .= 'This coupon code apply minimum amount $'.$detail->min_cart.' .<br/>';
				}
				
				if($detail->single_time == 1 or $detail->single_time == '1'){
					$codeUsed = Orders::where('user_id',$order_detail->user_id)->where('discount_code',$code)->first();
					if(count($codeUsed) > 0){
						$couponApply = 0;
						$res['msg'] .= 'You have already used this coupon code..<br/>';
					}
				}
			
				// Below code for check coupon products are available in cart or not
				
				if(!empty($detail->products)){
					$coupon_products_ids = explode(',',$detail->products);
					$product_id_exist = false;
					foreach($order_detail->orderProduct as $product){
						if(in_array($product->product_id,$coupon_products_ids)){
							$product_id_exist = true;
						}
					}
					if(!$product_id_exist){
						$couponApply = 0;
						$res['msg'] .= 'This coupon code not available for your order products.<br/>';
					}
				}
			}
			if($couponApply){
				if($detail->type == 'amount'){
					$discount = $detail->type_value;	
					$discount = number_format($discount,2,'.','');
					
					session()->put('paymentLink.discount', $discount);
					session()->put('paymentLink.discount_code', $code);
					session()->put('paymentLink.free_shipping_ids', array());
					
					$total = number_format((session()->get('paymentLink.gross') - session()->get('paymentLink.discount') + session()->get('paymentLink.shipping')),2,'.','');
					if($total < 0){
						$total = 0.00;
					}
					session()->put('paymentLink.total', $total);
					
					$res['code_apply'] = 1;
					$res['code_type'] = 'amount';
					$res['gross'] = $gross_total;
					$res['discount_amount'] = $discount;
					$res['shipping'] = session()->get('paymentLink.shipping');
					$res['total'] = $total;
					$res['msg'] = 'This coupon code applied on your order.';
					session()->put('paymentLink.coupon_msg', $res['msg']);
				}
				else if($detail->type == 'percent'){
					$discount = number_format((($gross_total*$detail->type_value)/100),2,'.','');
					if(!empty($detail->max_discount) && $discount > $detail->max_discount){
						$discount = $detail->max_discount;
					}
					$discount = number_format($discount,2,'.','');
					
					session()->put('paymentLink.gross', $order_detail->sub_total);
					session()->put('paymentLink.discount', $discount);	
					session()->put('paymentLink.discount_code', $code);
					session()->put('paymentLink.shipping', $order_detail->shipping_fee);
					session()->put('paymentLink.free_shipping_ids', array());
					
					$total = number_format((session()->get('paymentLink.gross') - session()->get('paymentLink.discount') + session()->get('paymentLink.shipping')),2,'.','');
					if($total < 0){
						$total = 0.00;
					}
					session()->put('paymentLink.total', $total);
					
					$res['code_apply'] = 1;
					$res['code_type'] = 'percent';
					$res['gross'] = $gross_total;
					$res['discount_amount'] = $discount;
					$res['shipping'] = session()->get('paymentLink.shipping');
					$res['total'] = $total;
					$res['msg'] = 'This coupon code applied on your order.';
					session()->put('paymentLink.coupon_msg', $res['msg']);
				}else if($detail->type == 'free_shipping'){
					$coupon_products_ids = explode(',',$detail->products);
					$free_shipping_ids = array();
					$cart_count = count($order_detail->orderProduct);
					$shipping_fee = 0.00;
					foreach($order_detail->orderProduct as $product){
						if(in_array($product['product_id'],$coupon_products_ids)){
							$free_shipping_ids[] = $product['id'];
							$cart_count--;
						}else{
							$shipping_fee += $product->product_shipping;
						}
					}
					//echo $shipping_fee;
					//pr($free_shipping_ids);die;
					
					if(!empty($free_shipping_ids)){
						
						session()->put('paymentLink.gross', $order_detail->sub_total);
						session()->put('paymentLink.discount', 0.00);	
						session()->put('paymentLink.discount_code', $code);
						session()->put('paymentLink.shipping', $shipping_fee);
						session()->put('paymentLink.free_shipping_ids', $free_shipping_ids);
						
						$total = number_format((session()->get('paymentLink.gross') - session()->get('paymentLink.discount') + session()->get('paymentLink.shipping')),2,'.','');
						if($total < 0){
							$total = 0.00;
						}
						session()->put('paymentLink.total', $total);
						$res['code_apply'] = 1;
						$res['gross'] = $gross_total;
						$res['discount_amount'] = 0.00;
						$res['shipping'] = session()->get('paymentLink.shipping');
						$res['total'] = $total;
						
						if($cart_count == 0){
							session()->put('paymentLink.free_shipping', 1);
							$res['code_type'] = 'free_shipping';
							$res['msg'] = 'Free shipping applied on your order.';
						}else{								
							session()->put('paymentLink.free_shipping', 0);
							$res['code_type'] = '';
							$res['msg'] = 'Free shipping applied on your cart products.';
						}
						session()->put('paymentLink.coupon_msg', $res['msg']);
					}else{
						$res['msg'] = 'This coupon code not valid for your order products.';
						session()->put('paymentLink.coupon_msg', '');
					}
				}
			}else{
				$res['msg'] = 'You enter invalid coupon code';
				session()->put('paymentLink.coupon_msg', '');
				session()->put('paymentLink.free_shipping', 0);
			}
			$res['status'] = 'success';
		}
		return json_encode($res);
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
			
			//pr($data);die;

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
			
			$result = $payment->payment_link(config('constants.Authorize_loginId'),config('constants.Authorize_transactionId'),$total,$data['card_number'],$data['cvv_number'],$expiry_date,$data['order_id']);
			//pr($result);die;
			
			if($result['status'] == 'success'){
				if($data['save_card'] == 1){
					$card = new UserCards();
					$card->user_id = $data['user_id'];
					$card->card_number = $data['card_number'];
					$card->expire_date = date('m-Y',strtotime($expiry_date));
					$card->save();
				}
			}
		}
		//pr($result);die;
		return json_encode($result);
	}
}
