<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ups;
use DB;
use PDF;
use Form;
use App\User;
use App\UserAddress;
use App\Designers;
use App\Vendors;
use App\Notes;
use App\Events;
use App\Messages;
use App\Category;
use App\Products;
use App\CustomOptions;
use App\ProductOptions;
use App\Orders;
use App\OrderProducts;
use App\OrderFiles;
use App\OrderProductOptions;
use App\OrderAddress;
use App\Discounts;
use App\ProductVariant;
use App\Coupons;
use App\State;
use App\ProductShipping;
use App\ProductVariantPrice;
use App\OrderPo;
use App\OrderPoDetails;
use App\OrderPoOptions;
use App\OrderPOAddress;

use \App\Helpers\UPSShipping;
use \App\Helpers\CalculateShippingWeight;

use Exception;
use \App\Helpers\FunctionsHelper;


class OrdersController extends Controller
{
	public function __construct(){
		$this->middleware('auth',['except' => ['pdf_view','print_view']]);
    }
	
    public function add(Request $request){					

		$pageTitle = "Order Add";
		$users = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',3)->where('status', 1)->pluck('name','id')->all();
		
		//$categories = Category::where('status',1)->pluck('name','id')->all();
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
		$states = State::where('status',1)->pluck('stateName','stateCode')->all();
		
		if($request->isMethod('post')){
			$data = $request->all();
			/* pr($data);
			pr(session()->get('products_weight'));
			pr(session()->get('carttotal'));
			pr(session()->get('cart'));
			die; */
			
			$products = session()->get('cart');
			$product_weight = session()->get('products_weight');
			$product_shipping_amounts = session()->get('product_shipping_amounts');
			
			if(!array_key_exists('billing_add_option',$data)){
				$data['billing_add_option'] = 0;
			}
			if(!array_key_exists('shipping_add_option',$data)){
				$data['shipping_add_option'] = 0;
			}
			if(!array_key_exists('multiple_shipping',$data)){
				$data['multiple_shipping'] = 0;
			}
			$validArr = [
                'customer' => 'required',
                'category' => 'required',
                'product' => 'required',
            ];
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
				if($data['customer'] == 0){
					$data['password'] = '123456';
					$user = new User();					
					$user->fname = $data['fname'];
					$user->lname = $data['lname'];
					$user->email = $data['email'];
					$user->password = bcrypt($data['password']);
					$user->phone_number = $data['phone_number'];
					$user->company_name = $data['company_name'];
					$user->status = '1';
					$user->save();
					$customer_id = $user->id;
					$user_detail = User::where('id',$customer_id)->first();
					$email = $data['email'];
					$name = $data['fname'].' '.$data['lname'];

					$params = array(
							'slug'=>'admin_user_register',
							'to'=>$data['email'],
							'params'=>array(
										'{{name}}'=>$data['fname'].' '.$data['lname'],
										'{{EMAIL}}'=>$data['email'],
										'{{PASSWORD}}'=>$data['password'],
										'{{SITE_URL}}'=>config('constants.SITE_URL'),
										'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
										'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
										'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
										)
							);
					parent::sendMail($params);
				}
				else{
					$customer_id = $data['customer'];
					$user_detail = User::where('id',$customer_id)->first();
					$email = $user_detail->email;
					$name = $user_detail->fname.' '.$user_detail->lname;
				}
				
				$order = new Orders();
				$order->user_id = $customer_id;
				if(\Auth::user()->role_id == 2){
					$order->agent_id = \Auth::user()->id;
				}
				$order->sub_total =  number_format((float)(session()->get('carttotal.gross')), 2, '.', '');
				$order->shipping_fee =  session()->get('carttotal.shipping');
				$order->sales_tax =  session()->get('carttotal.sales_tax');
				$order->total =  number_format((float)(session()->get('carttotal.total')), 2, '.', '');
				
				$order->shipping_option = $data['shipping_option'];
				//$order->rush_fee = $data['rush_fee'];
				$order->multiple_shipping = $data['multiple_shipping'];
				$order->customer_status = 0;
				$order->payment_status = 0;
				$order->status = 1;
				if($order->save()){
					
					/* Order Product Save */
					$i = 1;
					foreach($products as $cartkey=>$product){
						$order_product = new OrderProducts();
						$order_product->order_id = $order->id;
						$order_product->item_id = $order->id.'-'.$i;
						$order_product->product_id = $product['product_id'];
						$order_product->price_default = $product['price_default'];
						$order_product->price = number_format((float)($product['price']), 2, '.', '');
						$order_product->gross_total = number_format((float)($product['gross_total']), 2, '.', '');
						$order_product->qty = $product['quantity'];
						$order_product->qty_discount = number_format((float)($product['qty_discount']), 2, '.', '');
						$order_product->total = number_format((float)($product['total']), 2, '.', '');
						
						if(array_key_exists($cartkey,(array)$product_weight)){
							$order_product->product_weight = number_format(($product_weight[$cartkey]['weight']), 2, '.', '');
						}
						
						$order_product->product_shipping = @$product_shipping_amounts[$cartkey];
					
						$product_info = Products::findOrFail($product['product_id']);
						if($product_info->no_artwork_required){
							//$order_product->customer_status = 3;
							$order_product->art_work_status = 2;
						}else{
							//$order_product->customer_status = 2;
							$order_product->art_work_status = 1;
						}
						//$order_product->art_work_link = url('/'.$product_info->slug);
						
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

						if($data['skip_address'] == 1)
						{
							$order->skip_address = 1;
							$order->save();

							$order_address = new OrderAddress();
							$order_address->order_id = $order->id;
							$order_address->product_id = $product['product_id'];
							$order_address->order_product_id = $order_product->id;
							$order_address->save();
						}
						else
						{						
							$order_address = new OrderAddress();
							$order_address->order_id = $order->id;
							$order_address->product_id = $product['product_id'];
							$order_address->order_product_id = $order_product->id;
							if($data['billing_add_option'] == 0){
								
								/* Save User Billing Address */
								UserAddress::where('user_id',$customer_id)->where('type',1)->update(['status'=>0]);
								$user_address = new UserAddress();
								$user_address->user_id = $customer_id;
								$user_address->type = 1;
								$user_address->company_name = $data['billing_company_name'];
								$user_address->phone_number = $data['billing_phone_number'];
								$user_address->fname = $data['billing_fname'];
								$user_address->lname = $data['billing_lname'];
								$user_address->add1 = $data['billing_address1'];
								$user_address->add2 = $data['billing_address2'];
								$user_address->zipcode = $data['billing_zipcode'];
								$user_address->city = $data['billing_city'];
								$user_address->state = $data['billing_state'];
								$user_address->country = $data['billing_country'];
								$user_address->save();
								
								$order_address->billing_add_id = $user_address->id;
								$order_address->billing_company_name = $data['billing_company_name'];
								$order_address->billing_phone_number = $data['billing_phone_number'];
								$order_address->billing_fname = $data['billing_fname'];
								$order_address->billing_lname = $data['billing_lname'];
								$order_address->billing_add1 = $data['billing_address1'];
								$order_address->billing_add2 = $data['billing_address2'];
								$order_address->billing_zipcode = $data['billing_zipcode'];
								$order_address->billing_city = $data['billing_city'];
								$order_address->billing_state = $data['billing_state'];
								$order_address->billing_country = $data['billing_country'];
							
							}else{
								$user_add = UserAddress::findOrFail($data['billing_add_option']);
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
							}
						
							if($data['multiple_shipping'] == 0){
								if($data['shipping_add_option'] == 0){
									$order_address->shipping_company_name = $data['shipping_company_name'];
									$order_address->shipping_phone_number = $data['shipping_phone_number'];
									$order_address->shipping_fname = $data['shipping_fname'];
									$order_address->shipping_lname = $data['shipping_lname'];
									$order_address->shipping_add1 = $data['shipping_address1'];
									$order_address->shipping_add2 = $data['shipping_address2'];
									$order_address->shipping_ship_in_care = $data['shipping_ship_in_care'];
									$order_address->shipping_zipcode = $data['shipping_zipcode'];
									$order_address->shipping_city = $data['shipping_city'];
									$order_address->shipping_state = $data['shipping_state'];
									$order_address->shipping_country = $data['shipping_country'];
									
									/* Save User Shipping Address */
						
									$user_address = new UserAddress();
									$user_address->user_id = $customer_id;
									$user_address->type = 2;
									$user_address->address_name = $data['shipping_address_name'];
									$user_address->company_name = $data['shipping_company_name'];
									$user_address->phone_number = $data['shipping_phone_number'];
									$user_address->fname = $data['shipping_fname'];
									$user_address->lname = $data['shipping_lname'];
									$user_address->add1 = $data['shipping_address1'];
									$user_address->add2 = $data['shipping_address2'];
									$user_address->zipcode = $data['shipping_zipcode'];
									$user_address->city = $data['shipping_city'];
									$user_address->state = $data['shipping_state'];
									$user_address->country = $data['shipping_country'];
									$user_address->save();
								}else{
									$user_add = UserAddress::findOrFail($data['shipping_add_option']);
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
							else{
								if($data['multi'][$cartkey]['option'] == 0){
									$order_address->shipping_company_name = $data['multi'][$cartkey]['shipping_company_name'];
									$order_address->shipping_phone_number = $data['multi'][$cartkey]['shipping_phone_number'];
									$order_address->shipping_fname = $data['multi'][$cartkey]['shipping_fname'];
									$order_address->shipping_lname = $data['multi'][$cartkey]['shipping_lname'];
									$order_address->shipping_add1 = $data['multi'][$cartkey]['shipping_address1'];
									$order_address->shipping_add2 = $data['multi'][$cartkey]['shipping_address2'];
									$order_address->shipping_ship_in_care = $data['multi'][$cartkey]['shipping_ship_in_care'];
									$order_address->shipping_zipcode = $data['multi'][$cartkey]['shipping_zipcode'];
									$order_address->shipping_city = $data['multi'][$cartkey]['shipping_city'];
									$order_address->shipping_state = $data['multi'][$cartkey]['shipping_state'];
									$order_address->shipping_country = $data['multi'][$cartkey]['shipping_country'];
								
									/* Save User Shipping Address */
						
									$user_address = new UserAddress();
									$user_address->user_id = $customer_id;
									$user_address->type = 2;
									$user_address->address_name = $data['multi'][$cartkey]['shipping_address_name'];
									$user_address->company_name = $data['multi'][$cartkey]['shipping_company_name'];
									$user_address->phone_number = $data['multi'][$cartkey]['shipping_phone_number'];
									$user_address->fname = $data['multi'][$cartkey]['shipping_fname'];
									$user_address->lname = $data['multi'][$cartkey]['shipping_lname'];
									$user_address->add1 = $data['multi'][$cartkey]['shipping_address1'];
									$user_address->add2 = $data['multi'][$cartkey]['shipping_address2'];
									$user_address->ship_in_care = $data['multi'][$cartkey]['shipping_ship_in_care'];
									$user_address->zipcode = $data['multi'][$cartkey]['shipping_zipcode'];
									$user_address->city = $data['multi'][$cartkey]['shipping_city'];
									$user_address->state = $data['multi'][$cartkey]['shipping_state'];
									$user_address->country = $data['multi'][$cartkey]['shipping_country'];
									$user_address->save();
								}else{
									$user_add = UserAddress::findOrFail($data['multi'][$cartkey]['option']);
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
							$order_address->save();
							$i++;
						}	
					}
					
					// Below Code for check all order products files are uploaded or not //
					
					/*$check_status = DB::select('SELECT count(*) as art_work_status , (SELECT COUNT(*) FROM `order_products` WHERE `order_id` = '.$order->id.') as total_product FROM `order_products` WHERE `order_id` = '.$order->id.' AND art_work_status >=2');
					if($check_status[0]->art_work_status == $check_status[0]->total_product){
						Orders::where('id',$order->id)->update(['customer_status'=>3]);
					}*/
					
					// Remove Cart Items  from session//
					session()->forget('cart');
					
					// Mail Payment Link To User //
					if(isset($data['send_estimate']))
					{
						//FunctionsHelper::sendOrderReceipt($order->id,'estimate');

						return redirect('/admin/order/order-mail/'.$order->id);
					}					
					
					\Session::flash('success', 'Order created.');
					return redirect('/admin/order/lists');
				}
				else{
					\Session::flash('error', 'Order not created.');
					session()->forget('cart');
					return redirect('/admin/order/add');
				}
			}
			else{
				\Session::flash('error', 'Order not added.');
				return redirect('/admin/order/add')->withErrors($validation)->withInput();
        	}
		}
		else{
			session()->forget('cart');
		}
		return view('Admin/orders/add',compact('pageTitle','users','categories','states'));
	}
	
	public function edit($id,$type=null){
		$pageTitle = "Order Edit";
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$db = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress','files']);
		$order = $db->first();		
		if(count($order) > 0){			
			//pr($order->toArray());die;
			return view('Admin/orders/edit',compact('pageTitle','order','agents','type'));
		}else{
			return redirect('/admin/order/lists');
		}
	}
	
	public function po($id,$product_id=null,$type=null){
		$pageTitle = "Create Order PO";
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$vendor = DB::table('order_products')->where('order_id',$id)->where('product_id',$product_id)->first();
		//pr($vendor);die;
		
		$db = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress','files']);
		$order = $db->first();
		if(count($order) > 0 and count($vendor) > 0){			
			//pr($order->toArray());die;
			return view('Admin/orders/po',compact('pageTitle','order','agents','type','product_id','vendor'));
		}else{
			return redirect('/admin/order/lists');
		}
	}
	
	public function delete($order_id){
		
		// Delete Order Products //
		orderProducts::where('order_id',$order_id)->delete();
		
		// Delete Order Addresses //
		OrderAddress::where('order_id',$order_id)->delete();
		
		// Delete Order Product options //
		OrderProductOptions::where('order_id',$order_id)->delete();
		
		// Delete Order Product Files //
		$files = OrderFiles::where('order_id',$order_id)->get();
		foreach($files as $file){
			unlink(public_path('/uploads/orders/'.$file->name));
		}
		OrderFiles::where('order_id',$order_id)->delete();
		
		// Delete Order //
		Orders::where('id',$order_id)->delete();
		
		\Session::flash('success', 'Order deleted successfully.');
		return \Redirect::back();
		//return \Redirect::to('/admin/order/lists');
	}
	
	public function pdf_view($id){
		$pageTitle = "";
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$db = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress']);
		$order = $db->first();
		
		//PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','debugCss'=>true]);
		//$pdf= PDF::loadHtml(view('Admin/orders/pdf',compact('pageTitle','order','agents')));
		//$pdf = PDF::loadView('Admin/orders/pdf',compact('pageTitle','order','agents'));
		//return $pdf->stream();
		
		return view('Admin/orders/pdf',compact('pageTitle','order','agents'));
	}
	
	public function createinvoice($id = null){
		
		$url = url("admin/order/pdf_view/".$id);
		
		$file_name = 'EasyOrderBanners_Order_'.$id.'_Receipt.pdf';
		$file_path = "public/pdf/".$file_name;
				
		if($_SERVER['SERVER_NAME']=='192.168.1.77')
		{
			$output		=	exec("phantomjs pages.js  $url $file_path 2>&1"); 
		}
		else
		{
			$exe = "/home/octallabs/public_html/56/admin/phantomjs/bin/phantomjs";
			$output = exec("$exe --ssl-protocol=any --ignore-ssl-errors=yes pages.js  $url $file_path 2>&1");
		}
		
		header('Content-Type: application/pdf');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=" . $file_name);
		readfile($file_path);
		exit;
	}
	
	public function print_view($id){
		$pageTitle = "";
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$db = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress']);
		$order = $db->first();
		
		return view('Admin/orders/print',compact('order','agents'));
	}
	
	public function get_product_by_category($category_id=null){
		
		$products = Products::where('category_id',$category_id)->where('status',1)->pluck('name','id')->all();
		$responce['status'] = 'success';
		$responce['html'] = '<option value="">Select Product</option>';
		foreach($products as $key=>$val){
			$responce['html'] .= "<option value='".$key."'>".$val."</option>";
		}
		return json_encode($responce);
		
	}
	
	public function get_product_options($product_id=null){
		$responce['status'] = 'success';
		$html = array();
		$html['printing'] = '';
		$html['finishing'] = '';
		$html['production'] = '';
		
		$product = Products::where('id',$product_id)->where('status',1)->with('shipping','variants','variantCombinantion')->first();
		$sub_total = $product->price;
		
		$options = ProductOptions::where('product_id',$product_id)->with('CustomOption')->get();
		
		foreach($options as $val){
					
			if($val->CustomOption->option_type == 1){
				$values = json_decode($val->CustomOption->option_keys,true);				
				$html[$val->CustomOption->field_group] .=  '<div class="col-xs-12 col-sm-6 form-group">';
				
				$html[$val->CustomOption->field_group] .= Form::label('options['.$val->CustomOption->field_group.']['.$val->CustomOption->id.']'.'['.str_replace(' ','_',strtolower($val->CustomOption->name)).']',$val->CustomOption->name,array('class'=>'form-control-label'));
				
				$html[$val->CustomOption->field_group] .= '<select class="form-control option_fields option_custom" name="options['.$val->CustomOption->field_group.']['.$val->CustomOption->id.']'.'['.str_replace(' ','_',strtolower($val->CustomOption->name)).']" id="option_custom'.$val->CustomOption->id.'" data-name="'.$val->CustomOption->name.'" data-id="'.$val->CustomOption->id.'" required><option value="" rel="0" data-price="0" data-price-type="" selected="selected">Select</option>';
				
				foreach($values as $value){
					$price = 0;
					if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
					$weight = 0;
					if(array_key_exists('weight',$value) and !empty($value['weight'])){ $weight = $value['weight']; }

					$flat_rate_additional_price = 0;
					if(array_key_exists('flat_rate_additional_price',$value) and !empty($value['flat_rate_additional_price'])){ $flat_rate_additional_price = $value['flat_rate_additional_price']; }

					$selected ='';					
					if(array_key_exists('default',$value) and ($value['default']==1))
					{
						$selected ='selected';
						$sub_total = $sub_total + $value['price'];
					}						

					$html[$val->CustomOption->field_group] .= '<option value=\''.htmlentities($value['value']).'__'.$price.'\' rel="'.$price.'" data-price="'.$price.'" data-flat_rate_additional_price="'.$flat_rate_additional_price.'" data-weight="'.$weight.'" data-price-type="'.$val->CustomOption->price_formate.'" '.$selected.'>'.htmlentities($value['value']).'</option>';
				}
				$html[$val->CustomOption->field_group] .= '</select></div>';
			}
			if($val->CustomOption->option_type == 2){
				$values = json_decode($val->CustomOption->option_keys,true);
				$html[$val->CustomOption->field_group] .= '<div class="col-xs-12 col-sm-6 form-group">';
				
				$html[$val->CustomOption->field_group] .= Form::label('options['.$val->CustomOption->field_group.']['.$val->CustomOption->id.']'.'['.str_replace(' ','_',strtolower($val->CustomOption->name)).']',$val->CustomOption->name,array('class'=>'form-control-label'));
				foreach($values as $value){
					$price = 0;
					if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
					
					$html[$val->CustomOption->field_group] .= Form::text('options['.$val->CustomOption->field_group.']['.$val->CustomOption->id.']'.'['.str_replace(' ','_',strtolower($val->CustomOption->name)).']','',['class'=>'form-control option_fields','placeholder'=>$val->CustomOption->name,'rel'=>$price,'required']);
				}
				$html[$val->CustomOption->field_group] .=  '</div>';
			}
			
		}
		//pr($html); echo $sub_total;die();
		$session_total = 0;
		if(session()->has('cart')){
			foreach(session()->get('cart') as $val){
				$session_total += $val['total'];
			}
		}
		
		$responce['status'] = "success";
		$responce['price_sqft_area'] = $product->price_sqft_area;
		$responce['variant_check'] = $product->variant;
		$responce['show_width_height'] = $product->show_width_height;
		
		$responce['session_total'] = $session_total;
		$responce['html'] = '<div class="row order_option_box">';
		$responce['html'] .= Form::hidden('cartkey',null,['class'=>'form-control']);
		$responce['html'] .= Form::hidden('price_default',$product->price,['class'=>'form-control']);
		$responce['html'] .= Form::hidden('product_weight',$product->shipping_weight,['class'=>'form-control']);
		$responce['html'] .= Form::hidden('min_width',(!empty($product->min_width))?$product->min_width:0);
		$responce['html'] .= Form::hidden('max_width',(!empty($product->max_width))?$product->max_width:0);
		$responce['html'] .= Form::hidden('min_height',(!empty($product->min_height))?$product->min_height:0);
		$responce['html'] .= Form::hidden('max_height',(!empty($product->max_height))?$product->max_height:0);
		$responce['html'] .= Form::hidden('sub_total',$sub_total,['class'=>'form-control sub_total']);

		$responce['html'] .= '<div class="form-group col-xs-12"><h3 class="page-header">Printing Options</h3><div class="col-xs-12 col-sm-6 form-group">'.Form::label('printing[quantity]','Quantity',array('class'=>'form-control-label')). Form::number('quantity',1,['class'=>'form-control option_fields','id'=>'quantity','min'=>1]).'</div>';

		foreach($product->variants as $variant){
			$variant_options = array();
			foreach($variant->variantValues as $value){
				$variant_options[$value->id] = $value->value;
			}
			//pr($variant_options);
			$responce['html'] .= '<div class="col-xs-12 col-sm-6 form-group">'.Form::label('variants['.$variant->id.']',$variant->name,array('class'=>'form-control-label')). Form::select('variants['.$variant->id.']',$variant_options,'',['class'=>'form-control option_fields variants_option','id'=>'']).'</div>';
		}
		
		if($product->show_width_height == 1){
			$responce['html'] .= '<div class="col-xs-12 col-sm-6 form-group">'.Form::label('options[printing][0][width]','Width(ft)',array('class'=>'form-control-label')). Form::number('options[printing][0][width]',(!empty($product->min_width))?$product->min_width:1,['class'=>'form-control option_fields','id'=>'width','min'=>1]).'</div><div class="col-xs-12 col-sm-6 form-group">'.Form::label('options[printing][0][height]','Height(ft)',array('class'=>'form-control-label')). Form::number('options[printing][0][height]',(!empty($product->min_height))?$product->min_height:1,['class'=>'form-control option_fields','id'=>'height','min'=>1]).'</div>';
		}
		
		

		if($html['printing'] != ''){			
			$responce['html'] .= $html['printing'].'</div>';
		}else{
			$responce['html'] .= '</div>';
		}
		
		if($html['finishing'] != ''){
			$responce['html'] .= '<div class="form-group col-xs-12"><h3 class="page-header">Finishing Options</h3>'.$html['finishing'].'</div>';
		}
		if($html['production'] != ''){	
			$responce['html'] .= '<div class="form-group col-xs-12"><h3 class="page-header">Production Options</h3>'.$html['production'].'</div>';
		}
		$responce['html'] .= '<div class="col-xs-12 text-right"><button type="button" class="btn btn-warning " style="margin-right: 5px;">Product Amount <span id="product_amount">$'.$sub_total.'</span></button><button type="button" class="btn btn-info " onclick="add_to_cart()"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Add to Cart</button></div>';
		$responce['html'] .= '</div>';

		// Make a array of old combinations //
		$sets = array();
		//pr($product->variantCombinantion);
		if(count($product->variantCombinantion) > 0){
			$i = 0;
			foreach($product->variantCombinantion as $val){
				$key = $val->varient_id1;
				if($val->varient_id2 != ''){
					$key .= '-'.$val->varient_id2;
				}
				if($product->price_sqft_area == 1){
					$sets[$key]['variable_price'] = 1;
					$sets[$key]['price'][$i]['price'] = $val['price'];  
					$sets[$key]['price'][$i]['min_area'] = $val['min_area'];  
					$sets[$key]['price'][$i]['max_area'] = $val['max_area'];  
				}else{
					$sets[$key]['variable_price'] = 0;
					$sets[$key]['price'] = $val['price'];
				}
				$i++;
			}
		}
		//pr($sets);die;
		$discount = array();
		$discount_all = Discounts::where('status',1)->orderBy('quantity','ASC')->get();
		foreach($discount_all as $val){
			if(empty($val->products)){
				$discount[$val->quantity] = $val->percent;
			}else{
				$ids = explode(',',$val->products);
				if(in_array($product->id,$ids)){
					$discount[$val->quantity] = $val->percent;
				}
			}
		}
		
		$responce['combination'] = $sets;
		$responce['discount'] = $discount;
		$responce['shipping_type'] = (isset($product->shipping->type))?$product->shipping->type:'';
		$responce['shipping_min_value'] = (isset($product->shipping->min_value))?$product->shipping->min_value:'';
		$responce['shipping_weight'] = (isset($product->shipping->weight))?$product->shipping->weight:'';
		$responce['shipping_price'] = (isset($product->shipping->price))?$product->shipping->price:'';
		//pr($responce);die;
		return json_encode($responce,JSON_PRETTY_PRINT);
	}
	
	public function formvalidate(Request $request){
		$responce['flag'] = 0;
		$responce['res'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			$validArr = [
				//'fname' => 'required|string|max:255',
				//'lname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:users',
				//'phone_number' => 'required|min:10|numeric',
			];
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$responce['flag'] = 1;
			}
			else{
				$responce['res'] = $validation->errors();
			}
			return json_encode($responce);
		}
	}
	
	public function useraddress(Request $request){
		$responce['status'] = 0;
		$responce['res'] = array();
		$responce['res']['billing'] = '';
		$responce['res']['shipping'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;			
			if($data['customer'] != 0 and $data['customer'] != ''){
				$addresses = UserAddress::where('user_id',$data['customer'])->get();
				if(count($addresses) > 0){
					foreach($addresses as $val){
						$add_str = '';
						if($val->company_name !=""){
							$add_str .= $val->company_name.'<br/>';						
						}
						if($val->phone_number !=""){
							$add_str .= $val->phone_number.'<br/>';						
						}
						if($val->fname !=""){
							$add_str .= $val->fname.'&nbsp;'.$val->lname.'<br/>';						
						}
						
						$add_str .= $val->add1.'<br/>';
						if(!empty($val->add2)){
							$add_str .= $val->add2.'<br/>';
						}
						if($val->ship_in_care !=""){
							$add_str .= $val->ship_in_care.'<br/>';						
						}
						$add_str .= $val->city.',';
						$add_str .= $val->state.',';
						$add_str .= $val->zipcode;
						
						if($val->type == 1){
							$responce['res']['billing'] = '<div class="col-xs-6 col-sm-6 col-md-6 invoice-col"><address class="checked_address"><input type="radio" name="billing_add_option" value="'.$val->id.'" class="flat-red" checked>'.$add_str.'</address></div><div class="col-xs-6 invoice-col"><label class="checked_address"><input type="radio" name="billing_add_option" value="0" class="flat-red"> Add New Address</label></div>';
						}
						else{
							$responce['res']['shipping'] .= '<div class="col-xs-6 col-sm-6 col-md-4 invoice-col"><address class="checked_address"><input type="radio" name="shipping_add_option" value="'.$val->id.'" class="flat-red">'.$add_str.'</address></div>';
						}
					}
				}
				else{
					$responce['res']['billing'] = '<div class="col-xs-6 invoice-col"><label class="checked_address"><input type="radio" name="billing_add_option" value="0" class="flat-red"> Add New Address</label></div>';
				}
			}
			$responce['res']['shipping'] .= '<div class="col-xs-12 col-sm-6 col-md-4 invoice-col"><label class="checked_address"><input type="radio" name="shipping_add_option" value="0" class="flat-red"> Add New Address</label></div>';
			
			$responce['status'] = 1;
		}		
		return json_encode($responce);
	}
	
	public function productaddress(Request $request){
		$responce['status'] = 0;
		$responce['shipping'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$states = State::where('status',1)->pluck('stateName','stateCode')->all();
			if(session()->has('cart')){
				foreach(session()->get('cart') as $cartkey=>$value){
					$detail = Products::select('name')->where('id',$value['product_id'])->get()->toArray();
					$responce['shipping'] .= '<label class="col-sm-3">Shipping Address for product "'.$detail[0]['name'].'"</label><div class="col-sm-9" multi_add_div><div class="row">';
					$addresses = UserAddress::where('user_id',$data['customer'])->get();
					if(count($addresses) > 0){
						$class = 'hide';
						$i = 1;
						foreach($addresses as $val){
							if($val->type != 1){
								$add_str = '';

								if($val->company_name !=""){
									$add_str .= $val->company_name.'<br/>';						
								}
								if($val->phone_number !=""){
									$add_str .= $val->phone_number.'<br/>';						
								}
								if($val->fname !=""){
									$add_str .= $val->fname.'&nbsp;'.$val->lname.'<br/>';						
								}
								
								$add_str .= $val->add1.'<br/>';
								if(!empty($val->add2)){
									$add_str .= $val->add2.'<br/>';
								}
								if($val->ship_in_care !=""){
									$add_str .= $val->ship_in_care.'<br/>';						
								}

								$add_str .= $val->city.',';
								$add_str .= $val->state.',';
								$add_str .= $val->zipcode;
								
								($i == 1)?$check = 'checked':$check = '';
								$responce['shipping'] .= '<div class="col-xs-6 col-sm-6 col-md-4 invoice-col"><address class="checked_address"><input type="radio" name="multi['.$cartkey.'][option]" value="'.$val->id.'" data="'.$cartkey.'" class="flat-red multi_shipping_add_option" '.$check.'>'.$add_str.'</address></div>';
								$i++;
							}
						}
						$check = '';
					}else{
						$class = '';
						$check = 'checked';
					}
					
					$responce['shipping'] .= '<div class="col-xs-12 col-sm-6 col-md-4 invoice-col"><label class="checked_address"><input type="radio" name="multi['.$cartkey.'][option]" value="0" data="'.$cartkey.'" class="flat-red multi_shipping_add_option" '.$check.'> Add New Address</label></div></div>';
					
					$responce['shipping'] .= '<div id="multi_shipping_add_'.$cartkey.'" class="col-md-12 	
											multi_shipping order_option_box '.$class.'">';

					$responce['shipping'] .='<div class="form-group col-sm-12 col-xs-12">'.Form::label('shipping_address_name','Address Name',array('class'=>'form-control-label')).Form::text('shipping_address_name','',['class'=>'form-control  opt','placeholder'=>'Address Name']).'</div><div class="form-group col-sm-6 col-xs-12">'.Form::label('shipping_company_name','Company Name',array('class'=>'form-control-label')).Form::text('shipping_company_name','',['class'=>'form-control  opt','placeholder'=>'Company Name']).'</div><div class="form-group col-sm-6 col-xs-12">'.Form::label('shipping_phone_number','Phone Number',array('class'=>'form-control-label')).Form::text('shipping_phone_number','',['class'=>'form-control  opt','placeholder'=>'Phone Number']).'</div><div class="form-group col-sm-6 col-xs-12">'.Form::label('shipping_fname','First Name',array('class'=>'form-control-label')).Form::text('shipping_fname','',['class'=>'form-control','placeholder'=>'First Name']).'</div><div class="form-group col-sm-6 col-xs-12">'.Form::label('shipping_lname','Last Name',array('class'=>'form-control-label')).Form::text('shipping_lname','',['class'=>'form-control','placeholder'=>'Last Name']).'</div><div class="form-group col-sm-6 col-xs-12">'.Form::label('multi['.$cartkey.'][shipping_address1]','Address Line 1',array('class'=>'form-control-label')).Form::text('multi['.$cartkey.'][shipping_address1]','',['class'=>'form-control shipping_input','placeholder'=>'Address Line 1']).'</div><div class="form-group col-sm-6 col-xs-12">'.Form::label('multi['.$cartkey.'][shipping_address2]','Address Line 2',array('class'=>'form-control-label')).Form::text('multi['.$cartkey.'][shipping_address2]','',['class'=>'form-control shipping_input add2  opt','placeholder'=>'Address Line 2']).'</div><div class="form-group col-sm-12 col-xs-12">'.Form::label('shipping_ship_in_care','Ship in care of',array('class'=>'form-control-label')).Form::text('shipping_ship_in_care','',['class'=>'form-control shipping_input add2  opt','placeholder'=>'Ship in care of']).'</div><div class="clearfix"></div><div class="form-group col-sm-6 col-xs-12">'.Form::label('multi['.$cartkey.'][shipping_zipcode]','Zipcode',array('class'=>'form-control-label')).Form::number('multi['.$cartkey.'][shipping_zipcode]','',['class'=>'form-control shipping_input','placeholder'=>'Zipcode']).'</div><div class="form-group col-sm-6 col-xs-12">'.Form::label('multi['.$cartkey.'][shipping_city]','City',array('class'=>'form-control-label')).Form::text('multi['.$cartkey.'][shipping_city]','',['class'=>'form-control shipping_input','placeholder'=>'City']).'</div><div class="clearfix"></div><div class="form-group col-sm-6 col-xs-12">'.Form::label('multi['.$cartkey.'][shipping_state]','State',array('class'=>'form-control-label')).Form::select('multi['.$cartkey.'][shipping_state]',$states,'',['class'=>'form-control shipping_input','placeholder'=>'State']).'</div><div class="form-group col-sm-6 col-xs-12">'.Form::label('multi['.$cartkey.'][shipping_country]','Country',array('class'=>'form-control-label')).Form::text('multi['.$cartkey.'][shipping_country]','US',['class'=>'form-control shipping_input','placeholder'=>'Country','readonly']).'</div></div></div>';
				}
				$responce['status'] = 1;
			}
		}
		return json_encode($responce);
	}
	
	public function shipping(Request $request){
		$res['status'] = '';
		$res['ship'] = false;
		if($request->isMethod('post')){
			$states = State::where('status',1)->pluck('stateName','stateCode')->all();
			$data = $request->all();
			if(!array_key_exists('multiple_shipping',$data)){
				$data['multiple_shipping'] = 0;
			}
			$shipping_weight = new CalculateShippingWeight();
			$result = $shipping_weight->weight($data,'admin');
			//pr($result);die;
			
			$multiple = $result['multiple'];
			$weight = $result['total_weight'];
			$product_weight = $result['product_weight'];
			$product_shipping = $result['product_shipping'];
			$product_shipping_amounts = $result['product_shipping_amounts'];
			$session_total = $result['session_total'];
			$address = $result['address'];
			
			session()->put('products_weight',$product_weight);
			
			$tax_apply = false;
			$rate = 0;
			
			// check for shipping price
			if($multiple){
				foreach($product_weight as $k1=>$v1){
					$res['status'] = '';
					if($v1['product_shipping']){
						$shipping = new UPSShipping();
						$validate = $shipping->validateAddress($v1['address']['zipcode'],$v1['address']['stateCode'],$v1['address']['state']);
						if($validate['status']){
							$rate_detail = $shipping->RateCalculate($v1['address']['zipcode'],$v1['weight'],$data['shipping_option']);
							//pr($rate_detail);
							if($rate_detail['status']){
								$price = number_format($rate_detail['res']->MonetaryValue,2,'.','');
											
								$product_shipping_amounts[$k1] = $price;
								$res['status'] = 'success';
								$res['ship'] = true;
							}else{
								$res['msg'] = 'Please enter a valid location.';
							}
						}else{
							$res['msg'] = $validate['msg'];
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
							$res['ship'] = true;
							$product_shipping_amounts[$k1] = $price;
						}
						$res['status'] = 'success';
					}
				}
				$sum = array_sum($product_shipping_amounts);
				$sum =number_format($sum,2,'.','');
				$rate += $sum;
			}else{
				$shipping = new UPSShipping();
				$validate = $shipping->validateAddress($address['zipcode'],$address['stateCode'],$address['state']);
				if($validate['status']){
					if($weight > 0){
						$rate_detail = $shipping->RateCalculate($address['zipcode'],$weight,$data['shipping_option']);
						//pr($rate_detail);//die;
						if($rate_detail['status']){
							$ship_amount = number_format($rate_detail['res']->MonetaryValue,2,'.','');
							
							$one_LBS_ship_amount = $ship_amount/$weight;
							$one_LBS_ship_amount = number_format($one_LBS_ship_amount,2,'.','');
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
								}else{
									if($v1['product_shipping']){
										$price = $one_LBS_ship_amount*$v1['weight'];
										$product_shipping_amounts[$k1] = number_format($price,2,'.','');
									}
								}
							}					
							$sum = array_sum($product_shipping_amounts);
							$sum =number_format($sum,2,'.','');
							$rate += $sum;
							
							$res['status'] = 'success';
							$res['ship'] = true;
						}else{
							$res['msg'] = 'Please enter a valid location.';
						}
					}else{
						$res['status'] = 'success';
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
								$rate += $price;
								$res['ship'] = true;
							}
						}
					}
				}else{
					$res['msg'] = $validate['msg'];
				}
			}
			
			$tax = 0;
			if($multiple){
				// loop for product to check if state is PA
				foreach(session()->get('cart') as $key=>$val){
					if($address[$key]['stateCode'] == 'PA'){
						$tax += ($val['total']*config('constants.sales_tax'))/100;
						$tax = number_format($tax,2,'.','');
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
			
			$rate = number_format($rate,2,'.','');
			//pr($product_shipping_amounts);
			//echo "Rate : ".$rate.' Tax : '.$tax;die;
			
			session()->put('carttotal.shipping', $rate);
			session()->put('carttotal.sales_tax', $tax);
			
			$total = (session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping') + session()->get('carttotal.sales_tax'));
			$total = number_format($total,2,'.','');
			session()->put('carttotal.total', $total);
			
			$res['rate'] = $rate;
			$res['tax'] = $tax;
			$res['total'] = $total;
			
			session()->put('product_shipping_amounts',$product_shipping_amounts);
		}
		//pr($res); 
		return json_encode($res);
	}
	
	public function add_to_cart(Request $request){
		$responce['status'] = 0;
		$responce['res'] = '';
		if($request->isMethod('post')){
			$object = $request->all()['object'];
			parse_str($request->all()['form'], $data);
			//pr($data);
			//pr($object);die;
			
			$products = session()->get('cart');

			$cartkey = count($products);			
			
			if(@$data['cartkey'] != null and $cartkey != ''){
				$cartkey =  $data['cartkey'];
			}

			if($data['product'] >=1)
			{
				$products[$cartkey]['product_id'] = $data['product'];				
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
			}
			else
			{
				$products[$cartkey]['product_id'] = '';				
				$products[$cartkey]['product_name'] =$data['product_name'];				
				$products[$cartkey]['description'] = nl2br($data['description']);				
				$products[$cartkey]['shipping_type'] = 12;				
				$products[$cartkey]['price_default'] =  number_format((float)($data['price']), 2, '.', '');			
				$products[$cartkey]['price'] =  number_format((float)($data['price']), 2, '.', '');
				$products[$cartkey]['quantity'] = $data['quantity'];
				$products[$cartkey]['gross_total'] =  number_format((float)($data['gross_price']), 2, '.', '');
				$products[$cartkey]['qty_discount'] =  number_format((float)(0), 2, '.', '');
				$products[$cartkey]['total'] =  number_format((float)($data['gross_price']), 2, '.', '');
				$products[$cartkey]['sales_tax'] =  0.00;				
				
				$products[$cartkey]['project_name'] = '';
				$products[$cartkey]['comments'] = '';
				
				$products[$cartkey]['options'] = array();
				$products[$cartkey]['object_data'] = '';
			}
			
			
			session()->put('cart', $products);				
			
			$session_total = 0;
			foreach(session()->get('cart') as $k1=>$product){
				$session_total += $product['total'];						
			}			
			
			$session_total = number_format($session_total,2,'.','');
			
			$responce = $this->getCart();
			
			session()->put('carttotal.gross', $session_total);
			session()->put('carttotal.discount', '0');
			session()->put('carttotal.discount_code', '0');
			session()->put('carttotal.shipping', 0);
			session()->put('carttotal.sales_tax', 0);
			$total = (session()->get('carttotal.gross') - session()->get('carttotal.discount') + session()->get('carttotal.shipping'));
			session()->put('carttotal.total', number_format($total,2,'.',''));
		}
		return json_encode($responce);
	}
	
	public function getCart(){
		$responce['res'] = '';
		$responce['status'] = 1;
		$session_total = 0;
		if(session()->has('cart')){
			$i = 1;
			//pr(session()->get('cart'));die();
			foreach(session()->get('cart') as $k1=>$product){
				$session_total += $product['total'];
				$responce['res'] .= '<tr>';
				$product_detail = Products::find($product['product_id']);
				$responce['res'] .= '<td>'.$i.'</td>';
				if($product['product_id'] >= 1)
				{
					$responce['res'] .= '<td>'.$product_detail->name.'</td><td>';
				}
				else
				{
					$responce['res'] .= '<td>'.$product['product_name'].'</td>';
					$responce['res'] .= '<td>'.$product['description'].'</td>';
				}
				$options_list['printing'] = '';
				$options_list['finishing'] = '';
				$options_list['production'] = '';
				$variant_list = '';
				foreach((array)$product['options'] as $key1=>$val){
					$options_list[$key1] .= '<b>'.ucfirst($key1).'</b><ul>';
					foreach($val as $key2=>$val2){
						foreach($val2 as $key3=>$val3){
							$str = explode('__',$val3);
							$options_list[$key1] .= '<li>'.ucwords(str_replace('_',' ',$key3)).'=>'.$str[0].'</li>';
						}
					}
					if($key1 != 'printing'){
						$options_list[$key1] .= '</ul>';
					}
				}
				
				if(array_key_exists('variants',$product)){
					foreach($product['variants'] as $key=>$variant){
						$detail = \App\ProductVariant::where('id',$key)->with('variantValues')->first();
						foreach($detail->variantValues as $value){
							if($value->id == $variant){
								$variant_list .= '<li>'.ucwords($detail->name).'=>'.ucwords($value->value).'</li>';
							}
						}
					}
				}
				
				if($options_list['printing'] != ''){			
					$responce['res'] .= $options_list['printing'].$variant_list.'</ul>';
				}else{
					$responce['res'] .= '<b>Printing</b><ul>'.$variant_list.'</ul>';
				}
				
				if($options_list['finishing'] != ''){
					$responce['res'] .= $options_list['finishing'];
				}
				if($options_list['production'] != ''){	
					$responce['res'] .= $options_list['production'];
				}
				
				$responce['res'] .= '</td><td>$'.$product['price'].'</td>';
				$responce['res'] .= '<td>'.$product['quantity'].'</td>';
				$responce['res'] .= '<td>'.$product['gross_total'].'</td>';
				
				if($product['qty_discount'] > 0 or $product['qty_discount'] > '0'){
					$responce['res'] .= '<td>$'.$product['qty_discount'].'</td>';
				}else{
					$responce['res'] .= '<td>$0</td>';
				}
				
				$responce['res'] .= '<td>$'.$product['total'].'</td>';
				
				$responce['res'] .= '<td><a class="btn remove-product btn-danger" href="javascript:void(0)" data="'.$k1.'"><i class="fa fa-trash"></i></a></td></tr>';
				$i++;
			}
		}
		
		$session_total = number_format($session_total,2,'.','');
		$responce['session_total'] = $session_total;
		
		return $responce;
	}
	
	public function cart_products(Request $request){
		$responce['status'] = 0;
		$responce['res'] = '';
		$responce['res1'] = '';
		$responce['session_total'] = 0;
		if($request->isMethod('post')){
			$products = session()->get('cart');
			$data = $request->all();
			//pr($data);
			if($data['type'] == 'delete'){
				unset($products[$data['key']]);
				session()->put('cart', $products);
				$products = session()->get('cart');
			}
			if($request->session()->has('cart') and !empty($products)){
				$i = 1;
				if($data['type'] == 'delete'){
					foreach(session()->get('cart') as $k1=>$product){
						$responce['res'] .= '<tr>';
						$product_detail = Products::findOrFail($product['product_id']);
						$responce['res'] .= '<td>'.$i.'</td>';
						$responce['res'] .= '<td>'.$product_detail->name.'</td><td>';
						$options_list['printing'] = '';
						$options_list['finishing'] = '';
						$options_list['production'] = '';
						$variant_list = '';
						foreach((array)$product['options'] as $key1=>$val){
							$options_list[$key1] .= '<b>'.ucfirst($key1).'</b><ul>';
							foreach($val as $key2=>$val2){
								foreach($val2 as $key3=>$val3){
									$str = explode('__',$val3);
									$options_list[$key1] .= '<li>'.ucwords(str_replace('_',' ',$key3)).'=>'.$str[0].'</li>';
								}
							}
							if($key1 != 'printing'){
								$options_list[$key1] .= '</ul>';
							}
						}
						
						if(array_key_exists('variants',$product)){
							foreach($product['variants'] as $key=>$variant){
								$detail = \App\ProductVariant::where('id',$key)->with('variantValues')->first();
								//pr($detail);
								foreach($detail->variantValues as $value){
									if($value->id == $variant){
										$variant_list .= '<li>'.ucwords($detail->name).'=>'.ucwords($value->value).'</li>';
									}
								}
							}
						}
						
						if($options_list['printing'] != ''){			
							$responce['res'] .= $options_list['printing'].$variant_list.'</ul>';
						}else{
							$responce['res'] .= '<b>Printing</b><ul>'.$variant_list.'</ul>';
						}
						
						if($options_list['finishing'] != ''){
							$responce['res'] .= $options_list['finishing'];
						}
						if($options_list['production'] != ''){	
							$responce['res'] .= $options_list['production'];
						}
						
						$responce['res'] .= '</td><td>$'.$product['price'].'</td>';
						$responce['res'] .= '<td>'.$product['quantity'].'</td>';
						$responce['res'] .= '<td>'.$product['gross_total'].'</td>';
						
						if($product['qty_discount'] > 0 or $product['qty_discount'] > '0'){
							$responce['res'] .= '<td>$'.$product['qty_discount'].'</td>';
						}else{
							$responce['res'] .= '<td>$0</td>';
						}
						
						$responce['res'] .= '<td>$'.$product['total'].'</td>';
						
						$responce['res'] .= '<td><a class="btn remove-product btn-danger" href="javascript:void(0)" data="'.$k1.'"><i class="fa fa-trash"></i></a></td></tr>';
						$i++;
						$responce['session_total'] += $product['total'];
					}
				}else{
					foreach($products as $k1=>$product){
						$responce['res'] .= '<tr>';
						$product_detail = Products::findOrFail($product['product_id']);
						$responce['res'] .= '<td>'.$i.'</td>';
						$responce['res'] .= '<td>'.$product_detail->name.'</td>';
						$responce['res'] .= '<td>'.$product['quantity'].'</td><td>';
						$options_list['printing'] = '';
						$options_list['finishing'] = '';
						$options_list['production'] = '';
						$variant_list = '';
						foreach($product['options'] as $key1=>$val){
							$options_list[$key1] .= '<b>'.ucfirst($key1).'</b><ul>';
							foreach($val as $key2=>$val2){
								foreach($val2 as $key3=>$val3){
									$str = explode('__',$val3);
									$options_list[$key1] .= '<li>'.ucwords(str_replace('_',' ',$key3)).'=>'.$str[0].'</li>';
								}
							}
							if($key1 != 'printing'){
								$options_list[$key1] .= '</ul>';
							}
						}
						
						if(array_key_exists('variants',$product)){
							foreach($product['variants'] as $key=>$variant){
								$detail = \App\ProductVariant::where('id',$key)->with('variantValues')->first();
								//pr($detail);
								foreach($detail->variantValues as $value){
									if($value->id == $variant){
										$variant_list .= '<li>'.ucwords($detail->name).'=>'.ucwords($value->value).'</li>';
									}
								}
							}
						}
						
						if($options_list['printing'] != ''){			
							$responce['res'] .= $options_list['printing'].$variant_list.'</ul>';
						}else{
							$responce['res'] .= '<b>Printing</b><ul>'.$variant_list.'</ul>';
						}
						
						if($options_list['finishing'] != ''){
							$responce['res'] .= $options_list['finishing'];
						}
						if($options_list['production'] != ''){	
							$responce['res'] .= $options_list['production'];
						}
						
						
						$responce['res'] .= '</td><td>$'.$product['price_default'].'</td>';
						$responce['res'] .= '<td>$'.$product['total'].'</td>';
						$responce['res'] .= '<td><a class="btn remove-product btn-danger" href="javascript:void(0)" data="'.$k1.'"><i class="fa fa-trash"></i></a></td></tr>';
						$i++;
						$responce['session_total'] += $product['total'];
					}
				}
				
				$responce['status'] = 'success';
				$responce['res1'] = $responce['res'];
				$responce['session_total'] = number_format($responce['session_total'],2,'.','');
				
				$responce['res'] .= '<tr><td colspan="3">&nbsp;</td><td colspan="2"><b>Cart Total : </b></td><td colspan="2">$'.$responce['session_total'].'</td></tr>';
				if(session()->has('carttotal.shipping')){
					$responce['res'] .= '<tr><td colspan="3">&nbsp;</td><td colspan="2"><b>Shipping Amount : </b></td><td colspan="2">$'.session()->get('carttotal.shipping').'</td></tr>';
					$total = session()->get('carttotal.total');
					$responce['res'] .= '<tr><td colspan="3">&nbsp;</td><td colspan="2"><b>Total : </b></td><td colspan="2">$'.$total.'</td></tr>';
				}
			}
			else{
				$responce['status'] = 'success';
				$responce['res'] .= '<tr><td colspan="7"><center>Cart is empty</center></td></tr>';
			}
		}
		return json_encode($responce);
	}
	
	public function lists(Request $request,$field='date',$sort='DESC'){
		$pageTitle = "Orders List";	
		
		$data = $request->all();
		if(!$request->isMethod('post') and !isset($data['page'])){
			session()->forget('orders');
		}
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$designers = Designers::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('status', 1)->pluck('name','id')->all();
		
		$vendors = Vendors::select('id', DB::raw("concat(fname, ' ', lname) as name"),'company_name')->where('status', 1)->get();
		$temp = array();
		foreach($vendors as $vendor){
			if($vendor->company_name !=""){
				$temp[$vendor->id] = $vendor->company_name;
			}else{
				$temp[$vendor->id] = $vendor->name;
			}
		}
		$vendors = $temp;
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		
		$extra_admin = '';
		if($field == 'order'){
			$field = 'id';
			$extra_admin = 'order';
		}
		if($field == 'po'){
			$field = 'order_id';
			$extra_admin = 'po';
		}
		if($field == 'customer-status')
			$field = 'customer_status';
		
		if($field == 'artwork-status')
			$field = 'art_work_status';
		
		if($field == 'vendor-status')
			$field = 'vendor_status';
		
		if($field == 'payment-status')
			$field = 'payment_status';
		
		if($field == 'date')
			$field = 'created_at';
		
		if($field == 'due-date')
			$field = 'due_date';
		
		$db = Orders::select('orders.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email as customer_email','user.phone_number as customer_phone_number','user.company_name as customer_company_name',DB::raw("concat(agent.fname, ' ', agent.lname) as agent_name"),DB::raw("concat(designer.fname, ' ', designer.lname) as designer_name"))->with('orderProductsDetails');
		
		$db->leftJoin('users as user', 'orders.user_id', '=', 'user.id');
		$db->leftJoin('users as agent', 'orders.agent_id', '=', 'agent.id');
		$db->leftJoin('designers as designer', 'orders.designer_id', '=', 'designer.id');
		
		/*if(\Auth::user()->role_id == 2){
			$db->where('orders.agent_id',\Auth::user()->id);
		} */
		
		$db->where('orders.status','=',1);
		$db->where('orders.customer_status','>=',1);
		
		if($request->isMethod('post')){
			//pr($data);die;
			if(isset($data['id']) and !empty($data['id'])){
				session(['orders.id' => $data['id']]);
			}else{
				session()->forget('orders.id');
			}
			if(isset($data['agent']) and !empty($data['agent'])){
				session(['orders.agent' => $data['agent']]);
			}else{
				session()->forget('orders.agent');
			}
			if(isset($data['designer']) and !empty($data['designer'])){
				session(['orders.designer' => $data['designer']]);
			}else{
				session()->forget('orders.designer');
			}
			if(isset($data['vendor']) and !empty($data['vendor'])){
				session(['orders.vendor' => $data['vendor']]);
			}else{
				session()->forget('orders.vendor');
			}
			if(isset($data['search']) and !empty($data['search'])){
				session(['orders.search' => $data['search']]);
			}else{
				session()->forget('orders.search');
			}
			if(isset($data['from_date']) and !empty($data['from_date'])){
				session(['orders.from_date' => $data['from_date']]);
			}else{
				session()->forget('orders.from_date');
			}
			if(isset($data['end_date']) and !empty($data['end_date'])){
				session(['orders.end_date' => $data['end_date']]);
			}else{
				session()->forget('orders.end_date');
			}
			if(isset($data['order_type']) and $data['order_type'] != ''){
				session(['orders.order_type' => $data['order_type']]);
			}else{
				session()->forget('orders.order_type');
			}
			if(isset($data['status']) and !empty($data['status'])){
				session(['orders.status' => $data['status']]);
			}else{
				session()->forget('orders.status');
			}
			if(array_key_exists('page_size',$data)){
				session(['orders.page_size' => $data['page_size']]);
			}else{
				session()->forget('orders.page_size');
			}
		}
		
		if (session()->has('orders')) {
			//pr(session()->get('orders'));
			if (session()->has('orders.id')) {
				$id = session()->get('orders.id');
				$db->where('orders.id',$id);
			}
			if (session()->has('orders.agent')) {
				$agent = session()->get('orders.agent');
				$db->where('orders.agent_id',$agent);
			}
			if (session()->has('orders.designer')) {
				$designer = session()->get('orders.designer');
				$db->where('orders.designer_id',$designer);
			}
			if (session()->has('orders.vendor')) {
				$vendor = session()->get('orders.vendor');
				$order_ids = OrderProducts::where('vendor_id',$vendor)->pluck('order_id');
				$db->whereIn('orders.id',$order_ids);
			}
			if (session()->has('orders.search')) {
				$search = session()->get('orders.search');
				$db->where(function ($query) use($search) {					
					$query->orWhere('user.email','like','%'.$search.'%');
					$searchArr = explode(" ", $search);					
					foreach ($searchArr as $key => $value) {
						$query->orWhere('user.fname','like','%'.$value.'%');
						$query->orWhere('user.lname','like','%'.$value.'%');
						$query->orWhere('user.company_name','like','%'.$value.'%');
					}
				});
			}
			if (session()->has('orders.order_type')) {
				$order_type = session()->get('orders.order_type');
				if($order_type == 0){
					$db->where('orders.payment_status','=',$order_type);
				}else{
					$db->where('orders.payment_status','>=',$order_type);
				}
			}
			if (session()->has('orders.status')) {
				$status = session()->get('orders.status');
				$db->where('orders.status',$status);
			}
			
			if (array_key_exists('page_size',session()->get('orders'))){
				$page_size = session()->get('orders.page_size');
				$limit = $page_size;
			}
			
			if (session()->has('orders.from_date') and !session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$db->where('orders.created_at','>=',date('Y-m-d'.' 00:00:00',strtotime($from_date)));
			}
			else if (!session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$end_date = session()->get('orders.end_date');
				$db->where('orders.created_at','<=',date('Y-m-d'.' 23:59:59',strtotime($end_date)));
			}
			else if(session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$end_date = session()->get('orders.end_date');
				$from_date = date('Y-m-d'.' 00:00:00',strtotime($from_date));
				$end_date = date('Y-m-d'.' 23:59:59',strtotime($end_date));
				$db->whereBetween('orders.created_at',array($from_date,$end_date));
			}
		}
		
		if($field == 'name'){
			$db->orderBy('user.fname',$sort);
		}else{
			$db->orderBy('orders.'.$field,$sort);
		}
		//$db->orderBy('orders.created_at','desc');
		
		$orders = $db->paginate($limit);
		//pr(qLog());die;
		//pr($orders);die;
		return view('Admin/orders/lists',compact('pageTitle','limit','orders','data','agents','designers','vendors','field','sort','extra_admin'));
	}

	public function estimates(Request $request,$field='date',$sort='DESC'){
		$pageTitle = "Estimates List";	
		
		$data = $request->all();
		if(!$request->isMethod('post') and !isset($data['page'])){
			session()->forget('orders');
		}
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$designers = Designers::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('status', 1)->pluck('name','id')->all();
		
		$vendors = Vendors::select('id', DB::raw("concat(fname, ' ', lname) as name"),'company_name')->where('status', 1)->get();
		$temp = array();
		foreach($vendors as $vendor){
			if($vendor->company_name !=""){
				$temp[$vendor->id] = $vendor->company_name;
			}else{
				$temp[$vendor->id] = $vendor->name;
			}
		}
		$vendors = $temp;
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		
		$extra_admin = '';
		if($field == 'order'){
			$field = 'id';
			$extra_admin = 'order';
		}
		if($field == 'po'){
			$field = 'order_id';
			$extra_admin = 'po';
		}
		if($field == 'customer-status')
			$field = 'customer_status';
		
		if($field == 'artwork-status')
			$field = 'art_work_status';
		
		if($field == 'vendor-status')
			$field = 'vendor_status';
		
		if($field == 'payment-status')
			$field = 'payment_status';
		
		if($field == 'date')
			$field = 'created_at';
		
		if($field == 'due-date')
			$field = 'due_date';
		
		$db = Orders::select('orders.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email as customer_email','user.phone_number as customer_phone_number','user.company_name as customer_company_name',DB::raw("concat(agent.fname, ' ', agent.lname) as agent_name"),DB::raw("concat(designer.fname, ' ', designer.lname) as designer_name"))->with('orderProductsDetails');
		
		$db->leftJoin('users as user', 'orders.user_id', '=', 'user.id');
		$db->leftJoin('users as agent', 'orders.agent_id', '=', 'agent.id');
		$db->leftJoin('designers as designer', 'orders.designer_id', '=', 'designer.id');
		
		/* if(\Auth::user()->role_id == 2){
			$db->where('orders.agent_id',\Auth::user()->id);
		} */
		
		//$db->where('orders.status','=',1);
		$db->where('orders.customer_status','=',0);
		
		if($request->isMethod('post')){
			//pr($data);die;
			if(isset($data['id']) and !empty($data['id'])){
				session(['orders.id' => $data['id']]);
			}else{
				session()->forget('orders.id');
			}
			if(isset($data['agent']) and !empty($data['agent'])){
				session(['orders.agent' => $data['agent']]);
			}else{
				session()->forget('orders.agent');
			}
			if(isset($data['designer']) and !empty($data['designer'])){
				session(['orders.designer' => $data['designer']]);
			}else{
				session()->forget('orders.designer');
			}
			if(isset($data['vendor']) and !empty($data['vendor'])){
				session(['orders.vendor' => $data['vendor']]);
			}else{
				session()->forget('orders.vendor');
			}
			if(isset($data['search']) and !empty($data['search'])){
				session(['orders.search' => $data['search']]);
			}else{
				session()->forget('orders.search');
			}
			if(isset($data['from_date']) and !empty($data['from_date'])){
				session(['orders.from_date' => $data['from_date']]);
			}else{
				session()->forget('orders.from_date');
			}
			if(isset($data['end_date']) and !empty($data['end_date'])){
				session(['orders.end_date' => $data['end_date']]);
			}else{
				session()->forget('orders.end_date');
			}
			if(isset($data['order_type']) and $data['order_type'] != ''){
				session(['orders.order_type' => $data['order_type']]);
			}else{
				session()->forget('orders.order_type');
			}
			if(isset($data['status']) and !empty($data['status'])){
				session(['orders.status' => $data['status']]);
			}else{
				session()->forget('orders.status');
			}
			if(array_key_exists('page_size',$data)){
				session(['orders.page_size' => $data['page_size']]);
			}else{
				session()->forget('orders.page_size');
			}
		}
		
		if (session()->has('orders')) {
			//pr(session()->get('orders'));
			if (session()->has('orders.id')) {
				$id = session()->get('orders.id');
				$db->where('orders.id',$id);
			}
			if (session()->has('orders.agent')) {
				$agent = session()->get('orders.agent');
				$db->where('orders.agent_id',$agent);
			}
			if (session()->has('orders.designer')) {
				$designer = session()->get('orders.designer');
				$db->where('orders.designer_id',$designer);
			}
			if (session()->has('orders.vendor')) {
				$vendor = session()->get('orders.vendor');
				$order_ids = OrderProducts::where('vendor_id',$vendor)->pluck('order_id');
				$db->whereIn('orders.id',$order_ids);
			}
			if (session()->has('orders.search')) {
				$search = session()->get('orders.search');
				$db->where(function ($query) use($search) {					
					$query->orWhere('user.email','like','%'.$search.'%');
					$searchArr = explode(" ", $search);					
					foreach ($searchArr as $key => $value) {
						$query->orWhere('user.fname','like','%'.$value.'%');
						$query->orWhere('user.lname','like','%'.$value.'%');
					}
				});
			}
			if (session()->has('orders.order_type')) {
				$order_type = session()->get('orders.order_type');
				if($order_type == 0){
					$db->where('orders.payment_status','=',$order_type);
				}else{
					$db->where('orders.payment_status','>=',$order_type);
				}
			}
			if (session()->has('orders.status')) {
				$status = session()->get('orders.status');
				$db->where('orders.status',$status);
			}
			
			if (array_key_exists('page_size',session()->get('orders'))){
				$page_size = session()->get('orders.page_size');
				$limit = $page_size;
			}
			
			if (session()->has('orders.from_date') and !session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$db->where('orders.created_at','>=',date('Y-m-d'.' 00:00:00',strtotime($from_date)));
			}
			else if (!session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$end_date = session()->get('orders.end_date');
				$db->where('orders.created_at','<=',date('Y-m-d'.' 23:59:59',strtotime($end_date)));
			}
			else if(session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$end_date = session()->get('orders.end_date');
				$from_date = date('Y-m-d'.' 00:00:00',strtotime($from_date));
				$end_date = date('Y-m-d'.' 23:59:59',strtotime($end_date));
				$db->whereBetween('orders.created_at',array($from_date,$end_date));
			}
		}
		
		if($field == 'name'){
			$db->orderBy('user.fname',$sort);
		}else{
			$db->orderBy('orders.'.$field,$sort);
		}
		//$db->orderBy('orders.created_at','desc');
		
		$orders = $db->paginate($limit);
		//pr(qLog());die;
		//pr($orders);die;
		return view('Admin/orders/estimates',compact('pageTitle','limit','orders','data','agents','designers','vendors','field','sort','extra_admin'));
	}
	
	public function archived(Request $request,$field='date',$sort='DESC'){
		$pageTitle = "Archived Orders List";	
		
		$data = $request->all();
		if(!$request->isMethod('post') and !isset($data['page'])){
			session()->forget('orders');
		}
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$designers = Designers::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('status', 1)->pluck('name','id')->all();
		
		$vendors = Vendors::select('id', DB::raw("concat(fname, ' ', lname) as name"),'company_name')->where('status', 1)->get();
		$temp = array();
		foreach($vendors as $vendor){
			if(empty($vendor->name)){
				$temp[$vendor->id] = $vendor->company_name;
			}else{
				$temp[$vendor->id] = $vendor->name;
			}
		}
		$vendors = $temp;
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		
		$extra_admin = '';
		if($field == 'order'){
			$field = 'id';
			$extra_admin = 'order';
		}
		if($field == 'po'){
			$field = 'order_id';
			$extra_admin = 'po';
		}
		if($field == 'customer-status')
			$field = 'customer_status';
		
		if($field == 'artwork-status')
			$field = 'art_work_status';
		
		if($field == 'vendor-status')
			$field = 'vendor_status';
		
		if($field == 'payment-status')
			$field = 'payment_status';
		
		if($field == 'date')
			$field = 'created_at';
		
		if($field == 'due-date')
			$field = 'due_date';
		
		$db = Orders::select('orders.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email as customer_email','user.phone_number as customer_phone_number','user.company_name as customer_company_name',DB::raw("concat(agent.fname, ' ', agent.lname) as agent_name"),DB::raw("concat(designer.fname, ' ', designer.lname) as designer_name"))->with('orderProductsDetails');
		
		$db->leftJoin('users as user', 'orders.user_id', '=', 'user.id');
		$db->leftJoin('users as agent', 'orders.agent_id', '=', 'agent.id');
		$db->leftJoin('designers as designer', 'orders.designer_id', '=', 'designer.id');
		
		if(\Auth::user()->role_id == 2){
			$db->where('orders.agent_id',\Auth::user()->id);
		}
		
		$db->where('orders.status','=',2);
		
		if($request->isMethod('post')){
			if(isset($data['id']) and !empty($data['id'])){
				session(['orders.id' => $data['id']]);
			}else{
				session()->forget('orders.id');
			}
			if(isset($data['agent']) and !empty($data['agent'])){
				session(['orders.agent' => $data['agent']]);
			}else{
				session()->forget('orders.agent');
			}
			if(isset($data['designer']) and !empty($data['designer'])){
				session(['orders.designer' => $data['designer']]);
			}else{
				session()->forget('orders.designer');
			}
			if(isset($data['vendor']) and !empty($data['vendor'])){
				session(['orders.vendor' => $data['vendor']]);
			}else{
				session()->forget('orders.vendor');
			}
			if(isset($data['search']) and !empty($data['search'])){
				session(['orders.search' => $data['search']]);
			}else{
				session()->forget('orders.search');
			}
			if(isset($data['from_date']) and !empty($data['from_date'])){
				session(['orders.from_date' => $data['from_date']]);
			}else{
				session()->forget('orders.from_date');
			}
			if(isset($data['end_date']) and !empty($data['end_date'])){
				session(['orders.end_date' => $data['end_date']]);
			}else{
				session()->forget('orders.end_date');
			}
			if(isset($data['order_type']) and $data['order_type'] != ''){
				session(['orders.order_type' => $data['order_type']]);
			}else{
				session()->forget('orders.order_type');
			}
			if(isset($data['status']) and !empty($data['status'])){
				session(['orders.status' => $data['status']]);
			}else{
				session()->forget('orders.status');
			}
		}
		
		if (session()->has('orders')) {
			if (session()->has('orders.id')) {
				$id = session()->get('orders.id');
				$db->where('orders.id',$id);
			}
			if (session()->has('orders.agent')) {
				$agent = session()->get('orders.agent');
				$db->where('orders.agent_id',$agent);
			}
			if (session()->has('orders.designer')) {
				$designer = session()->get('orders.designer');
				$db->where('orders.designer_id',$designer);
			}
			if (session()->has('orders.vendor')) {
				$vendor = session()->get('orders.vendor');
				$order_ids = OrderProducts::where('vendor_id',$vendor)->pluck('order_id');
				$db->whereIn('orders.id',$order_ids);
			}
			if (session()->has('orders.search')) {
				$search = session()->get('orders.search');
				$db->where(function ($query) use($search) {
					$query->orWhere('user.fname','like','%'.$search.'%');
					$query->orWhere('user.lname','like','%'.$search.'%');
					$query->orWhere('user.email','like','%'.$search.'%');
				});
			}
			if (session()->has('orders.order_type')) {
				$order_type = session()->get('orders.order_type');
				$db->where('orders.payment_status',$order_type);
			}
			if (session()->has('orders.status')) {
				$status = session()->get('orders.status');
				$db->where('orders.status',$status);
			}
			
			if (session()->has('orders.from_date') and !session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$db->where('orders.created_at','>=',date('Y-m-d'.' 00:00:00',strtotime($from_date)));
			}
			else if (!session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$end_date = session()->get('orders.end_date');
				$db->where('orders.created_at','<=',date('Y-m-d'.' 23:59:59',strtotime($end_date)));
			}
			else if(session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$end_date = session()->get('orders.end_date');
				$from_date = date('Y-m-d'.' 00:00:00',strtotime($from_date));
				$end_date = date('Y-m-d'.' 23:59:59',strtotime($end_date));
				$db->whereBetween('orders.created_at',array($from_date,$end_date));
			}
		}
		
		if($field == 'name'){
			$db->orderBy('user.fname',$sort);
		}else{
			$db->orderBy('orders.'.$field,$sort);
		}
		$orders = $db->paginate($limit);
		return view('Admin/orders/archived',compact('pageTitle','limit','orders','data','agents','designers','vendors','field','sort','extra_admin'));
	}
	
	public function lists_products_wise(Request $request,$field='date',$sort='DESC'){
		$pageTitle = "Orders List";	
		
		$data = $request->all();
		if(!$request->isMethod('post') and !isset($data['page'])){
			session()->forget('orders');
		}
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$designers = Designers::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('status', 1)->pluck('name','id')->all();
		
		$vendors = Vendors::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('status', 1)->pluck('name','id')->all();
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		
		$extra_admin = '';
		if($field == 'order'){
			$field = 'order_id';
			$extra_admin = 'order';
		}
		if($field == 'po'){
			$field = 'order_id';
			$extra_admin = 'po';
		}
		if($field == 'customer-status')
			$field = 'customer_status';
		
		if($field == 'artwork-status')
			$field = 'art_work_status';
		
		if($field == 'vendor-status')
			$field = 'vendor_status';
		
		if($field == 'payment-status')
			$field = 'payment_status';
		
		if($field == 'date')
			$field = 'created_at';
		
		if($field == 'due-date')
			$field = 'due_date';
		
		$db = OrderProducts::select('order_products.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email as customer_email','user.phone_number as customer_phone_number','user.company_name as customer_company_name',DB::raw("concat(agent.fname, ' ', agent.lname) as agent_name"),DB::raw("concat(designer.fname, ' ', designer.lname) as designer_name"),DB::raw("concat(vendor.fname, ' ', vendor.lname) as vendor_name"))->with('order','product','notes');
		
		$db->leftJoin('orders as order', 'order_products.order_id', '=', 'order.id');
		$db->leftJoin('notes', 'order_products.item_id', '=', 'notes.order_id');
		$db->leftJoin('users as user', 'order.user_id', '=', 'user.id');
		$db->leftJoin('users as agent', 'order.agent_id', '=', 'agent.id');
		$db->leftJoin('designers as designer', 'order_products.designer_id', '=', 'designer.id');
		$db->leftJoin('vendors as vendor', 'order_products.vendor_id', '=', 'vendor.id');
		
		if(\Auth::user()->role_id == 2){
			$db->where('order.agent_id',\Auth::user()->id);
		}
		if($request->isMethod('post')){
			//pr($data);die;
			if(isset($data['id']) and !empty($data['id'])){
				session(['orders.id' => $data['id']]);
			}else{
				session()->forget('orders.id');
			}
			if(isset($data['agent']) and !empty($data['agent'])){
				session(['orders.agent' => $data['agent']]);
			}else{
				session()->forget('orders.agent');
			}
			if(isset($data['designer']) and !empty($data['designer'])){
				session(['orders.designer' => $data['designer']]);
			}else{
				session()->forget('orders.designer');
			}
			if(isset($data['vendor']) and !empty($data['vendor'])){
				session(['orders.vendor' => $data['vendor']]);
			}else{
				session()->forget('orders.vendor');
			}
			if(isset($data['search']) and !empty($data['search'])){
				session(['orders.search' => $data['search']]);
			}else{
				session()->forget('orders.search');
			}
			if(isset($data['from_date']) and !empty($data['from_date'])){
				session(['orders.from_date' => $data['from_date']]);
			}else{
				session()->forget('orders.from_date');
			}
			if(isset($data['end_date']) and !empty($data['end_date'])){
				session(['orders.end_date' => $data['end_date']]);
			}else{
				session()->forget('orders.end_date');
			}
			if(isset($data['status']) and !empty($data['status'])){
				session(['orders.status' => $data['status']]);
			}else{
				session()->forget('orders.status');
			}
		}
		
		if (session()->has('orders')) {
			if (session()->has('orders.id')) {
				$id = session()->get('orders.id');
				$db->where('order.id',$id);
			}
			if (session()->has('orders.agent')) {
				$agent = session()->get('orders.agent');
				$db->where('order.agent_id',$agent);
			}
			if (session()->has('orders.designer')) {
				$designer = session()->get('orders.designer');
				$db->where('order_products.designer_id',$designer);
			}
			if (session()->has('orders.vendor')) {
				$vendor = session()->get('orders.vendor');
				$db->where('order_products.vendor_id',$vendor);
			}
			if (session()->has('orders.search')) {
				$search = session()->get('orders.search');
				$db->where(function ($query) use($search) {
					$query->orWhere('user.fname','like','%'.$search.'%');
					$query->orWhere('user.lname','like','%'.$search.'%');
					$query->orWhere('user.email','like','%'.$search.'%');
				});
			}
			if (session()->has('orders.status')) {
				$status = session()->get('orders.status');
				$db->where('order_products.status',$status);
			}
			
			if (session()->has('orders.from_date') and !session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$db->where('order_products.created_at','>=',date('Y-m-d'.' 00:00:00',strtotime($from_date)));
			}
			else if (!session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$end_date = session()->get('orders.end_date');
				$db->where('order_products.created_at','<=',date('Y-m-d'.' 23:59:59',strtotime($end_date)));
			}
			else if(session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$end_date = session()->get('orders.end_date');
				$from_date = date('Y-m-d'.' 00:00:00',strtotime($from_date));
				$end_date = date('Y-m-d'.' 23:59:59',strtotime($end_date));
				$db->whereBetween('order_products.created_at',array($from_date,$end_date));
			}
		}else{
			$db->where('order_products.status',1);
		}
		
		if($field == 'name'){
			$db->orderBy('user.fname',$sort);
		}else{
			$db->orderBy('order_products.'.$field,$sort);
		}
		//$db->orderBy('order_products.created_at','desc');
		
		$orders = $db->paginate($limit);
		//pr(qLog());
		//pr($orders);die;
		return view('Admin/orders/lists',compact('pageTitle','limit','orders','data','agents','designers','vendors','field','sort','extra_admin'));
	}
	
	public function archived_old(Request $request,$field='date',$sort='DESC'){
		$pageTitle = "Orders List";	
		
		$data = $request->all();
		if(!$request->isMethod('post') and !isset($data['page'])){
			session()->forget('orders');
		}
		
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$designers = Designers::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('status', 1)->pluck('name','id')->all();
		
		$vendors = Vendors::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('status', 1)->pluck('name','id')->all();
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		
		$extra_admin = '';
		if($field == 'order'){
			$field = 'order_id';
			$extra_admin = 'order';
		}
		if($field == 'po'){
			$field = 'order_id';
			$extra_admin = 'po';
		}
		if($field == 'customer-status')
			$field = 'customer_status';
		
		if($field == 'artwork-status')
			$field = 'art_work_status';
		
		if($field == 'vendor-status')
			$field = 'vendor_status';
		
		if($field == 'payment-status')
			$field = 'payment_status';
		
		if($field == 'date')
			$field = 'created_at';
		
		if($field == 'due-date')
			$field = 'due_date';
		
		$db = OrderProducts::select('order_products.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email as customer_email','user.phone_number as customer_phone_number','user.company_name as customer_company_name',DB::raw("concat(agent.fname, ' ', agent.lname) as agent_name"),DB::raw("concat(designer.fname, ' ', designer.lname) as designer_name"),DB::raw("concat(vendor.fname, ' ', vendor.lname) as vendor_name"))->with('order','product','notes');
		
		$db->leftJoin('orders as order', 'order_products.order_id', '=', 'order.id');
		$db->leftJoin('notes', 'order_products.item_id', '=', 'notes.order_id');
		$db->leftJoin('users as user', 'order.user_id', '=', 'user.id');
		$db->leftJoin('users as agent', 'order.agent_id', '=', 'agent.id');
		$db->leftJoin('designers as designer', 'order_products.designer_id', '=', 'designer.id');
		$db->leftJoin('vendors as vendor', 'order_products.vendor_id', '=', 'vendor.id');
		
		if(\Auth::user()->role_id == 2){
			$db->where('order.agent_id',\Auth::user()->id);
		}
		if($request->isMethod('post')){
			//pr($data);die;
			if(isset($data['id']) and !empty($data['id'])){
				session(['orders.id' => $data['id']]);
			}else{
				session()->forget('orders.id');
			}
			if(isset($data['agent']) and !empty($data['agent'])){
				session(['orders.agent' => $data['agent']]);
			}else{
				session()->forget('orders.agent');
			}
			if(isset($data['designer']) and !empty($data['designer'])){
				session(['orders.designer' => $data['designer']]);
			}else{
				session()->forget('orders.designer');
			}
			if(isset($data['vendor']) and !empty($data['vendor'])){
				session(['orders.vendor' => $data['vendor']]);
			}else{
				session()->forget('orders.vendor');
			}
			if(isset($data['search']) and !empty($data['search'])){
				session(['orders.search' => $data['search']]);
			}else{
				session()->forget('orders.search');
			}
			if(isset($data['from_date']) and !empty($data['from_date'])){
				session(['orders.from_date' => $data['from_date']]);
			}else{
				session()->forget('orders.from_date');
			}
			if(isset($data['end_date']) and !empty($data['end_date'])){
				session(['orders.end_date' => $data['end_date']]);
			}else{
				session()->forget('orders.end_date');
			}
			if(isset($data['status']) and !empty($data['status'])){
				session(['orders.status' => $data['status']]);
			}else{
				session()->forget('orders.status');
			}
		}
		
		if (session()->has('orders')) {
			if (session()->has('orders.id')) {
				$id = session()->get('orders.id');
				$db->where('order.id',$id);
			}
			if (session()->has('orders.agent')) {
				$agent = session()->get('orders.agent');
				$db->where('order.agent_id',$agent);
			}
			if (session()->has('orders.designer')) {
				$designer = session()->get('orders.designer');
				$db->where('order_products.designer_id',$designer);
			}
			if (session()->has('orders.vendor')) {
				$vendor = session()->get('orders.vendor');
				$db->where('order_products.vendor_id',$vendor);
			}
			if (session()->has('orders.search')) {
				$search = session()->get('orders.search');
				$db->where(function ($query) use($search) {
					$query->orWhere('user.fname','like','%'.$search.'%');
					$query->orWhere('user.lname','like','%'.$search.'%');
					$query->orWhere('user.email','like','%'.$search.'%');
				});
			}
			if (session()->has('orders.status')) {
				$status = session()->get('orders.status');
				$db->where('order_products.status',$status);
			}
			
			if (session()->has('orders.from_date') and !session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$db->where('order_products.created_at','>=',date('Y-m-d'.' 00:00:00',strtotime($from_date)));
			}
			else if (!session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$end_date = session()->get('orders.end_date');
				$db->where('order_products.created_at','<=',date('Y-m-d'.' 23:59:59',strtotime($end_date)));
			}
			else if(session()->has('orders.from_date') and session()->has('orders.end_date')) {
				$from_date = session()->get('orders.from_date');
				$end_date = session()->get('orders.end_date');
				$from_date = date('Y-m-d'.' 00:00:00',strtotime($from_date));
				$end_date = date('Y-m-d'.' 23:59:59',strtotime($end_date));
				$db->whereBetween('order_products.created_at',array($from_date,$end_date));
			}
		}else{
			$db->where('order_products.status',2);
		}
		
		if($field == 'name'){
			$db->orderBy('user.fname',$sort);
		}else{
			$db->orderBy('order_products.'.$field,$sort);
		}
		//$db->orderBy('order_products.created_at','desc');
		
		$orders = $db->paginate($limit);
		//pr(qLog());
		//pr($orders);die;
		return view('Admin/orders/archived',compact('pageTitle','limit','orders','data','agents','designers','vendors','field','sort','extra_admin'));
	}
	
	public function view($id){
		$pageTitle = "Order View";
		$db = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress']);
		$order = $db->first();
		
		return view('Admin/orders/view',compact('pageTitle','order'));
	}
	
	public function assign_agent(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			$res = Orders::where('id', $data['order_id'])->update(['agent_id'=>$data['agent_id']]);
			$responce['status'] = 'success';
			$responce['html'] = '<b>Agent</b><br/><button class="btn btn-xs btn-primary agent_btn" data="'.$data['agent_id'].'" order-id="'.$data['order_id'].'" data-toggle="modal" data-target="#assign_agent_model" title="Assign Agent">'.$data['agent_name'].'</button>';
			
			$agent = User::findOrFail($data['agent_id']);
			
			$detail = $this->get_mail_detail($data['order_id'],0,'agent');
			
			$params = array('slug'=>'order_assign_to_agent',
							'to'=>$agent->email,
							'params'=>array(
										'{{name}}'=>$data['agent_name'],
										'{{order_id}}'=>$data['order_id'],
										'{{customer_name}}'=>$detail['customer_name'],
										'{{product_name}}'=>$detail['product_name'],
										'{{quantity}}'=>$detail['product_quantity'],
										'{{order_options}}'=>$detail['options'],
										'{{sub_total}}'=>'$'.$detail['sub_total'],
										'{{total}}'=>'$'.$detail['total'],
										'{{URL}}'=>url('admin/order/lists'),
										'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
										'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
										'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
										));
			parent::sendMail($params);
		}
		return json_encode($responce);
	}
	
	public function assign_designer(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			Orders::where('id', $data['order_id'])->update(['designer_id'=>$data['designer_id']]);
			
			OrderProducts::where('order_id', $data['order_id'])->update(['designer_id'=>$data['designer_id']]);
			
			$responce['status'] = 'success';
			$responce['html'] = '<b>Designer</b><br/><button class="btn btn-xs btn-primary agent_btn" data="'.$data['designer_id'].'" order-id="'.$data['order_id'].'" data-toggle="modal" data-target="#assign_designer_model" title="Assign Designer">'.$data['designer_name'].'</button>';
			
			$designer = Designers::findOrFail($data['designer_id']);
			
			$detail = $this->get_mail_detail($data['order_id'],0,'designer');
			
			$params = array('slug'=>'order_assign_to_designer',
							'to'=>$designer->email,
							'params'=>array(
										'{{name}}'=>$data['designer_name'],
										'{{order_id}}'=>$data['order_id'],
										'{{product_name}}'=>$detail['product_name'],
										'{{quantity}}'=>$detail['product_quantity'],
										'{{order_options}}'=>$detail['options'],
										'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
										'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
										'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
										));
			parent::sendMail($params);
		}
		return json_encode($responce);
	}
	
	public function get_mail_detail($order_id,$order_product_id,$str){
		
		$order=Orders::where('id', $order_id)->with(['customer','orderProduct','orderProductOptions'])->first();
		//pr($order);die;
		$data['sub_total'] = $order->sub_total;
		$data['total'] = $order->total;
		$data['customer_name'] = $order->customer->fname.' '.$order->customer->lname;
		
		$data['options'] = '<ol>';
		$order_options['printing'] = '';
		$order_options['finishing'] = '';
		$order_options['production'] = '';
		foreach($order->orderProductOptions as $val){
			if($str != 'agent' and $val->order_product_id == $order_product_id){	
				$order_options[$val->custom_option_field_group] .= '<li>'.$val->custom_option_name.'<ul><li>Value:'.$val->value.'</li>';
				
				/* if($str != 'vendor')
					$order_options[$val->custom_option_field_group] .= '<li>Price: $'.$val->price.'</li>'; */
				
				$order_options[$val->custom_option_field_group] .= '</ul></li>';
			}
		}
		if($order_options['printing'] !== ''){
			$data['options'] .= '<li>Printing <ul>'.$order_options['printing'].'</ul></li>';
		}
		if($order_options['finishing'] !== ''){
			$data['options'] .= '<li>Finishing <ul>'.$order_options['finishing'].'</ul></li>';
		}
		if($order_options['production'] !== '' and $str != 'vendor'){
			$data['options'] .= '<li>Production <ul>'.$order_options['production'].'</ul></li>';
		}
		$data['options'] .= '</ol>';
		
		$data['product_name'] = '';
		$data['product_quantity'] = '';
		foreach($order->orderProduct as $val){
			$data['product_name'] = $val->product->name;
			$data['product_quantity'] = $val->qty;
		}
		
		return $data;
	}
	
	public function assign_vendor(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		$responce['po_btn'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;

			$order_product = OrderProducts::where('id', $data['order_product_id'])->first();
			if($order_product->po_id != "")
			{
				$po_all_product = OrderProducts::where('po_id', $order_product->po_id)->count();
				if($po_all_product > 1)
				{
					$i= 0;		
					$o_products = OrderProducts::select('id','po_id','vendor_id','product_id')->where('order_id',$order_product->order_id)->where('po_id','!=','')->get();		
					if(count($o_products)>=1)
					{
						foreach($o_products as $product)
						{
							$vendor_po_ids[$product->vendor_id] = $product->po_id;
							$p_id_arr = explode('-', $product->po_id);
							if($p_id_arr[2] > $i )
							{
								$i = $p_id_arr[2] + 1;
							}
						}
					}
					else
					{
						$i= 1;
					}	
					$new_po_id = 'PO-'.$order_product->order_id.'-'.$i;

					$old_order_po = OrderPO::where('po_id', $order_product->po_id)->first();
					
					$order_po = new OrderPo();
					$order_po->po_id = $new_po_id;
					$order_po->order_id = $old_order_po->order_id;
					$order_po->agent_id = $old_order_po->agent_id;
					$order_po->vendor_id = $data['vendor_id'];
					$order_po->terms = $old_order_po->terms;
					$order_po->new_terms = $old_order_po->new_terms;
					$order_po->subtotal = $old_order_po->subtotal;
					$order_po->shipping = $old_order_po->shipping;
					$order_po->total = $old_order_po->total;;
					$order_po->status = $old_order_po->status;;
					$order_po->save();

					OrderProducts::where('id', $order_product->id)->update(['po_id'=>$new_po_id]);
					OrderPoDetails::where('order_product_id', $order_product->id)->update(['po_id'=>$new_po_id]);
				}
				else
				{
					OrderPo::where('order_id', $data['order_id'])->where('po_id',$order_product->po_id)->update(['vendor_id'=>$data['vendor_id']]);
				}
				
			}

			//die();
			/*if($order_product->po_id != "")
			{
				OrderPoDetails::where('order_id', $data['order_id'])->where('order_product_id', $data['order_product_id'])->delete();				
				OrderPOAddress::where('order_id', $data['order_id'])->where('order_product_id', $data['order_product_id'])->delete();
				OrderPoOptions::where('order_id', $data['order_id'])->where('order_product_id', $data['order_product_id'])->delete();

				$order_po_detail = OrderPoDetails::where('order_id', $data['order_id'])->where('po_id', $order_product->po_id)->count();
				if($order_po_detail <= 0)
				{
					OrderPo::where('order_id', $data['order_id'])->where('po_id',$order_product->po_id)->delete();
				}
			}*/
			
			$res = OrderProducts::where('id', $data['order_product_id'])->update(['vendor_id'=>$data['vendor_id']]);
			
			$responce['status'] = 'success';
			
			$order_products = OrderProducts::where('order_id',$data['order_id'])->get();
			
			$i = 1;
			$po_str = array();

			foreach($order_products as $product){
				if($product->vendor_id == ''){
					$i = 0;
				}
				if($product->po_id != ''){
					$po_str[$product->id] = '<a href="'.url('/admin/order/po/'.$product->po_id).'" class="btn btn-xs bg-purple margin">'.$product->po_id.'</a>';
				}
				else
				{
					$po_str[$product->id] = '<a href="'.url('/admin/order/po/create/'.$product->order_id.'/'.$product->product_id).'" class="btn btn-xs bg-purple margin">Create PO</a>';
				}
			}
			
			if($i == 1){
				orders::where('id',$data['order_id'])->update(['po_status'=>1]);
				$responce['po_btn'] = $po_str;
			}			
			
			$responce['html'] = '<b>Vendor</b><br/><button class="btn btn-xs btn-primary vendor_btn" data="'.$data['vendor_id'].'" order-id="'.$data['order_id'].'" order-product-id="'.$data['order_product_id'].'" order-product="'.$data['order_product'].'" data-toggle="modal" data-target="#assign_vendor_model" title="Assign Vendor">'.$data['vendor_name'].'</button>';
			
			/*$vendor = Vendors::findOrFail($data['vendor_id']);
			
			$detail = $this->get_mail_detail($data['order_id'],$data['order_product_id'],'vendor');
			
			if(!empty($vendor->email)){
				$params = array('slug'=>'order_assign_to_vendor',
								'to'=>$vendor->email,
								'params'=>array(
											'{{name}}'=>$data['vendor_name'],
											'{{order_id}}'=>$data['order_id'],
											'{{product_name}}'=>$detail['product_name'],
											'{{quantity}}'=>$detail['product_quantity'],
											'{{order_options}}'=>$detail['options'],
											'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
											'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
											'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
											));
				parent::sendMail($params);
			}*/
		}
		return json_encode($responce);
	}
	
	public function notes(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			if($data['type'] == 'list'){
				$list = Notes::where('user_id',$data['user_id'])->where('order_id',$data['order_id'])->get();
				$responce['status'] = 'success';
				if(!empty($list->toArray())){
					$responce['html'] = '<div class="box box-solid"><div class="box-body">';
					foreach($list as $val){
						$responce['html'] .= '<li class="note_li_'.$val->id.'">'.$val->note.'<span class="btn btn-xs btn-danger pull-right delete_note" data-id="'.$val->id.'" data-item-id="'.$val->order_id.'"><i class="fa fa-trash"></i></span></li>';
					}
					$responce['html'] .= '</div></div>';
				}
			}
			if($data['type'] == 'add'){
				parse_str($data['data'], $data);
				$note = new Notes();
				$note->user_id = $data['note_user_id'];
				$note->order_id = $data['note_order_id'];
				$note->note = $data['note'];
				$note->save();
				
				$responce['status'] = 'success';
				$responce['html'] = '<li class="note_li_'.$note->id.'">'.htmlentities($data['note']).'<span class="btn btn-xs btn-danger pull-right delete_note" data-id="'.$note->id.'" data-item-id="'.$note->order_id.'"><i class="fa fa-trash"></i></span></li>';
			}
		}
		return json_encode($responce);
	}
	
	public function events(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			
			if($data['type'] == 'list'){
				$list = Events::where('user_id',$data['user_id'])->where('order_id',$data['order_id'])->get();
				
				$order_id = explode('-',$data['order_id']);
				
				$responce['status'] = 'success';
				if(!empty($list->toArray())){
					$responce['html'] = '<div class="box box-solid"><div class="box-body">';
					foreach($list as $val){
						$user_name = User::select('email', DB::raw("concat(fname, ' ', lname) as name"))->where('id',$val->user_id)->first();
						
						$responce['html'] .= '<li class="event_li_'.$val->id.'"><i class="fa fa-calendar-plus-o"></i> '.date('M-d-Y',strtotime($val->date)).'<button type="button" class="btn btn-xs btn-danger pull-right event_delete" data-id="'.$val->id.'" data-item-id="'.$val->order_id.'"><i class="fa fa-trash"></i></button><br/><b><a href="'.url("admin/order/edit/".$order_id[0]).'">#'.$order_id[0].'</b></a><br/><b>Created By : </b>'.$user_name->name.'<br/><b>'.$val->title.'</b><br/>'.$val->message.'</li>';
					}
					$responce['html'] .= '</div></div>';
				}
			}
			if($data['type'] == 'add'){
				parse_str($data['data'], $data);
				
				$event = new Events();
				$event->user_id = $data['event_user_id'];
				$event->order_id = $data['event_order_id'];
				$event->date = $data['date'];
				$event->title = $data['title'];
				$event->message = $data['message'];
				$event->save();
				
				$responce['status'] = 'success';
				
				$user_detail = User::select('email', DB::raw("concat(fname, ' ', lname) as name"))->where('id',$data['event_user_id'])->first();
				
				$order_id = explode('-',$event->order_id);
				
				$responce['html'] = '<li class="event_li_'.$event->id.'"><i class="fa fa-calendar-plus-o"></i> '.date('d F Y',strtotime($data['date'])).'<button type="button" class="btn btn-xs btn-danger pull-right event_delete" data-id="'.$event->id.'" data-item-id="'.$event->order_id.'"><i class="fa fa-trash"></i></button><br/><b><a href="'.url("admin/order/edit/".$order_id[0]).'">#'.$order_id[0].'</b></a><br/><b>Created By : </b>'.$user_detail->name.'<br/><b>'.htmlentities($data['title']).'</b><br/>'.$data['message'].'</li>';
				
				$params = array('slug'=>'new_event_mail',
								'to'=>$user_detail->email,
								'params'=>array(
											'{{name}}'=>$user_detail->name,
											'{{order_id}}'=>$data['event_order_id'],
											'{{event_date}}'=>date('d F Y',strtotime($data['date'])),
											'{{event_name}}'=>$data['title'],
											'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
											));
				parent::sendMail($params);
			}
		}
		return json_encode($responce);
	}
	
	public function messages(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			
			if($data['type'] == 'list'){
				$list = Messages::where('order_id',$data['order_id'])->with('user_detail')->limit(15)->get();
				//pr($list);die;
				$responce['status'] = 'success';
				if(!empty($list->toArray())){
					foreach($list as $val){
						$class = 'pull-left';
						if($val->user_detail->id == \Auth::user()->id){
							$class = 'pull-right right';
						}
						$responce['html'] .= '<div class="direct-chat-msg '.$class.'"><div class="direct-chat-info clearfix"><span class="direct-chat-name pull-left">'.$val->user_detail->fname.' '.$val->user_detail->lname.'</span><span class="direct-chat-timestamp pull-right">'.date('d F H:i A',strtotime($val->date)).'</span></div><div class="direct-chat-text">'.htmlentities($val->message).'</div></div>';
					}
				}
			}
			if($data['type'] == 'add'){
				$message = new Messages();
				$message->user_id = \Auth::user()->id;
				$message->order_id = $data['order_id'];
				$message->date = date('Y-m-d H:i:s');
				$message->message = $data['message'];
				$message->save();
				
				$responce['status'] = 'success';
				$responce['html'] .= '<div class="direct-chat-msg pull-right right"><div class="direct-chat-info clearfix"><span class="direct-chat-name pull-left">'.\Auth::user()->fname.' '.\Auth::user()->lname.'</span><span class="direct-chat-timestamp pull-right">'.date('d F H:i A',strtotime($message->date)).'</span></div><div class="direct-chat-text">'.htmlentities($message->message).'</div></div>';
				
				$user_detail = Orders::select('user.email', DB::raw("concat(user.fname, ' ', user.lname) as name"))->where('orders.id',$data['order_id'])->leftJoin('users as user','orders.agent_id','=','user.id')->first();
				
				if(\Auth::user()->role_id == 1){
					$email = $user_detail->email;
					$name = $user_detail->name;
				}
				else{
					$email = \Auth::user()->email;
					$name = \Auth::user()->fname.' '.\Auth::user()->lname;
				}
				
				$params = array('slug'=>'new_chat_message',
								'to'=>$email,
								'params'=>array(
											'{{name}}'=>$name,
											'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
											));
				parent::sendMail($params);
			}
		}
		return json_encode($responce);
	}
	
	public function status(Request $request){
		$responce['status'] = false;
		$responce['status_key'] = '';
		$responce['status_value'] = '';
		$responce['art_work_date'] = '';
		
		if($request->isMethod('post')){
			$data = $request->all();
			$customer_name = '';
			$email = '';
			$str = '';
			$type = $data['type'];
			if($data['type'] == 'customer'){
				parse_str($data['data'], $data);
				//pr($data);die;
				$order_id = $data['customer_order_id'];
				
				$res = Orders::where('id', $data['customer_order_id'])->update(['customer_status'=>$data['select_customer_status'],'created_at'=>date('Y-m-d H:i:s')]);
				
				$res = OrderProducts::where('order_id', $data['customer_order_id'])->update(['customer_status'=>$data['select_customer_status']]);
				
				$detail_products = OrderProducts::select('order_products.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email')->where('order_id', $data['customer_order_id'])->with('product')->leftJoin('orders as order', 'order_products.order_id', '=', 'order.id')->leftJoin('users as user', 'order.user_id', '=', 'user.id')->get();
				
				$str .= '<table border="1">';
				$str .= '<tr><th>Sr.No</th><th>Quantity</th><th>Product Name</th></tr>';
				$i = 1;
				foreach($detail_products as $product){
					$customer_name = $product->customer_name;
					$email = $product->email;
					$str .= '<tr>';
					$str .= '<td>'.$i.'</td>';
					$str .= '<td>'.$product->qty.'</td>';
					$str .= '<td>'.$product->product->name.'</td>';
					$str .= '</tr>';
					$i++;
				}
				$str .= '</table>';

				if($data['select_customer_status'] == 5) // mail should not go if status is completed
				{
					$email ="";
				}
				
				$responce['status'] = 'success';
				$responce['status_key'] = $data['select_customer_status'];
				$responce['status_value'] = config('constants.customer_status.'.$data['select_customer_status']);
				$responce['status_color'] = config('constants.customer_status_color.'.$data['select_customer_status']);
			}
			else if($data['type'] == 'payment'){
				parse_str($data['data'], $data);
				$res = Orders::where('id', $data['payment_order_id'])->update(['payment_status'=>$data['select_payment_status']]);
				
				$res = OrderProducts::where('order_id', $data['payment_order_id'])->where('item_id', $data['payment_product_item_id'])->update(['payment_status'=>$data['select_payment_status']]);
				$responce['status'] = 'success';
				$responce['status_key'] = $data['select_payment_status'];
				$responce['status_value'] = config('constants.payment_status.'.$data['select_payment_status']);
				$responce['status_color'] = config('constants.payment_status_color.'.$data['select_payment_status']);
			}
			else if($data['type'] == 'vendor'){
				parse_str($data['data'], $data);
				$res = OrderProducts::where('id', $data['order_product_id'])->update(['vendor_status'=>$data['select_vendor_status']]);
				$responce['status'] = 'success';
				$responce['status_key'] = $data['select_vendor_status'];
				$responce['status_value'] = config('constants.vendor_status.'.$data['select_vendor_status']);
				$responce['status_color'] = config('constants.vendor_status_color.'.$data['select_vendor_status']);
			}
			else if($data['type'] == 'art_work'){
				parse_str($data['data'], $data);
				//pr($data);die;
				$item_id = explode('-',$data['art_work_product_item_id']);
				$order_id = $item_id[0];
				$update['art_work_status'] = $data['select_art_work_status'];
				
				if(trim($data['select_art_work_date']) != '' and !empty(trim($data['select_art_work_date']))){
					$update['art_work_date'] = $data['select_art_work_date'];
					$responce['art_work_date'] = $data['select_art_work_date'];
				}else{
					$update['art_work_date'] = null;
					$responce['art_work_date'] = '';
				}
				$res = OrderProducts::where('id', $data['art_work_product_id'])->update($update);
				
				$customer_status_btn = '';
				
				// Below Code for check all order products files are uploaded or not //
				
				$check_status = DB::select('SELECT count(*) as art_work_status , (SELECT COUNT(*) FROM `order_products` WHERE `order_id` = '.$data['art_work_order_id'].') as total_product FROM `order_products` WHERE `order_id` = '.$data['art_work_order_id'].' AND art_work_status >=2');
				if($check_status[0]->art_work_status == $check_status[0]->total_product){
					Orders::where('id',$data['art_work_order_id'])->update(['customer_status'=>3]);
					
					$status_value = config('constants.customer_status.3');
					$status_color = config('constants.customer_status_color.3');
					$customer_status_btn = '<button type="button" style="background:'.$status_color.' !important;border-color:'.$status_color.' !important" class="btn btn-xs bg-olive margin customer_status" data="3" order-id="'.$data['art_work_order_id'].'" data-toggle="modal" data-target="#customer_status" title="Set Customer Status">'.$status_value.'</button>';
				}
				
				// Below Code for check all order products are approved or not //
				
				$check_status = DB::select('SELECT count(*) as art_work_status , (SELECT COUNT(*) FROM `order_products` WHERE `order_id` = '.$data['art_work_order_id'].') as total_product FROM `order_products` WHERE `order_id` = '.$data['art_work_order_id'].' AND art_work_status =6');
				if($check_status[0]->art_work_status == $check_status[0]->total_product){
					Orders::where('id',$data['art_work_order_id'])->update(['customer_status'=>4]);
					
					
					$status_value = config('constants.customer_status.4');
					$status_color = config('constants.customer_status_color.4');
					$customer_status_btn = '<button type="button" style="background:'.$status_color.' !important;border-color:'.$status_color.' !important" class="btn btn-xs bg-olive margin customer_status" data="4" order-id="'.$data['art_work_order_id'].'" data-toggle="modal" data-target="#customer_status" title="Set Customer Status">'.$status_value.'</button>';
				}
				
				
				$detail =  OrderProducts::select('order_products.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email')->where('order_products.id', $data['art_work_product_id'])->with('product')->leftJoin('orders as order', 'order_products.order_id', '=', 'order.id')->leftJoin('users as user', 'order.user_id', '=', 'user.id')->first();
				
				$email = $detail->email;
				$customer_name = $detail->customer_name;
				
				$str = '<ul>';
				$str .= '<li><b>Product Quantity : </b>'.$detail->qty.'</li>';
				$str .= '<li><b>Product Name : </b>'.$detail->product->name.'</li>';
				$str .= '</ul>';
				
				$responce['status'] = 'success';
				$responce['status_key'] = $data['select_art_work_status'];
				$responce['customer_status_btn'] = $customer_status_btn;
				$responce['status_value'] = config('constants.art_work_status.'.$data['select_art_work_status']);
				$responce['status_color'] = config('constants.art_work_status_color.'.$data['select_art_work_status']);
			}
			if($email != '' and ($type == 'customer' or $type == 'art_work')){
				$params = array('slug'=>'order_status_changed',
							'to'=>$email,
							'params'=>array(
										'{{name}}'=>$customer_name,
										'{{status}}'=>$responce['status_value'],
										'{{detail}}'=>$str,
										'{{store_name}}'=>config('constants.store_name'),
										'{{store_phone_number}}'=>config('constants.store_phone_number'),
										'{{store_email}}'=>config('constants.store_email'),
										));
				parent::sendMail($params);
			}
		}
		return json_encode($responce);
	}
	
	public function set_value(Request $request){
		$responce['status'] = false;
		$responce['res'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			if($data['type'] == 'due_date'){
				parse_str($data['data'], $data);
				$data['select_due_date'] = $data['select_due_date'];
				$res = OrderProducts::where('id', $data['due_date_product_id'])->update(['due_date'=>$data['select_due_date'],'due_date_type'=>$data['select_type']]);
				
				$responce['status'] = 'success';
				$responce['date'] = $data['select_due_date'];
				$responce['res'] = $data['select_due_date'];
				$responce['type'] = $data['select_type'];
				if($data['select_type'] == 'soft_date'){
					$responce['class_name'] = 'btn-success';
				}else{
					$responce['class_name'] = 'btn-danger';
				}
			}
			else if($data['type'] == 'tracking_id'){
				parse_str($data['data'], $data);
				//pr($data);//die;
				
				$item_id = explode('-',$data['tracking_id_product_item_id']);
				
				$shipping_via ="";
				if($data['shipping_type'] == 1){
					$link = 'http://wwwapps.ups.com/WebTracking/processInputRequest?AgreeToTermsAndConditions=yes&loc=en_US&tracknum='.$data['select_tracking_id'].'&Requester=trkinppg';
					$shipping_via = 'UPS Shipping';
				}else if($data['shipping_type'] == 2){
					$link = 'http://www.fedex.com/apps/fedextrack/?action=track&trackingnumber='.$data['select_tracking_id'].'&cntry_code=us';
					$shipping_via = 'FedEx Shipping';
				}
				else if($data['shipping_type'] == 3){
					$link = '';
					$shipping_via = $data['shipping_career'];
				}
				
				$res = OrderProducts::where('id', $data['tracking_id_product_id'])->update(['shipping_type'=>$data['shipping_type'],'shipping_career'=>$shipping_via,'tracking_id'=>$data['select_tracking_id'],'tracking_link'=>$link]);
				
				$order_detail = Orders::where('id',$item_id[0])->with(['customer','orderProduct','orderProductOptions','orderAddress'])->first();
				//$description = '<ol>';
				$description = '';
				foreach($order_detail->orderProduct as $product){
					
					// only item need to send in email for which tracking is setting using this action
					if($product->id != $data['tracking_id_product_id']) 
						continue;

					$description .= '<li style="list-style:none;padding-left:20px;"><b>'.$product->product->name.'</b><ul><li>Quantity : '.$product->qty.'</li>';
					$variant_str = '';
					$option_str = '';
					foreach($order_detail->orderProductOptions as $option){
						if($option->type == 2){
							$variant_str .= '<li>'.$option->custom_option_name.':'.$option->value.'</li>';
						}else{
							$option_str .= '<li>'.$option->custom_option_name.':'.$option->value.'</li>';
						}
					}
					$variant_str = trim($variant_str,',');
					$option_str = trim($option_str,',');
					
					$str = '';
					if(!empty($variant_str)){
						$str .= '<li>Varients: <ul>'.$variant_str.'</ul></li>';
					}
					
					if(!empty($option_str)){
						$str .= '<li>Options : <ul>'.$option_str.'</ul></li>';
					}
					
					$description .= $str.'</ul></li>';
				}
				//$description .= '</ol>';
				//echo $description;die;

				if($link !="")
				{
					$link = 'You can track your package at '.$link;
				}
				
				$params = array('slug'=>'tracking_link_to_customer',
							'to'=>$order_detail->customer->email,
							'params'=>array(
										'{{customer_name}}'=>$order_detail->customer->fname.' '.$order_detail->customer->lname,
										'{{order_id}}'=>$order_detail->id,
										'{{product_detail}}'=>$description,
										'{{tracking_number}}'=>$data['select_tracking_id'],
										'{{tracking_link}}'=>$link,
										'{{shipping_via}}'=>$shipping_via,
										'{{AGENT_NAME}}'=>Auth::user()->fname.' '.Auth::user()->lname,
										'{{SITE_URL}}'=>config('constants.SITE_URL'),
										'{{site_phone_number}}'=>config('constants.site_phone_number'),
										));
				parent::sendMail($params);
				
				
				$responce['status'] = 'success';
				$responce['res'] = $data['select_tracking_id'];
				$responce['link'] = $link;
			}
			else if($data['type'] == 'po_id'){
				parse_str($data['data'], $data);
				$res = OrderProducts::where('id', $data['po_id_product_id'])->update(['po_id'=>$data['select_po_id']]);
				$responce['status'] = 'success';
				$responce['res'] = $data['select_po_id'];
			}
		}
		return json_encode($responce);
	}
	
	public function order_edit(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		$responce['class'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$states = State::where('status',1)->pluck('stateName','stateCode')->all();
			if($data['get'] == 'form'){
				if($data['type'] == 'billing_add'){
					$detail = OrderAddress::where('order_id',$data['order_id'])->first();
					
					$responce['html'] = Form::model('edit_add',['id'=>'edit_add']).'<div class="col-xs-12">';
					$responce['html'] .= Form::hidden('type','billing_add',['id'=>'type']);
					$responce['html'] .= Form::hidden('billing_add_id',$detail->billing_add_id,['id'=>'billing_add_id']);
					$responce['html'] .= Form::hidden('order_product_table_id',$data['order_product_table_id'],['id'=>'order_product_table_id']);
					$responce['html'] .= Form::hidden('edit_add_order_id',$data['order_id'],['id'=>'edit_add_order_id']);

					$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('billing_company_name', 'Billing Company Name',array('class'=>'form-control-label')).Form::text('billing_company_name', $detail->billing_company_name,array('class'=>'form-control add_option opt','id'=>'billing_company_name','placeholder'=>'Enter Billing Company Name')).'</div>';
					$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('billing_phone_number', 'Billing Phone Number',array('class'=>'form-control-label')).Form::text('billing_phone_number', $detail->billing_phone_number,array('class'=>'form-control add_option opt','id'=>'billing_phone_number','placeholder'=>'Enter Billing Phone Number')).'</div>';

					$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('billing_fname', 'Billing First Name',array('class'=>'form-control-label')).Form::text('billing_fname', $detail->billing_fname,array('class'=>'form-control add_option','id'=>'billing_fname','placeholder'=>'Enter Billing First Name')).'</div>';
					$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('billing_lname', 'Billing Last Name',array('class'=>'form-control-label opt')).Form::text('billing_lname', $detail->billing_lname,array('class'=>'form-control add_option','id'=>'billing_lname','placeholder'=>'Enter Billing Last Name')).'</div>';

					$responce['html'] .='<div class="col-md-6 form-group">';					
					$responce['html'] .= Form::label('billing_add1', 'Billing Add 1',array('class'=>'form-control-label'));
					$responce['html'] .= Form::text('billing_add1', $detail->billing_add1,array('class'=>'form-control add_option','id'=>'billing_add1','placeholder'=>'Enter Billing Add'));
					$responce['html'] .= '</div><div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('billing_add2', 'Billing Add 2',array('class'=>'form-control-label'));
					$responce['html'] .= Form::text('billing_add2', $detail->billing_add2,array('class'=>'form-control add_option add2 opt','id'=>'billing_add2','placeholder'=>'Enter Billing Add'));
					$responce['html'] .= '</div><div class="clearfix"></div><div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('billing_zipcode', 'Billing Zipcode',array('class'=>'form-control-label'));
					$responce['html'] .= Form::number('billing_zipcode', $detail->billing_zipcode,array('class'=>'form-control add_option','id'=>'billing_zipcode','placeholder'=>'Enter Billing Zipcode'));
					$responce['html'] .= '</div><div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('billing_city', 'Billing City',array('class'=>'form-control-label'));
					$responce['html'] .= Form::text('billing_city', $detail->billing_city,array('class'=>'form-control add_option','id'=>'billing_city','placeholder'=>'Enter Billing City'));
					$responce['html'] .= '</div><div class="clearfix"></div><div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('billing_state', 'Billing State',array('class'=>'form-control-label'));
					$responce['html'] .= Form::select('billing_state',$states, $detail->billing_state,array('class'=>'form-control add_option','id'=>'billing_state','placeholder'=>'Select State'));
					$responce['html'] .= '</div><div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('billing_country', 'Billing Country',array('class'=>'form-control-label'));
					$responce['html'] .= Form::text('billing_country', $detail->billing_country,array('class'=>'form-control add_option','id'=>'billing_country'));
					$responce['html'] .= '</div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_address">Update</button><img id="edit_add_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div></div>';
					$responce['html'] .= Form::close();
					
					$responce['status'] = 'success';
				}
				if($data['type'] == 'shipping_add'){

					$orderData = Orders::where('id',$data['order_id'])->first();
					$userAddress = UserAddress::where('user_id',$orderData->user_id)->get();
					$userAddressArr = array();
					foreach($userAddress as $val)
					{
						if($val->address_name !="")
							$userAddressArr[$val->id] = $val->address_name;
						else
							$userAddressArr[$val->id] = $val->add1;
					}

					$responce['html'] = Form::model('edit_add',['id'=>'edit_add']).'<div class="col-xs-12">';
					$responce['html'] .= Form::hidden('type','shipping_add',['id'=>'type']);
					if($data['order_multiple'] == 0 and $data['order_multiple'] == '0'){
						$detail = OrderAddress::where('order_id',$data['order_id'])->select('order_address.*',DB::raw("concat(users.fname, ' ', users.lname) as name"))->leftJoin('orders','orders.id','=','order_address.order_id')->leftJoin('users','users.id','=','orders.user_id')->first();
						$responce['html'] .= Form::hidden('customer_name',$detail->name,['id'=>'customer_name']);
					}
					else{
						$responce['html'] .= Form::hidden('order_product_id',$data['order_product_id'],['id'=>'order_product_id']);
						$detail = OrderAddress::where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_table_id'])->first();
					}
					
					$responce['html'] .= Form::hidden('order_multiple',$data['order_multiple'],['id'=>'order_multiple']);
					$responce['html'] .= Form::hidden('order_product_table_id',$data['order_product_table_id'],['id'=>'order_product_table_id']);
					$responce['html'] .= Form::hidden('edit_add_order_id',$data['order_id'],['id'=>'edit_add_order_id']);
					//$responce['html'] .= Form::hidden('shipping_fname',$detail->shipping_fname,['id'=>'edit_add_order_id']);
					//$responce['html'] .= Form::hidden('shipping_lname',$detail->shipping_lname,['id'=>'edit_add_order_id']);

					if(isset($data['address_id']) && $data['address_id']>=1)
					{
						$detail = UserAddress::where('id',$data['address_id'])->first();
						$detail->shipping_company_name = $detail->company_name;
						$detail->shipping_phone_number = $detail->phone_number;
						$detail->shipping_fname = $detail->fname;
						$detail->shipping_lname = $detail->lname;
						$detail->shipping_add1 = $detail->add1;
						$detail->shipping_add2 = $detail->add2;
						$detail->shipping_ship_in_care = $detail->ship_in_care;
						$detail->shipping_zipcode = $detail->zipcode;
						$detail->shipping_city = $detail->city;
						$detail->shipping_state = $detail->state;
						$detail->shipping_country = $detail->country;										
					}
					if(count($userAddressArr) >= 1)
					{
						$responce['html'] .='<div class="col-md-12 form-group">'.Form::label('saved_address', 'Select Address',array('class'=>'form-control-label')).Form::select('saved_address', $userAddressArr,@$data['address_id'],array('class'=>'form-control add_option opt','id'=>'saved_address','placeholder'=>'Select from saved Address','order-id'=>$data['order_id'],'order-multiple'=>$data['order_multiple'],'order-product-id'=>$data['order_product_id'],'order-product-table-id'=>$data['order_product_table_id'])).'</div>';				
					}
					
					$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('shipping_company_name', 'Shipping Company Name',array('class'=>'form-control-label')).Form::text('shipping_company_name', $detail->shipping_company_name,array('class'=>'form-control add_option opt','id'=>'shipping_company_name','placeholder'=>'Enter Shipping Company Name')).'</div>';
					$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('shipping_phone_number', 'Shipping Phone Number',array('class'=>'form-control-label')).Form::text('shipping_phone_number', $detail->shipping_phone_number,array('class'=>'form-control add_option opt','id'=>'shipping_phone_number','placeholder'=>'Enter Shipping Phone Number')).'</div>';

					$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('shipping_fname', 'Shipping First Name',array('class'=>'form-control-label')).Form::text('shipping_fname', $detail->shipping_fname,array('class'=>'form-control add_option','id'=>'shipping_fname','placeholder'=>'Enter Shipping First Name')).'</div>';
					$responce['html'] .='<div class="col-md-6 form-group">'.Form::label('shipping_lname', 'Shipping Last Name',array('class'=>'form-control-label')).Form::text('shipping_lname', $detail->shipping_lname,array('class'=>'form-control add_option opt','id'=>'shipping_lname','placeholder'=>'Enter Shipping Last Name')).'</div>';					
					
					$responce['html'] .='<div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('shipping_add1', 'Shipping Add 1',array('class'=>'form-control-label'));
					$responce['html'] .= Form::text('shipping_add1', $detail->shipping_add1,array('class'=>'form-control add_option','id'=>'shipping_add1','placeholder'=>'Enter Shipping Add'));
					$responce['html'] .= '</div><div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('shipping_add2', 'Shipping Add 2',array('class'=>'form-control-label'));
					$responce['html'] .= Form::text('shipping_add2', $detail->shipping_add2,array('class'=>'form-control add2 add_option opt','id'=>'shipping_add2','placeholder'=>'Enter Shipping Add'));
					$responce['html'] .= '</div><div class="clearfix"></div>';

					$responce['html'] .='<div class="col-md-12 form-group">'.Form::label('shipping_ship_in_care', 'Ship in care of',array('class'=>'form-control-label')).Form::text('shipping_ship_in_care', $detail->shipping_ship_in_care,array('class'=>'form-control add_option opt','id'=>'shipping_ship_in_care','placeholder'=>'Enter Shipping Ship in care of')).'</div>';

					$responce['html'] .='<div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('shipping_zipcode', 'Shipping Zipcode',array('class'=>'form-control-label'));
					$responce['html'] .= Form::number('shipping_zipcode', $detail->shipping_zipcode,array('class'=>'form-control add_option','id'=>'shipping_zipcode','placeholder'=>'Enter Shipping Zipcode'));
					$responce['html'] .= '</div><div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('shipping_city', 'Shipping City',array('class'=>'form-control-label'));
					$responce['html'] .= Form::text('shipping_city', $detail->shipping_city,array('class'=>'form-control add_option','id'=>'shipping_city','placeholder'=>'Enter Shipping City'));
					$responce['html'] .= '</div><div class="clearfix"></div><div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('shipping_state', 'Shipping State',array('class'=>'form-control-label'));
					$responce['html'] .= Form::select('shipping_state',$states, $detail->shipping_state,array('class'=>'form-control add_option','id'=>'shipping_state','placeholder'=>'Select State'));
					$responce['html'] .= '</div><div class="col-md-6 form-group">';
					$responce['html'] .= Form::label('shipping_country', 'Shipping Country',array('class'=>'form-control-label'));
					$responce['html'] .= Form::text('shipping_country', $detail->shipping_country,array('class'=>'form-control add_option','id'=>'shipping_country','readonly'));
					$responce['html'] .= '</div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_address">Update</button><img id="edit_add_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div></div>';
					$responce['html'] .= Form::close();
					
					$responce['status'] = 'success';
					
				}
			}
			if($data['get'] == 'form_save'){
				parse_str($data['data'], $data);
				//pr($data);die;
				$order = Orders::where('id',$data['edit_add_order_id'])->with('orderProduct','orderProductOptions','customer')->first();
				if($data['type'] == 'billing_add'){
					$update = OrderAddress::where('order_id', $data['edit_add_order_id'])->update(['billing_company_name'=>$data['billing_company_name'],'billing_phone_number'=>$data['billing_phone_number'],'billing_fname'=>$data['billing_fname'],'billing_lname'=>$data['billing_lname'],'billing_add1'=>$data['billing_add1'],'billing_add2'=>$data['billing_add2'],'billing_zipcode'=>$data['billing_zipcode'],'billing_city'=>$data['billing_city'],'billing_state'=>$data['billing_state'],'billing_country'=>$data['billing_country']]);
					if($update){
						$responce['status'] = 'success';
						
						if(isset($data['billing_add_id']) && !empty($data['billing_add_id'])){
							UserAddress::where('id',$data['billing_add_id'])->update(['company_name'=>$data['billing_company_name'],'phone_number'=>$data['billing_phone_number'],'fname'=>$data['billing_fname'],'lname'=>$data['billing_lname'],'add1'=>$data['billing_add1'],'add2'=>$data['billing_add2'],'zipcode'=>$data['billing_zipcode'],'city'=>$data['billing_city'],'state'=>$data['billing_state'],'country'=>$data['billing_country']]);
							
							$updateOtherAdd = OrderAddress::where('order_id','!=',$data['edit_add_order_id'])->where('billing_add_id',$data['billing_add_id'])->update(['billing_company_name'=>$data['billing_company_name'],'billing_phone_number'=>$data['billing_phone_number'],'billing_fname'=>$data['billing_fname'],'billing_lname'=>$data['billing_lname'],'billing_add1'=>$data['billing_add1'],'billing_add2'=>$data['billing_add2'],'billing_zipcode'=>$data['billing_zipcode'],'billing_city'=>$data['billing_city'],'billing_state'=>$data['billing_state'],'billing_country'=>$data['billing_country']]);
							
						}
						
						if($data['billing_company_name'] !="")
						{
							$responce['html'] = $data['billing_company_name'].'<br/>';
						}
						if($data['billing_phone_number'] !="")
						{
							$responce['html'] .= $data['billing_phone_number'].'<br/>';
						}
						
						$responce['html'] .= $data['billing_fname'].' '.$data['billing_lname'].'<br/>';						
						$responce['html'] .= $data['billing_add1'].' '.$data['billing_add2'].'<br/>';
						$responce['html'] .= 'Zipcode: '.$data['billing_zipcode'].'<br/>';
						$responce['html'] .= 'City: '.$data['billing_city'].'<br/>';
						$responce['html'] .= 'State: '.$data['billing_state'].'<br/>';
						$responce['html'] .= 'Country: '.$data['billing_country'].'<br/>';
						$responce['class']	= $data['type'].'_div';
						$responce['type']	= $data['type'];
					}
				}
				if($data['type'] == 'shipping_add'){
					if($data['order_multiple'] == 0 and $data['order_multiple'] == '0'){
						$update = OrderAddress::where('order_id', $data['edit_add_order_id'])->update(['shipping_company_name'=>$data['shipping_company_name'],'shipping_phone_number'=>$data['shipping_phone_number'],'shipping_fname'=>$data['shipping_fname'],'shipping_lname'=>$data['shipping_lname'],'shipping_add1'=>$data['shipping_add1'],'shipping_add2'=>$data['shipping_add2'],'shipping_ship_in_care'=>$data['shipping_ship_in_care'],'shipping_zipcode'=>$data['shipping_zipcode'],'shipping_city'=>$data['shipping_city'],'shipping_state'=>$data['shipping_state'],'shipping_country'=>$data['shipping_country']]);
						
						$class = 'shipping_add';
						
						$address['zipcode'] = $data['shipping_zipcode'];
						$address['stateCode'] = $data['shipping_state'];
						$address['state'] = $states[$data['shipping_state']];
					}
					else{
						$update = OrderAddress::where('order_id', $data['edit_add_order_id'])->where('order_product_id', $data['order_product_table_id'])->update(['shipping_company_name'=>$data['shipping_company_name'],'shipping_phone_number'=>$data['shipping_phone_number'],'shipping_fname'=>$data['shipping_fname'],'shipping_lname'=>$data['shipping_lname'],'shipping_add1'=>$data['shipping_add1'],'shipping_add2'=>$data['shipping_add2'],'shipping_ship_in_care'=>$data['shipping_ship_in_care'],'shipping_zipcode'=>$data['shipping_zipcode'],'shipping_city'=>$data['shipping_city'],'shipping_state'=>$data['shipping_state'],'shipping_country'=>$data['shipping_country']]);
						
						$address['zipcode'] = $data['shipping_zipcode'];
						$address['stateCode'] = $data['shipping_state'];
						$address['state'] = $states[$data['shipping_state']];
						
						$responce['html'] ='';
						$class = $data['order_multiple'];
					}
					
					$weight = 0;
					$total_shipping = 0;
					$shipping_products = array();
					foreach($order->orderProduct as $key=>$product){
						if(count($product->shipping) > 0 and ($product->shipping->type == 'paid' or ($product->shipping->type == 'free_value' and  $product->shipping->min_value > $product->total))){
							if($data['order_multiple'] != 0 or $data['order_multiple'] != '0'){
								if($product->product_id == $data['order_product_id']){
									$rate = $this->calculateShipping($address['zipcode'],$product->product_weight,$order->shipping_option);
									//pr($rate);
									if($rate['status']){
										$rate_amount = number_format($rate['res']->MonetaryValue,2,'.','');
										$product_obj = OrderProducts::where('order_id',$data['edit_add_order_id'])->where('id',$data['order_product_table_id'])->first();
										$product_obj->product_shipping = $rate_amount;
										$product_obj->save();
										$total_shipping += $rate_amount;
									}
								}else{
									$total_shipping += $product->product_shipping;
								}
							}else{
								$weight += $product->product_weight;
							}
							$shipping_products[] = $product->id;
						}else{
							$total_shipping += $product->product_shipping;
						}
					}
					if($weight != 0 and ($data['order_multiple'] == 0 or $data['order_multiple'] == '0')){
						
						$rate = $this->calculateShipping($address['zipcode'],$weight,$order->shipping_option);
						if($rate['status']){
							$rate_amount = number_format($rate['res']->MonetaryValue,2,'.','');
							$total_shipping += $rate_amount;
							
							$one_LBS_ship_amount = $total_shipping/$weight;
							foreach($order->orderProduct as $key=>$product){
								if(in_array($product->id,$shipping_products)){
									$product_shipping = $product->product_weight*$one_LBS_ship_amount;
									OrderProducts::where('id',$product->id)->update(['product_shipping'=>$product_shipping]);
								}
							}
						}
					}
					$order_obj = Orders::findOrFail($data['edit_add_order_id']);
					$order_obj->shipping_fee = $total_shipping;
					
					$total = ($order_obj->sub_total-$order_obj->discount)+$total_shipping+$order_obj->sales_tax;
					$order_obj->total = number_format($total,2,'.','');
					
					$order_obj->save();
					//die;
					$responce['discount'] = number_format($order_obj->discount,2,'.','');
					$responce['shipping_fee'] = number_format($order_obj->shipping_fee,2,'.','');
					$responce['sub_total'] = number_format($order_obj->sub_total,2,'.','');
					$responce['main_total'] = number_format($order_obj->total,2,'.','');
					
					if($update){
						
						$responce['status'] = 'success';
						if($data['shipping_company_name'] != ''){
							$responce['html'] .= '<strong>'.$data['shipping_company_name'].'</strong><br/>';
						}	
						if($data['shipping_phone_number'] != ''){
							$responce['html'] .= $data['shipping_phone_number'].'<br/>';
						}					
						if($data['shipping_fname'] != '' and $data['shipping_lname'] != ''){
							$responce['html'] .= '<strong>'.$data['shipping_fname'].' '.$data['shipping_lname'].'</strong><br/>';
						}						
						$responce['html'] .= $data['shipping_add1'].' ,'.$data['shipping_add2'].'<br/>';
						if($data['shipping_ship_in_care'] != ''){
							$responce['html'] .= $data['shipping_ship_in_care'].'<br/>';
						}
						$responce['html'] .= 'Zipcode: '.$data['shipping_zipcode'].'<br/>';
						$responce['html'] .= 'City: '.$data['shipping_city'].'<br/>';
						$responce['html'] .= 'State: '.$data['shipping_state'].'<br/>';
						$responce['html'] .= 'Country: '.$data['shipping_country'].'<br/>';
						$responce['class']	= $class.'_div';
						$responce['type']	= $data['type'];
					}
					
				}
			}
		}
		return json_encode($responce);
	}
	
	public function calculateShipping($zipcode,$weight,$option){
		$shipping = new UPSShipping();
		$rate_detail = $shipping->RateCalculate($zipcode,$weight,$option);
		return $rate_detail;
	}
	
	public function order_option_edit(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		$responce['class'] = '';
		$html = array();
		$html['printing'] = '';
		$html['finishing'] = '';
		$html['production'] = '';
		$params = array();
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			if($data['get'] == 'form'){
				if($data['type'] == 'product_options'){
					$order_product = OrderProducts::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->where('item_id',$data['order_item'])->first();
					
					$detail = OrderProductOptions::where('order_id',$data['order_id'])->where('order_product_id',$order_product->id)->with('optionDetail')->get();
					//pr($detail);die;
					
					foreach($detail as $value){
						if(!empty($value->optionDetail)){
							if($value->optionDetail->option_type == 1){
								$values = json_decode($value->optionDetail->option_keys,true);
								$html[$value->optionDetail->field_group] .=  '<div class="col-sm-6 col-xs-12 form-group">';
							
								$html[$value->optionDetail->field_group] .= Form::label('options['.$value->optionDetail->field_group.']['.$value->optionDetail->id.']'.'['.str_replace(' ','_',strtolower($value->optionDetail->name)).']',$value->optionDetail->label,array('class'=>'form-control-label'));
								
								$html[$value->optionDetail->field_group] .= '<select class="form-control option_fields validate option_custom" name="options['.$value->optionDetail->field_group.']['.$value->optionDetail->id.']'.'['.str_replace(' ','_',strtolower($value->optionDetail->name)).']" id="option_custom'.$value->optionDetail->id.'" data-name="'.$value->optionDetail->name.'" data-id="'.$value->optionDetail->id.'" required><option value="" rel="0" selected="selected">Select</option>';
								
								foreach($values as $val){
									$price = 0;
									$weight = 0;
									$selected_value = '';
									
									if($value->value === $val['value']){
										$selected_value = 'selected="selected"';
									}
									
									if(array_key_exists('price',$val) and !empty($val['price'])){ $price = $val['price']; }
									if(array_key_exists('weight',$val) and !empty($val['weight'])){ $weight = $val['weight']; }
									$html[$value->optionDetail->field_group] .= '<option '.$selected_value.' value=\''.htmlentities($val['value']).'__'.$price.'\' rel="'.$price.'" data-price="'.$price.'" data-weight="'.$weight.'" data-price-type="'.$value->optionDetail->price_formate.'">'.htmlentities($val['value']).'</option>';
								}
								$html[$value->optionDetail->field_group] .= '</select></div>';
							}
							if($value->optionDetail->option_type == 2){
								$values = json_decode($value->optionDetail->option_keys,true);
								$html[$value->optionDetail->field_group] .= '<div class="col-sm-6 col-xs-12 form-group">';
								
								$html[$value->optionDetail->field_group] .= Form::label('options['.$value->optionDetail->field_group.']['.$value->optionDetail->id.']'.'['.str_replace(' ','_',strtolower($value->optionDetail->name)).']',$value->optionDetail->name,array('class'=>'form-control-label'));
								foreach($values as $val){
									$price = 0;
									if(array_key_exists('price',$val) and !empty($val['price'])){ $price = $val['price']; }
									
									$html[$value->optionDetail->field_group] .= Form::text('options['.$value->optionDetail->field_group.']['.$value->optionDetail->id.']'.'['.str_replace(' ','_',strtolower($value->optionDetail->name)).']',$params[$value->optionDetail->id],['class'=>'form-control option_fields option_custom validate','placeholder'=>$value->optionDetail->name,'rel'=>$price,'required']);
								}
								$html[$value->optionDetail->field_group] .=  '</div>';
							}
						}
						
						
						if($value->custom_option_id == 0){
							$params[$value->custom_option_name] = $value->value;
						}else{
							$params[$value->custom_option_id] = $value->value;
						}
					}
					
					$product = Products::where('id',$data['order_product_id'])->with('shipping','variants','variantCombinantion')->first();
					
					$responce['status'] = "success";
					
					$responce['html'] = Form::model('edit_product_option',['id'=>'edit_product_option']).'<div class="col-md-12 form-group order_option_box">';
					$responce['html'] .= Form::hidden('price_default',$order_product->price_default,['class'=>'form-control']);
					$responce['html'] .= Form::hidden('quantity',$order_product->qty,['class'=>'form-control','id'=>'quantity']);
					$responce['html'] .= Form::hidden('order_id',$data['order_id'],['class'=>'form-control']);
					$responce['html'] .= Form::hidden('order_product_id',$data['order_product_id'],['class'=>'form-control']);
					$responce['html'] .= Form::hidden('order_product_table_id',$order_product->id,['class'=>'form-control']);
					$responce['html'] .= Form::hidden('order_qty',$data['order_qty'],['class'=>'form-control']);
					$responce['html'] .= Form::hidden('order_item',$data['order_item'],['class'=>'form-control']);
					$responce['html'] .= Form::hidden('min_width',(!empty($product->min_width))?$product->min_width:0);
					$responce['html'] .= Form::hidden('max_width',(!empty($product->max_width))?$product->max_width:0);
					$responce['html'] .= Form::hidden('min_height',(!empty($product->min_height))?$product->min_height:0);
					$responce['html'] .= Form::hidden('max_height',(!empty($product->max_height))?$product->max_height:0);
					
					if($product->show_width_height == 1 or count($product->variants) > 0){
						$responce['html'] .= '<div class="form-group col-xs-12"><h3 class="page-header">Printing Options</h3>';
					}
					//pr($product->variants->toArray());die;
					$selected_id = '';
					foreach($product->variants as $variant){
						$variant_options = array();
						foreach($variant->variantValues as $value){
							$check = OrderProductOptions::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->where('custom_option_name',$variant->name)->first();
							if($check->value == $value->value){
								$selected_id = $value->id;
							}
							$variant_options[$value->id] = $value->value;
						}
						//pr($variant_options);
						$responce['html'] .= '<div class="col-xs-12 col-sm-6 form-group">'.Form::label('variants['.$variant->id.']',$variant->name,array('class'=>'form-control-label')). Form::select('variants['.$variant->id.']',$variant_options,$selected_id,['class'=>'form-control option_fields validate variants_option','id'=>'']).'</div>';
					}					
					
					if($product->show_width_height == 1){
						$responce['html'] .= '<div class="col-sm-6 col-xs-12 form-group">'.Form::label('options[printing][0][width]','Width(ft)',array('class'=>'form-control-label')). Form::number('options[printing][0][width]',$params['Width'],['class'=>'form-control option_fields validate','id'=>'width','min'=>1]).'</div><div class="col-sm-6 col-xs-12 form-group">'.Form::label('options[printing][0][height]','Height(ft)',array('class'=>'form-control-label')). Form::number('options[printing][0][height]',$params['Height'],['class'=>'form-control option_fields validate','id'=>'height','min'=>1]).'</div>';
					}
					
					if($html['printing'] != ''){			
						$responce['html'] .= $html['printing'].'</div>';
					}else{
						$responce['html'] .= '</div>';
					}
					
					if($html['finishing'] != ''){
						$responce['html'] .= '<div class="form-group col-xs-12"><h3 class="page-header">Finishing Options</h3>'.$html['finishing'].'</div>';
					}
					if($html['production'] != ''){	
						$responce['html'] .= '<div class="form-group col-xs-12"><h3 class="page-header">Production Options</h3>'.$html['production'].'</div>';
					}
					$responce['html'] .= '<div class="form-group col-xs-12 hide extra_option_div"><h3 class="page-header">New Options</h3></div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_option">Update</button><img id="edit_option_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div><div class="col-md-6 form-group"><button type="button" class="btn btn-primary add_option pull-right">Add Option</button></div></div>';
					$responce['html'] .= Form::close();
					
					$responce['price_sqft_area'] = $product->price_sqft_area;
					$responce['variant_check'] = $product->variant;
					$responce['show_width_height'] = $product->show_width_height;
					
					// Make a array of old combinations //
					$sets = array();
					//pr($product->variantCombinantion);
					if(count($product->variantCombinantion) > 0){
						$i = 0;
						foreach($product->variantCombinantion as $val){
							$key = $val->varient_id1;
							if($val->varient_id2 != ''){
								$key .= '-'.$val->varient_id2;
							}
							if($product->price_sqft_area == 1){
								$sets[$key]['variable_price'] = 1;
								$sets[$key]['price'][$i]['price'] = $val['price'];  
								$sets[$key]['price'][$i]['min_area'] = $val['min_area'];  
								$sets[$key]['price'][$i]['max_area'] = $val['max_area'];  
							}else{
								$sets[$key]['variable_price'] = 0;
								$sets[$key]['price'] = $val['price'];
							}
							$i++;
						}
					}
					//pr($sets);die;
					
					$discount = array();
					$discount_all = Discounts::where('status',1)->orderBy('quantity','ASC')->get();
					foreach($discount_all as $val){
						if(empty($val->products)){
							$discount[$val->quantity] = $val->percent;
						}else{
							$ids = explode(',',$val->products);
							if(in_array($product->id,$ids)){
								$discount[$val->quantity] = $val->percent;
							}
						}
					}
					
					$responce['combination'] = $sets;
					$responce['discount'] = $discount;
					$responce['product_id'] = $product->id;
					$responce['product_weight'] = $product->shipping_weight;
					$responce['shipping_type'] = (isset($product->shipping->type))?$product->shipping->type:'';
					$responce['shipping_min_value'] = (isset($product->shipping->min_value))?$product->shipping->min_value:'';
					$responce['shipping_weight'] = (isset($product->shipping->weight))?$product->shipping->weight:'';
					$responce['shipping_price'] = (isset($product->shipping->price))?$product->shipping->price:'';
				}
			}
			if($data['get'] == 'form_save'){
				$object_data = $data['object'];
				parse_str($data['data'], $data);
				//pr($object_data);
				//pr($data);die;
				
				if(array_key_exists('new_option',$data)){
					foreach($data['new_option'] as $key=>$opt){
						/* Below Code for save new option value */
						
						$obj = new CustomOptions();
						$obj->field_group = $opt['field_group'];
						$obj->price_formate = $opt['price_formate'];
						$obj->name = $opt['name'];
						$obj->label = $opt['name'];
						$obj->option_type = 1;
						
						$temp[$key]['value'] = $opt['value'];
						$temp[$key]['price'] = $opt['price'];
						$temp[$key]['weight'] = $opt['weight'];
						$temp[$key]['flat_rate_additional_price'] = $opt['flat_rate_additional_price'];
						$obj->option_keys = json_encode($temp);
						
						$obj->free = 0;
						$obj->status = 0;
						$obj->save();
						
						/* Below Code for update object data array with new option value */
						
						$object_data['custom_option'][$obj->id]['id'] = $obj->id;
						$object_data['custom_option'][$obj->id]['name'] = $opt['name'];
						$object_data['custom_option'][$obj->id]['value'] = $opt['value'];
						$object_data['custom_option'][$obj->id]['price'] = $opt['price'];
						
						if(!empty($opt['weight']))
							$object_data['custom_option'][$obj->id]['weight'] = $opt['weight'];
						else
							$object_data['custom_option'][$obj->id]['weight'] = 0;
						
						$object_data['custom_option'][$obj->id]['price_type'] = $opt['price_formate'];
						
						/* Below Code for update new option value in order product option table */
						
						$OrderOptObj = new OrderProductOptions();
						$OrderOptObj->order_id = $data['order_id'];
						$OrderOptObj->product_id = $data['order_product_id'];
						$OrderOptObj->order_product_id = $data['order_product_table_id'];
						$OrderOptObj->type = 1;
						$OrderOptObj->custom_option_id = $obj->id;
						$OrderOptObj->custom_option_name = $opt['name'];
						$OrderOptObj->custom_option_field_group = $opt['field_group'];
						$OrderOptObj->value = $opt['value'];
						$OrderOptObj->price = $opt['price'];
						$OrderOptObj->save();
						
						/* Below Code for update post data array value */
						
						$data['options'][$opt['field_group']][$obj->id][str_replace(' ','_',strtolower($opt['name']))] = $opt['value'];
					}
				}
				
				//pr($object_data);
				//pr($data);die;
				
				$main_array = $data;
				$main_array['object_data'] = $object_data;
				
				$shipping_weight = new CalculateShippingWeight();
				$result = $shipping_weight->edit_weight($main_array);
				//pr($result);die;
				
				$weight = number_format($result['total_weight'],2,'.','');
				$product_weight = $result['product_weight'];
				
				/* Order Product Variants Save */
				if(array_key_exists('variants',$data)){
					foreach($data['variants'] as $key=>$val){
						$db = ProductVariant::select('product_variant.name as name','value.value as value');
		
						$detail= $db->leftJoin('product_variant_values as value','value.variant_id','=','product_variant.id')->where('product_variant.id',$key)->where('value.id',$val)->first();
						//pr($detail->toArray());
						
						$order_product_option = OrderProductOptions::where('order_id',$data['order_id']);
						$order_product_option->where('product_id',$data['order_product_id']);
						$order_product_option->where('order_product_id',$data['order_product_table_id']);
						$order_product_option->where('custom_option_name',$detail->name);
						$order_product_option->update(['value'=>$detail->value,'price'=>$object_data['variant_price']]);
					}
				}
				
				foreach($data['options'] as $key1=>$val){
					foreach($val as $key2=>$val2){
						foreach($val2 as $key3=>$val3){
							$order_product_option = OrderProductOptions::where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_table_id']);
							$order_product_option->where('product_id',$data['order_product_id']);
							$order_product_option->where('custom_option_id',$key2);
							$str = explode('__',$val3);
							if($key2 == 0){
								$order_product_option->where('custom_option_name',ucwords($key3));
								if($key3 == 'width'){
									$price = ($val2['width']*$val2['height'])*$data['price_default'];
									$order_product_option->update(['value'=>$str[0],'price'=>$price]);	
								}else{
									$order_product_option->update(['value'=>$str[0]]);
								}
							}
							else{
								if(array_key_exists(1,$str)){
									$order_product_option->update(['custom_option_name'=>ucwords(str_replace('_',' ',$key3)),'value'=>$str[0],'price'=>$str[1]]);
								}else{
									$order_product_option->update(['custom_option_name'=>ucwords(str_replace('_',' ',$key3)),'value'=>$str[0]]);
								}
							}
						}
					}
				}
				
				orderProducts::where('id',$data['order_product_table_id'])->where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->update(['price_default'=>number_format($object_data['variant_price'],2,'.',''),'product_weight'=>$product_weight[$data['order_product_table_id']]['weight'],
					'price'=>number_format($object_data['gross_price'],2,'.',''),
					'gross_total'=>number_format($object_data['gross_total'],2,'.',''),
					'qty_discount'=>number_format($object_data['qty_discount'],2,'.',''),
					'total'=>number_format($object_data['total'],2,'.','')
				]);
				
				$db = Orders::where('id', $data['order_id'])->with(['orderProduct','orderProductOptions','orderAddresses']);
				$order = $db->first();
				//pr($order);die;
				
				$responce['status'] = 'success';
				$responce['class'] = $data['order_item'];
				$product_total = 0;
				$main_total = 0;
				$discount = $order->discount;
				$sales_tax = $order->sales_tax;
				$params = array();
				
				//pr($order->orderProduct);die;
				
				foreach($order->orderProduct as $val){
					$params[$val->id]['id'] = $val->id;
					$params[$val->id]['product_id'] = $val->product_id;
					$params[$val->id]['qty'] = $val->qty;
					$params[$val->id]['total'] = $val->total;
					
					if(array_key_exists($val->id,$product_weight)){
						$params[$val->id]['product_weight'] = $product_weight[$val->id]['weight'];
					}else{
						$params[$val->id]['product_weight'] = $val->product_weight;
					}
					
					if(count($val->shipping) > 0 and ($val->shipping->type == 'paid' or ($val->shipping->type == 'free_value' and $val->total < $val->shipping->min_value))){
						$params[$val->id]['shipping'] = true;
						$params[$val->id]['shipping_type'] = $val->shipping->type;
						$params[$val->id]['shipping_price'] = $val->shipping->price;
						$params[$val->id]['shipping_min_value'] = $val->shipping->min_value;
						$params[$val->id]['shipping_reduce_price'] = $val->shipping->reduce_price;
						$params[$val->id]['shipping_additional_qty_price'] = $val->shipping->additional_qty_price;
					}
					else if(count($val->shipping) > 0 and $val->shipping->type == 'flat'){
						$params[$val->id]['shipping'] = false;
						$params[$val->id]['shipping_type'] = $val->shipping->type;
						$params[$val->id]['shipping_price'] = $val->shipping->price;
						$params[$val->id]['shipping_min_value'] = $val->shipping->min_value;
						$params[$val->id]['shipping_reduce_price'] = $val->shipping->reduce_price;
						$params[$val->id]['shipping_additional_qty_price'] = $val->shipping->additional_qty_price;
					}else{
						$params[$val->id]['shipping'] = false;
						$params[$val->id]['shipping_type'] = '';
						$params[$val->id]['shipping_price'] = 0;
						$params[$val->id]['shipping_min_value'] = 0;
						$params[$val->id]['shipping_reduce_price'] = 0;
						$params[$val->id]['shipping_additional_qty_price'] = 0;
					}
					
					
					// Check below that product variant has additional shipping price or not //
					$product = Products::where('id',$val->product_id)->with('variants')->first();
					$variant_options = array();
					foreach($product->variants as $variant){
						foreach($variant->variantValues as $value){
							$check = OrderProductOptions::where('order_id',$data['order_id'])->where('order_product_id',$val->id)->where('custom_option_name',$variant->name)->first();
							if($check->value == $value->value){
								$variant_options[] = $value->id;
							}
						}
					}
										
					$db = ProductVariantPrice::select('shipping_weight','shipping_price')->where('product_id',$val->product_id);
					$j = 1;
					foreach($variant_options as $ids){
						$db->where('varient_id'.$j,$ids);
						$j++;
					}
					$variant_weight = $db->first();
					
					
					if(!empty($variant_weight->shipping_price) and isset($val->shipping) and $val->shipping->type == 'flat'){
						$additional_shipping = number_format($variant_weight->shipping_price,2,'.','');
						$params[$val->id]['shipping_price'] += $additional_shipping;
					}
					
					
					// old code for add varient price in shipping rate of flat shipping //
					
					/* pr($data['variants']);
					$db = ProductVariantPrice::select('shipping_price')->where('product_id',$val->product_id);
					$j = 1;
					foreach((array)@$data['variants'] as $key=>$variant){
						$db->where('varient_id'.$j,$variant);
						$j++;
					}
					$variant_weight = $db->first();				
					
					if(!empty($variant_weight->shipping_price) and isset($val->shipping) and $val->shipping->type == 'flat'){
						$params[$val->id]['shipping_price'] += $variant_weight->shipping_price;
					} */
					
					if($val->id == $data['order_product_table_id']){
						$responce['html'] .= '<strong>'.$val->product->name.'.</strong><div class="col-xs-12">';
						$product_total = $val->total;
						foreach($order->orderProductOptions as $options){
							if($data['order_product_table_id'] == $options->order_product_id){
								if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height'){
									$params[$options->order_product_id][$options->custom_option_name] = $options->value;
									$responce['html'] .= $options->custom_option_name.'(ft)'.':'.$options->value.'<br/>';
								}else{
									$responce['html'] .= $options->custom_option_name.':'.$options->value.'<br/>';
								}
							}
						}
					}else{
						foreach($order->orderProductOptions as $options){
							if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height'){
								$params[$options->order_product_id][$options->custom_option_name] = $options->value;
							}
						}
					}
					foreach($order->orderAddresses as $add){
						if($val->id == $add->order_product_id){
							$params[$val->id]['zipcode'] = $add->shipping_zipcode;
						}
					}
					$main_total += $val->total;
				}
				
				foreach($order->orderProductOptions as $options){
					if($options->custom_option_id != 0 && !empty($options->custom_option_id)){
						$option_detail = CustomOptions::where('id',$options->custom_option_id)->first();
						$option_keys = json_decode($option_detail->option_keys,true);
						$flat_rate_additional_price = 0;
						
						foreach($option_keys as $key=>$value){
							if(isset($value['value']) && $value['value'] == $options->value && isset($value['flat_rate_additional_price']) && !empty($value['flat_rate_additional_price'])){ 
								$flat_rate_additional_price = $value['flat_rate_additional_price']; 
							}
						}
						
						$flat_rate_additional_price = number_format($flat_rate_additional_price,2,'.','');
						
						$params[$options->order_product_id]['shipping_price'] += $flat_rate_additional_price;
					}
				}
				
				//pr($params);die;
				
				$final_shipping = 0;
				$product_shipping = 0;
				if($order->multiple_shipping == 0){
					// Below for loop code for add other order product weight //
					
					foreach($order->orderProduct as $detail){
						if($detail->id != $data['order_product_table_id']){
							if(count($detail->shipping) > 0 and ($detail->shipping->type == 'paid' or ($detail->shipping->type == 'free_value' and $detail->total < $detail->shipping->min_value))){
								$weight += $detail->product_weight;
							}
						}
					}
					
					$zipcode = $params[$data['order_product_table_id']]['zipcode'];
					if($weight > 0){
						$rate = $this->calculateShipping($zipcode,$weight,$order->shipping_option);
						
						if($rate['status']){
							$shipping_amount = number_format($rate['res']->MonetaryValue,2,'.','');
							
							$one_LBS_ship_amount = $shipping_amount/$weight;
							$one_LBS_ship_amount = number_format($one_LBS_ship_amount,2,'.','');
							
							foreach($params as $key=>$val){
								$price = 0.00;
								if($val['shipping_type'] == 'paid' or ($val['shipping_type'] == 'free_value' and $val['total'] < $val['shipping_min_value'])){
									$price = $one_LBS_ship_amount*$val['product_weight'];
									$price = number_format($price,2,'.','');
								}else if($val['shipping_type'] == 'flat'){
									if($val['shipping_reduce_price'] == 1){
										$qty = $val['qty'] - 1;
										$price = $val['shipping_price']+($qty*$val['shipping_additional_qty_price']);
										$price = number_format($price,2,'.','');
									}else{
										$qty = $val['qty'];
										$price = $val['shipping_price']*$qty;
										$price = number_format($price,2,'.','');
									}
								}
								orderProducts::where('id',$key)->update(['product_shipping'=>$price]);
							}
						}
					}else{
						foreach($params as $k1=>$v1){
							if($v1['shipping_type'] == 'flat'){
								if($v1['shipping_reduce_price'] == 1){
									$qty = $v1['qty'] - 1;
									$price = $v1['shipping_price']+($qty*$v1['shipping_additional_qty_price']);
									$price = number_format($price,2,'.','');
								}else{
									$price = $v1['qty']*(number_format($v1['shipping_price'],2,'.',''));
								}
								orderProducts::where('id',$v1['id'])->update(['product_shipping'=>$price]);
							}
						}
					}
				}else{
					$product_wght = number_format($product_weight[$data['order_product_table_id']]['weight'],2,'.','');
					if($product_weight[$data['order_product_table_id']]['product_shipping']){
						$zipcode = $params[$data['order_product_table_id']]['zipcode'];
						$rate = $this->calculateShipping($zipcode,$product_wght,$order->shipping_option);
						if($rate['status']){
							$product_shipping = number_format($rate['res']->MonetaryValue,2,'.','');
						}
					}else{
						if($product_weight[$data['order_product_table_id']]['product_shipping_type'] == 'flat'){
							if($product_weight[$data['order_product_table_id']]['product_reduce_price'] == 1){
								$qty = $product_weight[$data['order_product_table_id']]['product_qty'] - 1;
								$price = $product_weight[$data['order_product_table_id']]['product_shipping_price']+($qty*$product_weight[$data['order_product_table_id']]['product_additional_qty_price']);
								$product_shipping = number_format($price,2,'.','');
							}else{
								$product_shipping = $product_weight[$data['order_product_table_id']]['product_qty']*(number_format($product_weight[$data['order_product_table_id']]['product_shipping_price'],2,'.',''));
							}
						}
					}
					
					orderProducts::where('id',$data['order_product_table_id'])->update(['product_weight'=>$product_wght,'product_shipping'=>$product_shipping]);
				}
				
				$products = orderProducts::where('order_id',$data['order_id'])->get();
				foreach($products as $key=>$val){
					$final_shipping += $val->product_shipping;
				}
				//echo $final_shipping;die;
				
				$main_total = number_format($main_total,2,'.','');
				$total = ($main_total-$discount)+$final_shipping+$sales_tax;
				
				$order = Orders::findOrFail($data['order_id']);
				$order->sub_total = $main_total;
				$order->discount = $discount;
				$order->sales_tax = $sales_tax;
				$order->shipping_fee = $final_shipping;
				$order->total = number_format($total,2,'.','');
				$order->save();				
				
				$responce['html'] .= '</div>';
				$responce['product_total'] = number_format($product_total,2,'.','');
				$responce['default_price'] = number_format($object_data['variant_price'],2,'.','');
				$responce['gross_price'] = number_format($object_data['gross_price'],2,'.','');
				$responce['gross_total'] = number_format($object_data['gross_total'],2,'.','');
				$responce['qty_discount'] = number_format($object_data['qty_discount'],2,'.','');
				
				$responce['discount'] = number_format($order->discount,2,'.','');
				$responce['shipping_fee'] = number_format($order->shipping_fee,2,'.','');
				$responce['sub_total'] = number_format($order->sub_total,2,'.','');
				$responce['main_total'] = number_format($order->total,2,'.','');
			}
		}
		return json_encode($responce);
	}
	
	public function order_values_edit(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		$responce['class'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			if(isset($data['data']))
			{
				$data['data'] = $data['data'].'&get='.$data['get'];
				parse_str($data['data'], $data);				
			}
			//pr($data);die;

			if($data['get'] == 'form'){
				if($data['type'] == 'qty'){
					$order_product = OrderProducts::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->where('item_id',$data['order_item'])->first();
					
					$responce['html'] = Form::model('edit_order_values',['id'=>'edit_order_values']).'<div class="col-xs-12"><div class="col-md-6 form-group">';
					$responce['html'] .= Form::hidden('type',$data['type'],['id'=>'type']);
					$responce['html'] .= Form::hidden('order_product_table_id',$order_product->id,['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_item',$data['order_item'],['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_id',$data['order_id'],['id'=>'order_id']);
					$responce['html'] .= Form::hidden('order_product_id',$data['order_product_id'],['id'=>'order_product_id']);
					
					$responce['html'] .= Form::label('quantity', 'Quantity',array('class'=>'form-control-label'));
					$responce['html'] .= Form::number('quantity', $data['order_value'],array('class'=>'form-control','id'=>'quantity','type'=>'number'));
					
					$responce['html'] .= '</div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_order_product_values">Update</button><img id="edit_product_values_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div></div>';
					
					$responce['status'] = 'success';
				}
				elseif($data['type'] == 'rate'){
					$order_product = OrderProducts::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->where('item_id',$data['order_item'])->first();
					$responce['html'] = Form::model('edit_order_values',['id'=>'edit_order_values']).'<div class="col-xs-12"><div class="col-md-6 form-group">';
					$responce['html'] .= Form::hidden('type',$data['type'],['id'=>'type']);
					$responce['html'] .= Form::hidden('order_product_table_id',$order_product->id,['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_item',$data['order_item'],['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_id',$data['order_id'],['id'=>'order_id']);
					$responce['html'] .= Form::hidden('order_product_id',$data['order_product_id'],['id'=>'order_product_id']);
					$responce['html'] .= Form::label('price_default', 'Rate',array('class'=>'form-control-label'));
					$responce['html'] .= '<div class="input-group"><span class="input-group-addon">$</span>';
					$responce['html'] .= Form::number('price_default', $data['order_value'],array('class'=>'form-control','id'=>'price_default','type'=>'number'));
					$responce['html'] .= '</div></div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_order_product_values">Update</button><img id="edit_product_values_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div></div>';
					
					$responce['status'] = 'success';
				}
				elseif($data['type'] == 'price'){
					$order_product = OrderProducts::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->where('item_id',$data['order_item'])->first();
					$responce['html'] = Form::model('edit_order_values',['id'=>'edit_order_values']).'<div class="col-xs-12"><div class="col-md-6 form-group">';
					$responce['html'] .= Form::hidden('type',$data['type'],['id'=>'type']);
					$responce['html'] .= Form::hidden('order_product_table_id',$order_product->id,['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_item',$data['order_item'],['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_id',$data['order_id'],['id'=>'order_id']);
					$responce['html'] .= Form::hidden('order_product_id',$data['order_product_id'],['id'=>'order_product_id']);
					$responce['html'] .= Form::label('price_default', 'Rate',array('class'=>'form-control-label'));
					$responce['html'] .= '<div class="input-group"><span class="input-group-addon">$</span>';
					$responce['html'] .= Form::number('price', $data['order_value'],array('class'=>'form-control','id'=>'price','type'=>'number'));
					$responce['html'] .= '</div></div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_order_product_values">Update</button><img id="edit_product_values_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div></div>';
					
					$responce['status'] = 'success';
				}
				elseif($data['type'] == 'gross_total'){
					$order_product = OrderProducts::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->where('item_id',$data['order_item'])->first();
					$responce['html'] = Form::model('edit_order_values',['id'=>'edit_order_values']).'<div class="col-xs-12"><div class="col-md-6 form-group">';
					$responce['html'] .= Form::hidden('type',$data['type'],['id'=>'type']);
					$responce['html'] .= Form::hidden('order_product_table_id',$order_product->id,['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_item',$data['order_item'],['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_id',$data['order_id'],['id'=>'order_id']);
					$responce['html'] .= Form::hidden('order_product_id',$data['order_product_id'],['id'=>'order_product_id']);
					$responce['html'] .= Form::label('gross_total', 'Rate',array('class'=>'form-control-label'));
					$responce['html'] .= '<div class="input-group"><span class="input-group-addon">$</span>';
					$responce['html'] .= Form::number('gross_total', $data['order_value'],array('class'=>'form-control','id'=>'gross_total','type'=>'number'));
					$responce['html'] .= '</div></div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_order_product_values">Update</button><img id="edit_product_values_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div></div>';
					
					$responce['status'] = 'success';
				}
			}
			elseif($data['get'] == 'form_save' && $data['type']=='gross_total'){
						
				$detail = OrderProducts::where('order_id', $data['order_id'])->get();
				$product_total = 0;
				$main_total = 0;			
				$old_qty = 0;
				foreach($detail as $val){
					if($val->id == $data['order_product_table_id']){
						$qty = $val->qty;

						// Below code for get all discounts or set discount according to qty //						
						$discount_all = Discounts::where('status',1)->orderBy('quantity','ASC')->get();
						$discount_per = 0;
						foreach($discount_all as $value){
							if(empty($value->products) && $value->quantity<=$qty){
								$discount_per = number_format($value->percent,2,'.','');
							}else{
								$ids = explode(',',$value->products);
								if(in_array($val->product_id,$ids) && $value->quantity<=$qty){
									$discount_per = number_format($value->percent,2,'.','');
								}
							}
						}	

						$gross_total = $data['gross_total'];
						$gross_price = $gross_total / $qty;						
						$qty_discount = ($gross_total *  $discount_per)/100;				
						$total =  $gross_total - $qty_discount;
						$total =  number_format($total,2,'.','');
						$gross_total =  number_format($gross_total,2,'.','');
						$gross_price =  number_format($gross_price,2,'.','');
						
						// Below code for update order products price //						
						orderProducts::where('order_id',$data['order_id'])->where('id',$data['order_product_table_id'])->update(['price'=>$gross_price,'gross_total'=>$gross_total,'qty_discount'=>$qty_discount,'total'=>$total]);						
						$main_total += $total;
					}else{
						$main_total += $val->total;
					}
				}
				
				$db = Orders::where('id', $data['order_id'])->with(['orderProduct','orderProductOptions','orderAddresses']);
				$order = $db->first();											
				$main_total = number_format($main_total,2,'.','');
				$order_total = ($main_total - $order->discount) + $order->shipping_fee + $order->sales_tax;
				
				$order = Orders::findOrFail($data['order_id']);
				$order->sub_total = $main_total;				
				$order->total = number_format($order_total,2,'.','');
				$order->save();				
			}
			elseif($data['get'] == 'form_save' && $data['type']=='price'){
						
				$detail = OrderProducts::where('order_id', $data['order_id'])->get();
				$product_total = 0;
				$main_total = 0;			
				$old_qty = 0;
				foreach($detail as $val){
					if($val->id == $data['order_product_table_id']){
						$qty = $val->qty;				

						// Below code for get all discounts or set discount according to qty //						
						$discount_all = Discounts::where('status',1)->orderBy('quantity','ASC')->get();
						$discount_per = 0;
						foreach($discount_all as $value){
							if(empty($value->products) && $value->quantity<=$qty){
								$discount_per = number_format($value->percent,2,'.','');
							}else{
								$ids = explode(',',$value->products);
								if(in_array($val->product_id,$ids) && $value->quantity<=$qty){
									$discount_per = number_format($value->percent,2,'.','');
								}
							}
						}				
						
						$gross_price = $data['price'];
						$gross_total = number_format(($gross_price*$qty),2,'.','');						
						$qty_discount = ($gross_total *  $discount_per)/100;						
						$total =  $gross_total - $qty_discount;
						$total =  number_format($total,2,'.','');					
						
						// Below code for update order products price //						
						orderProducts::where('order_id',$data['order_id'])->where('id',$data['order_product_table_id'])->update(['price'=>$gross_price,'gross_total'=>$gross_total,'qty_discount'=>$qty_discount,'total'=>$total]);						
						$main_total += $total;
					}else{
						$main_total += $val->total;
					}
				}
				
				$db = Orders::where('id', $data['order_id'])->with(['orderProduct','orderProductOptions','orderAddresses']);
				$order = $db->first();											
				$main_total = number_format($main_total,2,'.','');
				$order_total = ($main_total - $order->discount) + $order->shipping_fee + $order->sales_tax;
				
				$order = Orders::findOrFail($data['order_id']);
				$order->sub_total = $main_total;				
				$order->total = number_format($order_total,2,'.','');
				$order->save();				
			}
			else if($data['get'] == 'form_save' && $data['type']=='qty'){
				$product_total = 0;
				$main_total = 0;
				
				$detail = OrderProducts::where('order_id', $data['order_id'])->get();
				$old_qty = 0;
				foreach($detail as $val){
					if($val->id == $data['order_product_table_id']){
						$old_qty = $val->qty;
						$width = 1;
						$height = 1;					
												
						$qty = $data['quantity'];
						$variant_price = $val->price_default;
		
						// Below code for get all discounts or set discount according to qty //						
						$discount_all = Discounts::where('status',1)->orderBy('quantity','ASC')->get();
						$discount_per = 0;
						foreach($discount_all as $value){
							if(empty($value->products) && $value->quantity<=$qty){
								$discount_per = number_format($value->percent,2,'.','');
							}else{
								$ids = explode(',',$value->products);
								if(in_array($val->product_id,$ids) && $value->quantity<=$qty){
									$discount_per = number_format($value->percent,2,'.','');
								}
							}
						}				
						
						$gross_price = $val->price;
						$gross_total = number_format(($gross_price*$qty),2,'.','');						
						$qty_discount = ($gross_total *  $discount_per)/100;						
						$total =  $gross_total - $qty_discount;
						$total =  number_format($total,2,'.','');						
						
						// Below code for calculate product weight according new QTY //
						
						$new_weight = $val->product_weight/$old_qty;
						$wght = $qty*$new_weight;
						$wght = number_format($wght,2,'.','');
						
						// Below code for update order products price //
						
						orderProducts::where('order_id',$data['order_id'])->where('id',$data['order_product_table_id'])->update(['price'=>$gross_price,'gross_total'=>$gross_total,'qty'=>$qty,'qty_discount'=>$qty_discount,'total'=>$total,'product_weight'=>$wght]);						
						$main_total += $total;
					}else{
						$main_total += $val->total;
					}
				}
				
				$db = Orders::where('id', $data['order_id'])->with(['orderProduct','orderProductOptions','orderAddresses']);
				$order = $db->first();			
				
				/* Shipping Calculations */
				$params = array();
				foreach($order->orderProduct as $val){
					$params[$val->id]['id'] = $val->id;
					$params[$val->id]['product_id'] = $val->product_id;
					$params[$val->id]['qty'] = $val->qty;
					$params[$val->id]['product_weight'] = $val->product_weight;
					$params[$val->id]['total'] = $val->total;
				
					if(count($val->shipping) > 0 and ($val->shipping->type == 'paid' or ($val->shipping->type == 'free_value' and $val->total < $val->shipping->min_value))){
						$params[$val->id]['shipping'] = true;
						$params[$val->id]['shipping_type'] = $val->shipping->type;
						$params[$val->id]['shipping_price'] = $val->shipping->price;
						$params[$val->id]['shipping_min_value'] = $val->shipping->min_value;
						$params[$val->id]['shipping_reduce_price'] = $val->shipping->reduce_price;
						$params[$val->id]['shipping_additional_qty_price'] = $val->shipping->additional_qty_price;
					}
					else if(count($val->shipping) > 0 and $val->shipping->type == 'flat'){
						$params[$val->id]['shipping'] = false;
						$params[$val->id]['shipping_type'] = $val->shipping->type;
						$params[$val->id]['shipping_price'] = $val->shipping->price;
						$params[$val->id]['shipping_min_value'] = $val->shipping->min_value;
						$params[$val->id]['shipping_reduce_price'] = $val->shipping->reduce_price;
						$params[$val->id]['shipping_additional_qty_price'] = $val->shipping->additional_qty_price;
					}else{
						$params[$val->id]['shipping'] = false;
						$params[$val->id]['shipping_type'] = '';
						$params[$val->id]['shipping_price'] = 0;
						$params[$val->id]['shipping_min_value'] = 0;
						$params[$val->id]['shipping_reduce_price'] = 0;
						$params[$val->id]['shipping_additional_qty_price'] = 0;
					}
					
					
					$product = Products::where('id',$val->product_id)->with('variants')->first();
					$variant_options = array();
					foreach($product->variants as $variant){
						foreach($variant->variantValues as $value){
							$check = OrderProductOptions::where('order_id',$data['order_id'])->where('order_product_id',$val->id)->where('custom_option_name',$variant->name)->first();
							if($check->value == $value->value){
								$variant_options[] = $value->id;
							}
						}
					}
										
					$db = ProductVariantPrice::select('shipping_weight','shipping_price')->where('product_id',$val->product_id);
					$j = 1;
					foreach($variant_options as $ids){
						$db->where('varient_id'.$j,$ids);
						$j++;
					}
					$variant_weight = $db->first();
					
					if(!empty($variant_weight->shipping_price) and isset($val->shipping) and $val->shipping->type == 'flat'){
						$additional_shipping = number_format($variant_weight->shipping_price,2,'.','');
						$params[$val->id]['shipping_price'] += $additional_shipping;
					}
					
					if($val->id == $data['order_product_table_id']){
						$responce['html'] .= '<strong>'.$val->product->name.'.</strong><div class="col-xs-12">';
						$product_total = $val->total;
						foreach($order->orderProductOptions as $options){
							if($data['order_product_table_id'] == $options->order_product_id){
								if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height'){
									$params[$options->order_product_id][$options->custom_option_name] = $options->value;
									$responce['html'] .= $options->custom_option_name.'(ft)'.':'.$options->value.'<br/>';
								}else{
									$responce['html'] .= $options->custom_option_name.':'.$options->value.'<br/>';
								}
							}
						}
					}else{
						foreach($order->orderProductOptions as $options){
							if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height'){
								$params[$options->order_product_id][$options->custom_option_name] = $options->value;
							}
						}
					}
					
					foreach($order->orderAddresses as $add){
						if($val->id == $add->order_product_id){
							$params[$val->id]['zipcode'] = $add->shipping_zipcode;
						}
					}
				}
				
				foreach($order->orderProductOptions as $options){
					if($options->custom_option_id != 0 && !empty($options->custom_option_id)){
						$option_detail = CustomOptions::where('id',$options->custom_option_id)->first();
						$option_keys = json_decode($option_detail->option_keys);
						
						$flat_rate_additional_price = 0;
						
						foreach($option_keys as $key=>$value){
							if($value->value == $options->value && isset($value->flat_rate_additional_price) && !empty($value->flat_rate_additional_price)){ 
								$flat_rate_additional_price = $value->flat_rate_additional_price; 
							}
						}
						
						$flat_rate_additional_price = number_format($flat_rate_additional_price,2,'.','');
						
						$params[$options->order_product_id]['shipping_price'] += $flat_rate_additional_price;
					}
				}
				
				//pr($params);die;
				$weight = 0;
				$total_shipping = 0;
				if($order->multiple_shipping == 0){
					foreach($params as $key=>$val){
						$wght = $val['product_weight'];
						if($val['shipping']){
							if($val['shipping_type'] == 'paid' or ($val['shipping_type'] == 'free_value' and $val['total'] < $val['shipping_min_value'])){
								$weight += $wght;
							}
						}
					}
					$weight = number_format($weight,2,'.','');
					$zipcode = $params[$data['order_product_table_id']]['zipcode'];
					if($weight > 0){
						$rate = $this->calculateShipping($zipcode,$weight,$order->shipping_option);
						//pr($rate);die;
						if($rate['status']){
							$rate = number_format($rate['res']->MonetaryValue,2,'.','');
							$one_LBS_ship_amount = $rate/$weight;
							$one_LBS_ship_amount = number_format($one_LBS_ship_amount,2,'.','');
							
							foreach($params as $key=>$val){
								$price = 0.00;
								if($val['shipping_type'] == 'paid' or ($val['shipping_type'] == 'free_value' and $val['total'] < $val['shipping_min_value'])){
									$price = $one_LBS_ship_amount*$val['product_weight'];
									$price = number_format($price,2,'.','');
								}else if($val['shipping_type'] == 'flat'){
									if($val['shipping_reduce_price'] == 1){
										$qty = $val['qty'] - 1;
										$price = $val['shipping_price']+($qty*$val['shipping_additional_qty_price']);
										$price = number_format($price,2,'.','');
									}else{
										$qty = $val['qty'];
										$price = $val['shipping_price']*$qty;
										$price = number_format($price,2,'.','');
									}
								}
								orderProducts::where('id',$key)->update(['product_shipping'=>$price]);
							}
						}
					}else{
						foreach($params as $k1=>$v1){
							$price = 0.00;
							if($v1['shipping_type'] == 'flat'){
								if($v1['shipping_reduce_price'] == 1){
									$qty = $v1['qty'] - 1;
									$price = $v1['shipping_price']+($qty*$v1['shipping_additional_qty_price']);
									$price = number_format($price,2,'.','');
								}else{
									$price = $v1['qty']*(number_format($v1['shipping_price'],2,'.',''));
								}
							}
							orderProducts::where('id',$v1['id'])->update(['product_shipping'=>$price]);
						}
					}
				}else{
					$product_shipping = $params[$data['order_product_table_id']]['shipping'];
					
					$weight = $params[$data['order_product_table_id']]['product_weight'];
					$weight = number_format($weight,2,'.','');
					if($product_shipping){
						$zipcode = $params[$data['order_product_table_id']]['zipcode'];
						$rate = $this->calculateShipping($zipcode,$weight,$order->shipping_option);
						if($rate['status']){
							$product_ship = number_format($rate['res']->MonetaryValue,2,'.','');
							
							orderProducts::where('order_id',$data['order_id'])->where('id',$data['order_product_table_id'])->update(['product_weight'=>$weight,'product_shipping'=>$product_ship]);
						}
					}else{
						$price = 0;
						
						if($params[$data['order_product_table_id']]['shipping_type'] == 'flat'){
							if($params[$data['order_product_table_id']]['shipping_reduce_price'] == 1){
								$qty = $params[$data['order_product_table_id']]['qty'] - 1;
								$price = $params[$data['order_product_table_id']]['shipping_price']+($qty*$params[$data['order_product_table_id']]['shipping_additional_qty_price']);
								$price = number_format($price,2,'.','');
							}else{
								$price = $params[$data['order_product_table_id']]['qty']*(number_format($params[$data['order_product_table_id']]['shipping_price'],2,'.',''));
							}
						}
						
						orderProducts::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->update(['product_weight'=>$weight,'product_shipping'=>$price]);
					}
				}
				
				$products = orderProducts::where('order_id',$data['order_id'])->get();
				foreach($products as $key=>$val){
					$total_shipping += $val->product_shipping;
				}
				
				$main_total = number_format($main_total,2,'.','');
				$order_total = ($main_total - $order->discount) + $total_shipping + $order->sales_tax;
				
				$order = Orders::findOrFail($data['order_id']);
				$order->sub_total = $main_total;								
				$order->shipping_fee = $total_shipping;
				$order->total = number_format($order_total,2,'.','');
				$order->save();				
			}

			if($data['get'] == 'form_save'){
				$order = Orders::where('id', $data['order_id'])->first();
				$responce['status'] = 'success';
				$responce['type'] = $data['type'];				
				$responce['class'] = $data['order_item'];				
				$responce['discount'] = number_format($order->discount,2,'.','');
				$responce['shipping_fee'] = number_format($order->shipping_fee,2,'.','');
				$responce['sub_total'] = number_format($order->sub_total,2,'.','');
				$responce['main_total'] = number_format($order->total,2,'.','');

				$products = OrderProducts::where('order_id',$data['order_id'])->get();				
				foreach($products as $key=>$val){
					if($val->id == $data['order_product_table_id'])
					{						
						$responce['qty'] = $val->qty;
						$responce['default_price'] = $val->price_default;						
						$responce['gross_price'] = $val->price;
						$responce['gross_total'] = $val->gross_total;
						$responce['qty_discount'] = $val->qty_discount;
						$responce['product_total'] = $val->total;						
					}
				}				
			}				
		}
		return json_encode($responce);
	}

	public function order_values_edit_old(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		$responce['class'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			if($data['get'] == 'form'){
				if($data['type'] == 'qty'){
					$order_product = OrderProducts::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->where('item_id',$data['order_item'])->first();
					
					$responce['html'] = Form::model('edit_order_values',['id'=>'edit_order_values']).'<div class="col-xs-12"><div class="col-md-6 form-group">';
					$responce['html'] .= Form::hidden('type',$data['type'],['id'=>'type']);
					$responce['html'] .= Form::hidden('order_product_table_id',$order_product->id,['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_item',$data['order_item'],['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_id',$data['order_id'],['id'=>'order_id']);
					$responce['html'] .= Form::hidden('order_product_id',$data['order_product_id'],['id'=>'order_product_id']);
					
					$responce['html'] .= Form::label('quantity', 'Quantity',array('class'=>'form-control-label'));
					$responce['html'] .= Form::number('quantity', $data['order_value'],array('class'=>'form-control','id'=>'quantity','type'=>'number'));
					
					$responce['html'] .= '</div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_order_product_values">Update</button><img id="edit_product_values_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div></div>';
					
					$responce['status'] = 'success';
				}
				if($data['type'] == 'rate'){
					$order_product = OrderProducts::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->where('item_id',$data['order_item'])->first();
					$responce['html'] = Form::model('edit_order_values',['id'=>'edit_order_values']).'<div class="col-xs-12"><div class="col-md-6 form-group">';
					$responce['html'] .= Form::hidden('type',$data['type'],['id'=>'type']);
					$responce['html'] .= Form::hidden('order_product_table_id',$order_product->id,['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_item',$data['order_item'],['id'=>'order_item']);
					$responce['html'] .= Form::hidden('order_id',$data['order_id'],['id'=>'order_id']);
					$responce['html'] .= Form::hidden('order_product_id',$data['order_product_id'],['id'=>'order_product_id']);
					$responce['html'] .= Form::label('price_default', 'Rate',array('class'=>'form-control-label'));
					$responce['html'] .= '<div class="input-group"><span class="input-group-addon">$</span>';
					$responce['html'] .= Form::number('price_default', $data['order_value'],array('class'=>'form-control','id'=>'price_default','type'=>'number'));
					$responce['html'] .= '</div></div></div><div class="clearfix"></div><div class="col-xs-12"><div class="col-md-6 form-group"><button type="button" class="btn btn-success edit_order_product_values">Update</button><img id="edit_product_values_loader_img" class="loader_img" src="'.url('public/img/loader/Spinner.gif').'"></div></div>';
					
					$responce['status'] = 'success';
				}
			}
			if($data['get'] == 'form_save'){
				parse_str($data['data'], $data);
				
				$product_total = 0;
				$main_total = 0;
				//pr($data);
				//die;
				
				$detail = OrderProducts::where('order_id', $data['order_id'])->get();
				$old_qty = 0;
				foreach($detail as $val){
					if($val->id == $data['order_product_table_id']){
						$old_qty = $val->qty;
						$width = 1;
						$height = 1;
						
						// Below if else condition for set price default and Qty according form //
						
						if($data['type'] == 'rate'){
							$variant_price = number_format($data['price_default'],2,'.','');
							$qty = $val->qty;
						}else{
							$variant_price = number_format($val->price_default,2,'.','');
							$qty = $data['quantity'];
						}
						
						// Below code for get all discounts or set discount according to qty //
						
						$discount_all = Discounts::where('status',1)->orderBy('quantity','ASC')->get();
						$discount = array();
						foreach($discount_all as $value){
							if(empty($value->products)){
								$discount[$value->quantity] = $value->percent;
							}else{
								$ids = explode(',',$value->products);
								if(in_array($val->product_id,$ids)){
									$discount[$value->quantity] = $value->percent;
								}
							}
						}
						$discount_per = 0;
						foreach($discount as $k=>$d){
							if($k <= $qty)
								$discount_per = number_format($d,2,'.','');
						}
						
						// below code for update option values according to change qty and default price // 
						
						$options_array = array();
						
						$options = OrderProductOptions::where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_table_id'])->get();
						
						foreach($options as $value){
							if($value->type == 3){
								if($value->custom_option_name == 'Width'){	$width = $value->value; }
								if($value->custom_option_name == 'Height'){	$height = $value->value; }
							}
							if($value->type == 2 and $value->price != '' and $data['type'] != 'rate'){
								$variant_price = $value->price;
							}
							
							$options_array[$value->type][$value->custom_option_name]['value'] = $value->value;
							$options_array[$value->type][$value->custom_option_name]['price'] = $value->price;
						}
					
						$variant_price = number_format($variant_price,2,'.','');
						
						$gross_price = $variant_price * ($width * $height);
						
						foreach($options_array[1] as $key=>$value){
							$option_detail = CustomOptions::where('name',$key)->where('status',1)->first();
							if($option_detail->price_formate == 'area'){
								$gross_price = $gross_price + ( ($width * $height) * $value['price'] );
							}else if($option_detail->price_formate == 'item'){
								$gross_price = ($gross_price + $value['price']);
							}else if($option_detail->price_formate == 'parimeter'){
								$gross_price = $gross_price + ( (($width + $height) * 2 ) * $value['price'] );
							}else if($option_detail->price_formate == 'gross'){
								$gross_price = $gross_price + ( $value['price'] ) ;
							}
						}
						
						$gross_total = number_format(($gross_price*$qty),2,'.','');
						
						$qty_discount = ($gross_total *  $discount_per)/100;
						
						$total =  $gross_total - $qty_discount;
						$total =  number_format($total,2,'.','');
							
						if(array_key_exists(3,$options_array)){
							$width_price =  ($options_array[3]['Width']['value']*$options_array[3]['Height']['value']*$variant_price);
							
							OrderProductOptions::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->where('custom_option_name','Width')->update(['price'=>$width_price]);
						}
						
						// Below code for calculate product weight according new QTY //
						
						$new_weight = $val->product_weight/$old_qty;
						$wght = $qty*$new_weight;
						$wght = number_format($wght,2,'.','');
						
						// Below code for update order products price //
						
						orderProducts::where('order_id',$data['order_id'])->where('id',$data['order_product_table_id'])->update(['price_default'=>$variant_price,'price'=>$gross_price,'gross_total'=>$gross_total,'qty'=>$qty,'qty_discount'=>$qty_discount,'total'=>$total,'product_weight'=>$wght]);
						
						$main_total += $total;
					}else{
						$main_total += $val->total;
					}
				}
				
				$db = Orders::where('id', $data['order_id'])->with(['orderProduct','orderProductOptions','orderAddresses']);
				$order = $db->first();
				
				$order_discount = $order->discount;
				$sales_tax = $order->sales_tax;
				
				/* Shipping Calculations */
				$params = array();
				foreach($order->orderProduct as $val){
					$params[$val->id]['id'] = $val->id;
					$params[$val->id]['product_id'] = $val->product_id;
					$params[$val->id]['qty'] = $val->qty;
					$params[$val->id]['product_weight'] = $val->product_weight;
					$params[$val->id]['total'] = $val->total;
				
					if(count($val->shipping) > 0 and ($val->shipping->type == 'paid' or ($val->shipping->type == 'free_value' and $val->total < $val->shipping->min_value))){
						$params[$val->id]['shipping'] = true;
					}else{
						$params[$val->id]['shipping'] = false;
					}
					
					$params[$val->id]['shipping_type'] = $val->shipping->type;
					$params[$val->id]['shipping_price'] = $val->shipping->price;
					
					$product = Products::where('id',$data['order_product_id'])->with('variants')->first();
					$variant_options = array();
					foreach($product->variants as $variant){
						foreach($variant->variantValues as $value){
							$check = OrderProductOptions::where('order_id',$data['order_id'])->where('order_product_id',$data['order_product_table_id'])->where('custom_option_name',$variant->name)->first();
							if($check->value == $value->value){
								$variant_options[] = $value->id;
							}
						}
					}
										
					$db = ProductVariantPrice::select('shipping_weight','shipping_price')->where('product_id',$data['order_product_id']);
					$j = 1;
					foreach($variant_options as $ids){
						$db->where('varient_id'.$j,$ids);
						$j++;
					}
					$variant_weight = $db->first();
					
					if(!empty($variant_weight->shipping_price) and $val->shipping->type == 'flat'){
						$additional_shipping = number_format($variant_weight->shipping_price,2,'.','');
						$params[$val->id]['shipping_price'] += $additional_shipping;
					}
					
					$params[$val->id]['shipping_min_value'] = $val->shipping->min_value;
					$params[$val->id]['shipping_reduce_price'] = $val->shipping->reduce_price;
					$params[$val->id]['shipping_additional_qty_price'] = $val->shipping->additional_qty_price;
					
					if($val->id == $data['order_product_table_id']){
						$responce['html'] .= '<strong>'.$val->product->name.'.</strong><div class="col-xs-12">';
						$product_total = $val->total;
						foreach($order->orderProductOptions as $options){
							if($data['order_product_table_id'] == $options->order_product_id){
								if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height'){
									$params[$options->order_product_id][$options->custom_option_name] = $options->value;
									$responce['html'] .= $options->custom_option_name.'(ft)'.':'.$options->value.'<br/>';
								}else{
									$responce['html'] .= $options->custom_option_name.':'.$options->value.'<br/>';
								}
							}
						}
					}else{
						foreach($order->orderProductOptions as $options){
							if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height'){
								$params[$options->order_product_id][$options->custom_option_name] = $options->value;
							}
						}
					}
					
					foreach($order->orderAddresses as $add){
						if($val->id == $add->order_product_id){
							$params[$val->id]['zipcode'] = $add->shipping_zipcode;
						}
					}
				}
				//pr($params);die;
				$weight = 0;
				$total_shipping = 0;
				if($order->multiple_shipping == 0){
					foreach($params as $key=>$val){
						$wght = $val['product_weight'];
						if($val['shipping']){
							if($val['shipping_type'] == 'paid' or ($val['shipping_type'] == 'free_value' and $val['total'] < $val['shipping_min_value'])){
								$weight += $wght;
							}
						}
					}
					$weight = number_format($weight,2,'.','');
					$zipcode = $params[$data['order_product_table_id']]['zipcode'];
					if($weight > 0){
						$rate = $this->calculateShipping($zipcode,$weight,$order->shipping_option);
						//pr($rate);die;
						if($rate['status']){
							$rate = number_format($rate['res']->MonetaryValue,2,'.','');
							$one_LBS_ship_amount = $rate/$weight;
							$one_LBS_ship_amount = number_format($one_LBS_ship_amount,2,'.','');
							
							foreach($params as $key=>$val){
								$price = 0.00;
								if($val['shipping_type'] == 'paid' or ($val['shipping_type'] == 'free_value' and $val['total'] < $val['shipping_min_value'])){
									$price = $one_LBS_ship_amount*$val['product_weight'];
									$price = number_format($price,2,'.','');
								}else if($val['shipping_type'] == 'flat'){
									if($val['shipping_reduce_price'] == 1){
										$qty = $val['qty'] - 1;
										$price = $val['shipping_price']+($qty*$val['shipping_additional_qty_price']);
										$price = number_format($price,2,'.','');
									}else{
										$qty = $val['qty'];
										$price = $val['shipping_price']*$qty;
										$price = number_format($price,2,'.','');
									}
								}
								orderProducts::where('id',$key)->update(['product_shipping'=>$price]);
							}
						}
					}else{
						foreach($params as $k1=>$v1){
							$price = 0.00;
							if($v1['shipping_type'] == 'flat'){
								if($v1['shipping_reduce_price'] == 1){
									$qty = $v1['qty'] - 1;
									$price = $v1['shipping_price']+($qty*$v1['shipping_additional_qty_price']);
									$price = number_format($price,2,'.','');
								}else{
									$price = $v1['qty']*(number_format($v1['shipping_price'],2,'.',''));
								}
							}
							orderProducts::where('id',$v1['id'])->update(['product_shipping'=>$price]);
						}
					}
				}else{
					$product_shipping = $params[$data['order_product_table_id']]['shipping'];
					
					$weight = $params[$data['order_product_table_id']]['product_weight'];
					$weight = number_format($weight,2,'.','');
					if($product_shipping){
						$zipcode = $params[$data['order_product_table_id']]['zipcode'];
						$rate = $this->calculateShipping($zipcode,$weight,$order->shipping_option);
						if($rate['status']){
							$product_ship = number_format($rate['res']->MonetaryValue,2,'.','');
							
							orderProducts::where('order_id',$data['order_id'])->where('id',$data['order_product_table_id'])->update(['product_weight'=>$weight,'product_shipping'=>$product_ship]);
						}
					}else{
						$price = 0;
						
						if($params[$data['order_product_table_id']]['shipping_type'] == 'flat'){
							if($params[$data['order_product_table_id']]['shipping_reduce_price'] == 1){
								$qty = $params[$data['order_product_table_id']]['qty'] - 1;
								$price = $params[$data['order_product_table_id']]['shipping_price']+($qty*$params[$data['order_product_table_id']]['shipping_additional_qty_price']);
								$price = number_format($price,2,'.','');
							}else{
								$price = $params[$data['order_product_table_id']]['qty']*(number_format($$params[$data['order_product_table_id']]['shipping_price'],2,'.',''));
							}
						}
						
						orderProducts::where('order_id',$data['order_id'])->where('product_id',$data['order_product_id'])->update(['product_weight'=>$weight,'product_shipping'=>$price]);
					}
				}
				
				$products = orderProducts::where('order_id',$data['order_id'])->get();
				foreach($products as $key=>$val){
					$total_shipping += $val->product_shipping;
				}
				
				$main_total = number_format($main_total,2,'.','');
				$order_total = ($main_total-$order_discount)+$total_shipping+$sales_tax;
				
				$order = Orders::findOrFail($data['order_id']);
				$order->sub_total = $main_total;
				$order->discount = $order_discount;
				$order->sales_tax = $sales_tax;
				$order->shipping_fee = $total_shipping;
				$order->total = number_format($order_total,2,'.','');
				$order->save();
				
				$responce['status'] = 'success';
				$responce['type'] = $data['type'];
				$responce['qty'] = $params[$data['order_product_table_id']]['qty'];
				$responce['default_price'] = $variant_price;
				$responce['class'] = $data['order_item'];
				$responce['product_total'] = number_format($total,2,'.','');
				$responce['gross_price'] = number_format($gross_price,2,'.','');
				$responce['gross_total'] = number_format($gross_total,2,'.','');
				$responce['qty_discount'] = number_format($qty_discount,2,'.','');
				
				$responce['discount'] = number_format($order->discount,2,'.','');
				$responce['shipping_fee'] = number_format($order->shipping_fee,2,'.','');
				$responce['sub_total'] = number_format($order->sub_total,2,'.','');
				$responce['main_total'] = number_format($order->total,2,'.','');
			}
		}
		return json_encode($responce);
	}

	public function order_save(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			$orderDetail = Orders::findOrFail($data['order_id']);
			//pr($orderDetail);die;
			
			$orderDetail->agent_id = $data['agent'];
			$orderDetail->terms = $data['terms'];
			$orderDetail->new_terms = $data['new_terms'];
			$orderDetail->customer_po = $data['customer_po'];
			$orderDetail->shipping_fee = $data['shipping_amount'];
			
			$order_total = ($orderDetail->sub_total - $orderDetail->discount) + $data['shipping_amount'] + $orderDetail->sales_tax;
			
			$orderDetail->total = $order_total;
			
			$orderDetail->save();
			
			//Orders::where('id',$data['order_id'])->update(['agent_id'=>$data['agent'],'terms'=>$data['terms'],'new_terms'=>$data['new_terms'],'customer_po'=>$data['customer_po']]);
			$responce['status'] = 'success';			
		}
		return json_encode($responce);
	}
	
	public function order_changes(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			if($data['type'] == 'terms'){
				$responce['status'] = 'success';
				if($data['terms'] == 3){
					Orders::where('id',$data['order_id'])->update(['terms'=>$data['terms'],'new_terms'=>$data['new_terms']]);
				}else{
					Orders::where('id',$data['order_id'])->update(['terms'=>$data['terms'],'new_terms'=>'']);
				}
			}
			if($data['type'] == 'agent'){
				$responce['status'] = 'success';
				Orders::where('id',$data['order_id'])->update(['agent_id'=>$data['agent']]);
			}
			if($data['type'] == 'customer_po'){
				$responce['status'] = 'success';
				Orders::where('id',$data['order_id'])->update(['customer_po'=>$data['customer_po']]);
			}
			if($data['type'] == 'po'){
				OrderProducts::where('order_id',$data['order_id'])->where('po_id',$data['old_po'])->update(['po_id'=>'PO-'.$data['new_po']]);
				$responce['status'] = 'success';
				$responce['html'] = url('admin/order/edit/'.$data['order_id'].'/PO-'.$data['new_po'].'?msg=PO number changed successfully');
			}
		}
		return json_encode($responce);
	}
	
	public function savecomment(Request $request){
		$responce['status'] = '';
		$responce['res'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$update = OrderProducts::where('id',$data['id'])->update([$data['field']=>$data['field_value']]);
			if($update){
				$responce['status']  = 'success';
				$responce['id'] = $data['id'];
				$responce['res'] = $data['field_value'];
				$responce['field'] = $data['field'];
				$responce['data_btn'] = $data['data_btn'];
				$responce['key_id'] = $data['key_id'];
			}
		}
		return json_encode($responce);
	}
	
	public function bookShipping(Request $request){
		$res['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);
			$order = Orders::where('id',$data['order_id'])->with('orderAddresses','customer')->first();
			//pr($order->orderAddresses);die;
			//pr(config('constants.ShipperDetail.name'));
			$address = array();
			if(count($order->orderAddresses) > 0){
				foreach($order->orderAddresses as $value){
					if($value->product_id == $data['product_id']){
						$address['name'] = $order->customer->fname.' '.$order->customer->lname;
						$address['email'] = $order->customer->email;
						$address['phone_number'] = $order->customer->phone_number;
						$address['company_name'] = $order->customer->company_name;
						$address['add1'] = $value->shipping_add1;
						$address['add2'] = $value->shipping_add2;
						$address['zipcode'] = $value->shipping_zipcode;
						$address['city'] = $value->shipping_city;
						$address['state'] = $value->shipping_state;
						$address['country'] = $value->shipping_country;
					}
				}
			}
			//pr($address);die;
			$weight = 0;
			$params = array();
			if($order->multiple_shipping == 0 or $order->multi_shipping == '0'){
				$products = DB::table('order_products as product')->where('product.order_id',$data['order_id']);
				$detail = $products->leftJoin('order_product_options as options',function($join){
									$join->on('options.order_id', '=', 'product.order_id');
									$join->on('options.product_id', '=', 'product.product_id');
								})->get();
				
				foreach($detail->toArray() as $val){
					$ship = ProductShipping::where('product_id',$val->product_id)->first();
					if(count($ship) > 0){
						if($val->custom_option_name == 'Width'){
							$params[$val->product_id]['width'] = $val->value;
						}
						if($val->custom_option_name == 'Height'){
							$params[$val->product_id]['height'] = $val->value;
						}
						$params[$val->product_id]['quantity'] = $val->qty;
						$params[$val->product_id]['type'] = $ship->type;
						$params[$val->product_id]['min_value'] = $ship->min_value;
						$params[$val->product_id]['weight'] = $ship->weight;
					}
				}
			}else{
				$product = DB::table('order_products as product')->where('product.order_id',$data['order_id'])->where('product.product_id',$data['product_id']);
				$detail= $product->leftJoin('order_product_options as options',function($join){
									$join->on('options.order_id', '=', 'product.order_id');
									$join->on('options.product_id', '=', 'product.product_id');
								})->get();
				foreach($detail->toArray() as $val){
					$ship = ProductShipping::where('product_id',$val->product_id)->first();
					if(count($ship) > 0){
						if($val->custom_option_name == 'Width'){
							$params[$val->product_id]['width'] = $val->value;
						}
						if($val->custom_option_name == 'Height'){
							$params[$val->product_id]['height'] = $val->value;
						}
						$params[$val->product_id]['quantity'] = $val->qty;
						$params[$val->product_id]['type'] = $ship->type;
						$params[$val->product_id]['min_value'] = $ship->min_value;
						$params[$val->product_id]['weight'] = $ship->weight;
					}
				}
			}
			
			foreach($params as $key=>$val){
				$sqft = 1;
				if(array_key_exists('weight',$val) and array_key_exists('height',$val)){
					$sqft = $val['width'] * $val['height'];
				}
				$weight += $sqft*$val['quantity']*$val['weight'];
			}
			/* echo $weight."<br/>";
			pr($params);
			die; */
			if(empty($params)){
				$weight = 1;
			}
			
			if(!empty($address)){
				try{
					// call the upshelper for book shipping
					$shipping = new UPSShipping();
					$ship = $shipping->shipping($address,$data['order_id'],$data['item_id'],$weight,$order->shipping_option);
					//pr($ship);
					if($ship['status'] == 'success'){
						if($order->multiple_shipping == 0 or $order->multiple_shipping == '0'){
							OrderProducts::where('order_id',$data['order_id'])->update(['tracking_id'=>$ship['tracking_number'],'label_image'=>$ship['image_name']]);
							$res['multiple'] = false;
						}else{
							OrderProducts::where('order_id',$data['order_id'])->where('product_id',$data['product_id'])->where('item_id',$data['item_id'])->update(['tracking_id'=>$ship['tracking_number'],'label_image'=>$ship['image_name']]);
							$res['multiple'] = true;
						}
						$res['status'] = 'success';
						$res['tracking_number'] = $ship['tracking_number'];
						$res['shipping_option'] = config('constants.Shipping_option.'.$order->shipping_option);
						$res['image_name'] = $ship['image_name'];
					}
					else{
						$res['msg'] = $ship['msg'];
					}
				} catch (Exception $e) {
					
				}
			}else{
				$res['msg'] = "Unable to get shipping address of order, please try again.";
			}
		}
		return json_encode($res);
	}

	public function tracking($track_id){
		$pageTitle = 'Order Tracking';
		$tracking = new Ups\Tracking(config('constants.Ups_accessKey'), config('constants.Ups_userId'), config('constants.Ups_password'), config('constants.Ups_SandBox'));
		$order_product = OrderProducts::where('tracking_id',$track_id)->first();
		$track = array();
		$errormessage="";
		$i = 1;		
		try{
			$shipment = $tracking->track($track_id);
			//pr($shipment->Package->Activity);die;
			foreach($shipment->Package->Activity as $activity) {
				$data = (array)$activity;
				if(!empty((array) $data['ActivityLocation']) > 0){
					if(isset($data['ActivityLocation']->Address)){
						$address = (array) $data['ActivityLocation']->Address;
						if(count($address) > 0){
							if(array_key_exists('City',$address))
							$track[$i]['address']['City'] = $address['City'];
							if(array_key_exists('StateProvinceCode',$address))
							$track[$i]['address']['StateProvinceCode'] = $address['StateProvinceCode'];
							if(array_key_exists('CountryCode',$address))
							$track[$i]['address']['CountryCode'] = $address['CountryCode'];
						}else{
							$track[$i]['address'] = '';
						}
					}
				}else{
					$track[$i]['address'] = '';
				}
				
				$track[$i]['date'] = date('d/m/Y',strtotime($data['Date']));
				$track[$i]['time'] = date('H:i',strtotime($data['Time']));
				$track[$i]['description'] = $data['Status']->StatusType->Description;
				$i++; 
			}			
			//pr($track);die;			
			return view('Admin/orders/tracking',compact('pageTitle','track','order_product'));
		} catch (Exception $e) {
			//pr($e);die;
			$errormessage = $e->getMessage();
			\Session::flash('error', $errormessage);
			return view('Admin/orders/tracking',compact('pageTitle','track','order_product'));			
		}
	}
	
	public function change_status($id,$status,$action=null){
		$url = '/admin/order/lists';
		if($action != null){
			$url = '/admin/'.$action;
		}
		
		$update = Orders::where('id',$id)->update(['status'=>$status]);
		if($update){
			\Session::flash('success', 'Order updated successfully.');
			return \Redirect::to($url);
		}
		else{
			\Session::flash('error', 'Order not updated successfully,please try again.');
			return \Redirect::to($url);
		}
	}
	
	public function orderMail(Request $request,$id){
		$order = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress'])->first();
		//pr($order);die();

		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;			
			\Mail::send([], [], function ($message) use ($data) {
				$message->from($data['from'],config('constants.SITE_NAME'));
				$message->to($data['to'])->subject($data['subject']);
				$message->attach("public/pdf/front/order_receipt/".$data['file_name'], [
						'as' => str_replace(" ", "_", $data['file_name']),
						'mime' => 'application/pdf',
					]);
				$message->setBody($data['message'], 'text/html');
			});
			$responce['status'] = true;

			return $responce;
		}

		$url = url("invoice_receipt/".$id);
		
		$file_name = 'EasyOrderBanners_Order_'.$id.'_Receipt.pdf';

		if($order->customer_status == 0)
		{
			$file_name = 'EasyOrderBanners_Estimate_'.$id.'.pdf';
		}

		$file_path = "public/pdf/front/order_receipt/".$file_name;
				
		if($_SERVER['SERVER_NAME']=='192.168.1.77')
		{
			$output		=	exec("phantomjs pages.js  $url $file_path 2>&1"); 
		}
		else
		{
			$exe = "/home/octallabs/public_html/56/admin/phantomjs/bin/phantomjs";
			$output = exec("$exe --ssl-protocol=any --ignore-ssl-errors=yes pages.js  $url $file_path 2>&1");
		}
		$pdf = $file_name;

		return view('Admin/orders/order_mail',compact('id','order','pdf'));
	}

	public function test(){
		//pr(new Exception);die;
		$shipping = new UPSShipping();
		$ship = $shipping->test();
	}

	public function invoice_email(){
		$order_details = Orders::with(['customer','orderProduct','orderProductOptions','orderAddress'])->findOrFail(38);
		//pr($order_details);
		$order_total = 0;
		$str = '<table border="1" cellspacing="2" cellpadding="2"><tr><td colspan="4"><img src="http://192.168.1.77/easyorderbanner/public/img/front/logo.png"/> <span style="float:right;"><b>Order Id: </b>#'.$order_details->id.'</span></td></tr>';
		$str .= '<tr><th>Product Name</th><th>Description</th><th>Quantity</th><th>Total</th></tr>';
		foreach($order_details->orderProduct as $product){
			$str .= '<tr><td><strong>'.$product->product->name.'</strong></td>';
			$str .= '<td><ul>';
			foreach($order_details->orderProductOptions as $options){
				if($product->product_id == $options->product_id){
					if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height'){
						$str .= '<li>'.$options->custom_option_name.'(ft)'.':'.$options->value.'<br/></li>';
					}else{
						$str .= '<li>'.$options->custom_option_name.':'.$options->value.'<br/></li>';
					}	
				}
			}
			$str .= '</ul></td><td>'.$product->qty.'</td>';
			$str .= '<td>$'.$product->total.'</td></tr>';
			$order_total += $product->total; 
		}
		if($order_details->discount > 0){
			$str .= '<tr><td colspan="2"></td><th>Discount : </th><td>$'.$order_details->discount.'</td></tr>';
		}
		if($order_details->shipping_fee > 0){
			$str .= '<tr><td colspan="2"></td><th>Shipping : </th><td>$'.$order_details->shipping_fee.'</td></tr>';
		}
		if($order_details->sales_tax > 0){
			$str .= '<tr><td colspan="2"></td><th>Sales Tax : </th><td>$'.$order_details->sales_tax.'</td></tr>';
		}
		$str .= '<tr><td colspan="2"></td><th>Total : </th><td>$'.$order_details->total.'</td></tr></table>';
		
		$file_name = 'order_18.pdf';
		$file_path = "public/pdf/".$file_name;
		
		$params = array(
						'slug'=>'order_receipt',
						//'to'=>'testsoon@mailinator.com',
						'to'=>'jitendra.dariwal@octalinfosolution.com',
						'pdf'=>$file_path,
						'params'=>array(
									'{{name}}'=>$order_details->customer->fname.'-'.$order_details->customer->lname,
									'{{site_name}}'=>config('constants.SITE_NAME'),
									'{{details_rows}}'=>$str,
									)
						);
		parent::sendMail($params);
		
	}
}
