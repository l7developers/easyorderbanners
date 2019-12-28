@extends('layouts.app')
@section('content')
<script src="https://www.paypalobjects.com/api/checkout.js"></script>

<section class="product_cart">
	<div class="container">
		<div class="product_cart_title">
			If you have any questions about checkout or the order process, <br> please contact us at {{config('constants.site_phone_number')}}. We’re here to help!
		</div>
		<div class="cart_tab">
			<ul>
				<li class="active"> <a href="{{url('cart')}}"><span>1</span> Review Cart</a></li>
				<li class="active"> <a href="{{url('checkout')}}"><span>2</span> Billing, shipping & Payment</a></li>
				<li class=""> <a href="javascript:void(0)"><span>3</span> Art Uploads</a></li>
			</ul>
		</div>
		@php
			$user_addresses = \App\UserAddress::where('user_id',\Auth::user()->id)->where('status',1)->get();
		@endphp
		<div class="row">
			<div class="col-sm-7">					
				<div class="billingdetails">
				{{Form::model('checkout',['id'=>'checkout'])}}
					<h4 class="billingHeading">Billing Details</h4>
					<div class="form-group col-sm-12 billing_error has-error hide"></div>
					<div class="clearfix"></div>
					@php
						if(count($user_addresses) > 0){
							foreach($user_addresses as $user_address){
								if($user_address->type == 1){
					@endphp
					<div class="form-group col-sm-6">
						<div class="radio">
							<label>
								@php
									$checked = 'checked';
									if(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == $user_address->id){
										$checked = 'checked';
									}else{
										if(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0){
											$checked = '';
										}
									}
								@endphp
								<input type="radio" name="billing_type" value="{{$user_address->id}}" {{$checked}} /> 
								<address class="billing_address_box">
									@if(!empty($user_address->company_name))
										{{$user_address->company_name}}<br/>
									@endif	
									
									@if(!empty($user_address->fname) && !empty($user_address->lname))
										{{$user_address->fname.' '.$user_address->lname}}<br/>
									@endif

									@if(!empty($user_address->add1) and !empty($user_address->add2))
										{{$user_address->add1}}<br/>{{$user_address->add2}}<br/>
									@else
										{{$user_address->add1}}<br/>
									@endif

									{{$user_address->city.', '.$user_address->state.' '.$user_address->zipcode . ' ' . $user_address->country}}
								</address>
							</label>
						</div>
					</div>
					@php
								}
							}
						}
					@endphp
					<div class="form-group col-sm-6">
						<div class="radio">
							<label><input type="radio" name="billing_type" value="0" {{ (\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?'checked':'' }}> New Address</label>
						</div>
					</div>
					<div class="col-sm-12 new_billing_div" style="{{ (\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?'checked':'display:none;' }}">
						<div class="form-group col-sm-6">{{Form::label('billing_company_name','Company Name')}}		{{Form::text('billing_company_name',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_company_name']:'',['class'=>'form-control opt','placeholder'=>'Company Name'])}}
						</div>
						<div class="form-group col-sm-6">{{Form::label('billing_phone_number','Phone Number')}}		{{Form::text('billing_phone_number',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_phone_number']:'',['class'=>'form-control  opt','placeholder'=>'Phone Number'])}}
						</div>
						<div class="form-group col-sm-6">{{Form::label('billing_fname','First Name')}}		{{Form::text('billing_fname',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_fname']:'',['class'=>'form-control','placeholder'=>'First Name'])}}
						</div>
						<div class="form-group col-sm-6">{{Form::label('billing_lname','Last Name')}}		{{Form::text('billing_lname',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_lname']:'',['class'=>'form-control','placeholder'=>'Last Name'])}}
						</div>
						<div class="form-group col-sm-12">
							{{Form::label('','Street address')}} {{Form::text('billing_address1',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_address1']:'',['class'=>'form-control','placeholder'=>'Street address 1'])}}
						</div>
						<div class="form-group col-sm-12">				{{Form::text('billing_address2',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_address2']:'',['class'=>'form-control street2  opt','placeholder'=>'Street address 2','data'=>'add2'])}}
						</div>
						<div class="form-group col-sm-6">{{Form::label('billing_zipcode','Zipcode')}}		{{Form::number('billing_zipcode',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_zipcode']:'',['class'=>'form-control','placeholder'=>'Zipcode'])}}
						</div>
						<div class="form-group col-sm-6">                               {{Form::label('billing_city','Town / City')}} {{Form::text('billing_city',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_city']:'',['class'=>'form-control','placeholder'=>'City'])}}
						</div>
						<div class="form-group col-sm-6">                                {{Form::label('billing_state','State')}} 
						{{Form::select('billing_state',$states,(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_state']:'',['class'=>'form-control','placeholder'=>'State'])}}
						</div>
						<div class="form-group col-sm-6">								{{Form::label('billing_country','Country')}} {{Form::text('billing_country',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['billing_country']:'US',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
						</div>
					</div>
					<div class="clearfix"></div>
					
					<h4 class="shippingHeading">Shipping Details</h4>
					
					<div class="form-group col-sm-12 shipping_error has-error hide"></div>
					
					<div class="clearfix"></div>
					
					<div class="form-group col-sm-8 selectAddressDiv">
						{{Form::select('selectAddress',$userAddress,(\session()->has('selectAddress'))?\session()->get('selectAddress'):'',['class'=>'form-control','placeholder'=>'Select Address','data-type'=>''])}}						
						<div class="clearfix"></div>
						<div class="select_shipping" style="display:none;"></div><br/>
					</div><br/>
					
					<div class="clearfix"></div>
					
					<input type="hidden" name="same_as_billing" value="0"/>
					
					<div class="col-sm-12 shipping_type_div">
						<?php //var_dump(\session()->get('orderAddresses')['shipping_type']); ?> 
						<div class="col-sm-6 col-xs-12 radio shipping_type_1 no-padding">
							<label>
								<input type="radio" name="shipping_type" value="1" {{(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 1)?'checked':''}}> Same as Billing Address
							</label>
							<div class="clearfix"></div>
							<div class="same_as_billing_shipping" style="display:none;"></div><br/>
						</div>
						<div class="col-sm-6 col-xs-12 radio shipping_type_0 no-padding">
							<label>
								<input type="radio" name="shipping_type" value="0" {{(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 && \session()->get('selectAddress') == '')?'checked':''}}> New Shipping Address
							</label>
						</div>
						<div class="clearfix"></div>
						<div class="col-xs-12 radio shipping_type_multiple hide" >
							<label>
								<input type="radio" name="shipping_type" value="multiple" {{(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 'multiple')?'checked':''}}> Split Shipping to Multiple Addresses
							</label>
						</div>
					</div>
					
					<div class="clearfix"></div>
					
					@php
						$display = 'display:none';
						if(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == '0'){
							$display = 'display:block';
						}
					@endphp
					<div class="col-sm-12 new_shipping_div no-padding" style="{{$display}}">
						<div class="form-group col-sm-6">{{Form::label('shipping_company_name','Company Name')}}		{{Form::text('shipping_company_name',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['shipping_company_name']:'',['class'=>'form-control opt','placeholder'=>'Company Name'])}}
						</div>
						<div class="form-group col-sm-6">{{Form::label('shipping_phone_number','Phone Number')}}		{{Form::text('shipping_phone_number',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['billing_type'] == 0)?\session()->get('orderAddresses')['shipping_phone_number']:'',['class'=>'form-control  opt','placeholder'=>'Phone Number'])}}
						</div>
						<div class="form-group col-sm-6">{{Form::label('','First Name')}}		{{Form::text('shipping_fname',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 and \session()->get('orderAddresses')['shipping_type'] != 'multiple')?\session()->get('orderAddresses')['shipping_fname']:'',['class'=>'form-control','placeholder'=>'First Name'])}}
						</div>
						<div class="form-group col-sm-6">{{Form::label('','Last Name')}}		{{Form::text('shipping_lname',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 and \session()->get('orderAddresses')['shipping_type'] != 'multiple')?\session()->get('orderAddresses')['shipping_lname']:'',['class'=>'form-control','placeholder'=>'Last Name'])}}
						</div>
						<div class="form-group col-sm-12">
							{{Form::label('','Street address')}} {{Form::text('shipping_address1',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 and \session()->get('orderAddresses')['shipping_type'] != 'multiple')?\session()->get('orderAddresses')['shipping_address1']:'',['class'=>'form-control','placeholder'=>'Street address 1'])}}
						</div>
						<div class="form-group col-sm-12">				{{Form::text('shipping_address2',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 and \session()->get('orderAddresses')['shipping_type'] != 'multiple')?\session()->get('orderAddresses')['shipping_address2']:'',['class'=>'form-control street2  opt','placeholder'=>'Street address 2','data'=>'add2'])}}
						</div>
						<div class="form-group col-sm-12">
						{{Form::label('shipping_ship_in_care','Ship in care of')}}				{{Form::text('shipping_ship_in_care',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 and \session()->get('orderAddresses')['shipping_type'] != 'multiple')?\session()->get('orderAddresses')['shipping_ship_in_care']:'',['class'=>'form-control  opt','placeholder'=>'Ship in care of','data'=>'add2'])}}
						</div>
						<div class="form-group col-sm-6">
						{{Form::label('shipping_zipcode','Zipcode')}}		{{Form::number('shipping_zipcode',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 and \session()->get('orderAddresses')['shipping_type'] != 'multiple')?\session()->get('orderAddresses')['shipping_zipcode']:'',['class'=>'form-control','placeholder'=>'Zipcode'])}}
						</div>
						<div class="form-group col-sm-6">                               {{Form::label('shipping_city','Town / City')}} {{Form::text('shipping_city',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 and \session()->get('orderAddresses')['shipping_type'] != 'multiple')?\session()->get('orderAddresses')['shipping_city']:'',['class'=>'form-control','placeholder'=>'City'])}}
						</div>
						<div class="form-group col-sm-6">                                {{Form::label('shipping_state','State')}} {{Form::select('shipping_state',$states,(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 and \session()->get('orderAddresses')['shipping_type'] != 'multiple')?\session()->get('orderAddresses')['shipping_state']:'',['class'=>'form-control','placeholder'=>'State'])}}
						</div>
						<div class="form-group col-sm-6">								{{Form::label('shipping_country','Country')}} {{Form::text('shipping_country',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0 and \session()->get('orderAddresses')['shipping_type'] != 'multiple')?\session()->get('orderAddresses')['shipping_country']:'US',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
						</div>
						<div class="form-group col-sm-12">{{Form::label('shipping_address_name','Name This Address')}}		{{Form::text('shipping_address_name',(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 0)?\session()->get('orderAddresses')['shipping_address_name']:'',['class'=>'form-control','placeholder'=>'Provide this address with a name for quick access'])}}
						</div>
					</div>
					<div class="row multi_shipping">
						@php
						if(\session()->has('orderAddresses') && \session()->get('orderAddresses')['same_as_billing'] == 0 && \session()->get('orderAddresses')['shipping_type'] === 'multiple'){
							
							$userAddress = \App\UserAddress::where('user_id',Auth::user()->id)->where('type',2)->pluck('address_name','id');
							
							foreach(\session()->get('cart') as $key=>$value){
								$product = \App\Products::select('name')->where('id',$value['product_id'])->first();
						@endphp
						<div class="col-sm-12 multi_product_add" data="{{$key}}" style="margin-left:15px">
							<h4>{{$product->name}}</h4>
							<div class="form-group col-sm-12 multi_shipping_error_{{$key}} has-error hide"></div>
							
							@php
								echo '<div class="form-group col-sm-12 multiDivBox" data="'.$key.'" data-key="'.$key.'">';
								
								echo '<div class="col-xs-8">'.Form::select('multi['.$key.'][shippingAddress]',$userAddress,\session()->get('orderAddresses')['multi'][$key]['shippingAddress'],['class'=>'form-control','placeholder'=>'Select from My Saved Addresses','data'=>$key]).'</div>';
								
								echo '<div class="clearfix"></div>';
								
								$select = (array_key_exists('option',\session()->get('orderAddresses')['multi'][$key]) && \session()->get('orderAddresses')['multi'][$key]['option'] == 0)?'checked':''; 
								
								echo '<div class="col-sm-6"><div class="radio"><label><input type="radio" class="shippingOption" name="multi['.$key.'][option]" value="0" '.$select.' data="'.$value['product_id'].'">New Address</label></div></div>';
								
								echo '</div>';
							@endphp
							
							<div class="col-sm-12 multiple_ship_div multi_div_{{$value['product_id']}}" style="{{($select == 'checked'?'display:block':'display:none')}}">
								<div class="form-group col-sm-6">	{{Form::label('multi['.$key.'][company_name]','Company Name')}}	{{Form::text('multi['.$key.'][company_name]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['company_name']:'',['class'=>'form-control','placeholder'=>'Company Name'])}}
								</div>
								<div class="form-group col-sm-6">	{{Form::label('multi['.$key.'][phone_number]','Phone Number')}}	{{Form::text('multi['.$key.'][phone_number]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['phone_number']:'',['class'=>'form-control','placeholder'=>'Phone Number'])}}
								</div>
								<div class="form-group col-sm-6">	{{Form::label('multi['.$key.'][fname]','First Name')}}	{{Form::text('multi['.$key.'][fname]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['fname']:'',['class'=>'form-control','placeholder'=>'First Name'])}}
								</div>
								<div class="form-group col-sm-6">	{{Form::label('multi['.$key.'][lname]','Last Name')}}	{{Form::text('multi['.$key.'][lname]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['lname']:'',['class'=>'form-control','placeholder'=>'Last Name'])}}
								</div>
								<div class="form-group col-sm-12">
									{{Form::label('','Street address')}}										{{Form::text('multi['.$key.'][add1]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['add1']:'',['class'=>'form-control','placeholder'=>'Street address 1'])}}
								</div>
								<div class="form-group col-sm-12">		{{Form::text('multi['.$key.'][add2]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['add2']:'',['class'=>'form-control street2','placeholder'=>'Street address 2','data'=>'add2'])}}
								</div>
								<div class="form-group col-sm-12">	
								{{Form::label('multi['.$key.'][ship_in_care]','Ship in care of')}}	{{Form::text('multi['.$key.'][ship_in_care]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['ship_in_care']:'',['class'=>'form-control street2 opt','placeholder'=>'Ship in care of','data'=>'ship_in_care'])}}
								</div>
								<div class="form-group col-sm-6">	{{Form::label('multi['.$key.'][zipcode]','Zipcode')}}	{{Form::number('multi['.$key.'][zipcode]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['zipcode']:'',['class'=>'form-control','placeholder'=>'Zipcode'])}}
								</div>
								<div class="form-group col-sm-6">{{Form::label('multi['.$key.'][city]','Town / City')}}							{{Form::text('multi['.$key.'][city]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['city']:'',['class'=>'form-control','placeholder'=>'City'])}}
								</div>
								<div class="form-group col-sm-6">{{Form::label('multi['.$key.'][state]','State')}}	{{Form::select('multi['.$key.'][state]',$states,(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['state']:'',['class'=>'form-control','placeholder'=>'State'])}}
								</div>
								<div class="form-group col-sm-6">{{Form::label('multi['.$key.'][country]','Country')}}	{{Form::text('multi['.$key.'][country]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['country']:'',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
								</div>
								<div class="form-group col-sm-12">	{{Form::label('multi['.$key.'][address_name]','Name This Address')}}	{{Form::text('multi['.$key.'][address_name]',(\session()->has('orderAddresses') and !empty(\session()->get('orderAddresses')['multi'][$key]))?\session()->get('orderAddresses')['multi'][$key]['address_name']:'',['class'=>'form-control','placeholder'=>'Provide this address with a name for quick access'])}}
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					@php	
						}
					}
					@endphp
					</div>
					
					<h4>Shipping Option</h4>
					<div class="form-group col-sm-12 ship_option_error has-error hide"></div>
					<div class="clearfix"></div>
					<div class="form-group col-sm-6">	
					@php
						$val = '';
						if(\session()->get('shipping_option') != 0 or \session()->get('shipping_option') != '0'){
							$val = \session()->get('shipping_option');
						}
					@endphp	{{Form::select('shipping_option',config('constants.Shipping_option'),$val,['class'=>'form-control','placeholder'=>'Select Shipping','data-type'=>'option'])}}
					</div>
					<div class="space"></div>
					<div class="clearfix"></div>
					<div class="form-group">
						<p style="font-size:12px;padding:0 20px;font-style: italic;font-weight: bold;">* Ship rates on flexible substrates are quoted as folded unless they are under 4’, then they will ship rolled. If you have a large vinyl or mesh banner over 4’ and you do not want it folded to avoid creases or fold marks,  please call us at 800-920-9527 to place the order, or just place the order and call us to make the pricing adjustment.</p>
					</div>
					<div class="clearfix"></div>
					
				{{Form::close()}}
				</div>
			</div>
			<div class="col-sm-5">
				<div class="order-summary">
					<h4>Order Summary</h4>
					<div class="order_info">
						<table class="table">
							<thead>
								<tr>
									<td><span>Product </span></td>
									<td><span class="text-right"> Total</span> </td>
								</tr>
							</thead>
							<tbody>
							@php
								$session_total = 0; 
								foreach($products as $product){
									$session_total += $product['total'];
									$product_detail = \App\Products::select('name')->where('id',$product['product_id'])->with('Catgory','product_image')->first();
							@endphp
								<tr>
									<td>
										{{$product_detail->name}}
									</td>
									<td>
										<span class="text-right"> ${{priceFormat($product['total'])}}</span>
									</td>
								</tr>
							@php
							}
							$session_total = priceFormat($session_total);
							@endphp
								<tr>
									<td>
										<span>Subtotal</span>
									</td>
									<td>
										<span class="text-right"> ${{priceFormat(\session()->get('carttotal.gross'))}}</span>
									</td>
								</tr>
								
								@if(\session()->get('carttotal.discount') != 0 or \session()->get('carttotal.discount') != '0')
									<tr>
										<td>
											<span>Discount</span>
										</td>
										<td>
											<span class="text-right"> ${{priceFormat(\session()->get('carttotal.discount'))}}</span>
										</td>
									</tr>
								@endif	
								@php
								$class="hide";
								if(\session()->get('carttotal.shipping') != 0 or \session()->get('carttotal.shipping') != '0'){
									$class = '';
								}								
								@endphp
								<tr class="shipping_tr {{$class}}">
									<td>
										<span>Shipping</span>
									</td>
									<td>
										<span class="text-right"> ${{priceFormat(\session()->get('carttotal.shipping'))}}</span>
									</td>
								</tr>
								@php
								$class="hide";
								if(\session()->get('carttotal.sales_tax') != 0 or \session()->get('carttotal.sales_tax') != '0'){
									$class = '';
								}								
								@endphp
								<tr class="tax_tr {{$class}}">
									<td>
										<span>Sales Tax</span>
									</td>
									<td>
										<span class="text-right"> ${{priceFormat(\session()->get('carttotal.sales_tax'))}}</span>
									</td>
								</tr>
								
								<tr class="total_tr">
									<td>
										<span>Total</span>
									</td>
									<td>
										<span class="text-right"> ${{ priceFormat(\session()->get('carttotal.total'))}}</span>
										<input type="hidden" id="order_total" value="{{ \session()->get('carttotal.total')}}"/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<h4>Place Order</h4>
					<div class="order_info">
						<div class="form-group col-sm-12 error_msg hide"></div>
						<table class="table">
							<tbody>
								@if(\Auth::user()->pay_by_invoice == 1)
								<tr>
									<td colspan="2">
										<div class="radio">
											<label>
												<input type="radio" name="optionsPayment" id="optionsRadios3" value="pay_by_invoice" />Pay By Invoice
											</label>
										</div>
									</td>
								</tr>
								@endif
								<tr>
									<td>
										<div class="radio">
											<label>
												<input type="radio" name="optionsPayment" id="optionsRadios3" value="credit_card" />Credit Card
											</label>
										</div>
									</td>
									<td class="text-right">
										<img src="{{URL::to('/public/img/front/card_04.jpg')}}" alt="" />
										<img src="{{URL::to('/public/img/front/card_03.jpg')}}" alt="" />
										<img src="{{URL::to('/public/img/front/card_02.jpg')}}" alt="" />
										<img src="{{URL::to('/public/img/front/card_01.jpg')}}" alt="" />
									</td>
								</tr>
								<tr>
									<td>
										<div class="radio">
											<label>
												<input type="radio" name="optionsPayment" id="optionsRadios3" value="paypal" />PayPal
											</label>
										</div>
									</td>
									<td class="text-right">
										<img src="{{URL::to('/public/img/front/paypal.jpg')}}" alt="" />
									</td>
								</tr>
							</tbody>
						</table>
						
						<div class="placeorder credit_card_payment" style="display:none;">	{{Form::model('authorized_payment',['class'=>'contactpage authorize_form'])}}
								@if(!empty($card_options))
								<div class="form-group col-xs-12 no-padding">
									{{Form::label('save_card_option','Saved Cards')}}	{{Form::select('save_card_option',$card_options,'',['class'=>'form-control','id'=>'save_card_option','placeholder'=>'Select Card Number'])}}
								</div>
								@endif
								<div class="form-group col-xs-12 no-padding">
									{{Form::label('card_number','Card Number')}}	{{Form::number('card_number','',['class'=>'form-control authorizePayment','id'=>'account_number','placeholder'=>'Card Number','maxlength'=>'19','minlength'=>'11'])}}
								</div>
								<div class="form-group col-sm-8 col-xs-12 no-padding main_box">
									{{Form::label('expiry_date','Expiry Date')}}<div class="clearfix"></div>
									<div class="payment_select col-xs-5">
									{{Form::select('expire_month',[''=>'Select Month']+$months,old('expire_month'),['class'=>'form-control authorizePayment','id'=>'expire_month'])}}
									</div>
									<div class="payment_select col-xs-5">
									{{Form::select('expire_year',[''=>'Select Year']+$years,old('expire_year'),['class'=>'form-control authorizePayment','id'=>'expire_year'])}}
									</div>									
								</div>
								<div class="form-group col-sm-4 col-xs-12 no-padding pull-right">
									{{Form::label('cvv_number','Card CVV Number')}}	{{Form::number('cvv_number','',['class'=>'form-control authorizePayment','id'=>'cvv_number','placeholder'=>'Card CVV Number','maxlength'=>'4','minlength'=>'3'])}}
								</div>
								<!--<div class="form-group col-xs-12 no-padding">
									<div class="" style="margin:0 4px">
										<label>
											<input type="checkbox" name="save_card" value="1"> Save Card Details
										</label>
									</div>
								</div>-->
								<div class="clearfix"></div>
							{{Form::close()}}
						</div>
						<div class="placeorder">
							<div class="processorder">
								<i class="fa fa-spinner fa-spin spinner_place hide" style="font-size:24px"></i>
								<a class="placeorder_btn" href="javascript:void(0)" data-type="process">PLACE ORDER</a>
								<div class="hide" id="paypal-button-container"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
$(document).ready(function() {
	var userAddresses = <?php echo json_encode($userAddresses->toArray(),JSON_PRETTY_PRINT);?>;
	
	$(document).on('click','input[name="billing_type"]', function(event){
		if($(this).val() == 0){
			$('div.new_billing_div').slideDown();
			$('.new_billing_div input').each( function (index, data) {
				$(this).closest('div').removeClass('has-error');
				$($(this)).nextAll().remove();
			});
		}else{
			$('div.new_billing_div').slideUp();
		}
		$('select[name="shipping_option"]').val('');
	});
	
	$(document).on('click','.multiDivBox input[type="radio"]', function(event){
		if($(this).val() == 0){
			$('select[name="multi['+$(this).attr('data')+'][shippingAddress]"]').val('');
			$('div.multi_div_'+$(this).attr('data')).slideDown();
			$('div.multi_div_'+$(this).attr('data')+' input,div.multi_div_'+$(this).attr('data')+' select').each( function (index, data) {
				$(this).closest('div').removeClass('has-error');
				$($(this)).nextAll().remove();
			});
		}else{
			$('div.multi_div_'+$(this).attr('data')).slideUp();
		}
		$('select[name="shipping_option"]').val('');
	});
	
	$(document).on('change','.selectAddressDiv select', function(event){
		if($(this).val() != ''){
			//$('.shipping_type_div').slideUp();
			$('.same_as_billing_shipping').slideUp();
			$('.same_as_billing_shipping').html('');
		
			var TempVal = $(this).val();
			$.each(userAddresses,function(i,v){
				if(TempVal == v.id){
					var str = '<address class="add_box" style="min-height: auto;">';
					
					if(v.company_name != '' && v.company_name != null)
						str += v.company_name+'<br/>';
					
					if(v.fname != '' && v.fname != null)
						str += v.fname;
					
					if(v.lname != '' && v.lname != null)
						str += '&nbsp;'+v.lname;
					
					str += "<br/>";
					
					str += v.add1+', ';
					
					if(v.add2 != '' && v.add2 != null)
						str += v.add2;
					
					str += '<br/>';
					
					str += v.city+', '+v.state+', '+v.zipcode+'<br/>';
					
					str += v.country;
					
					str += '</address>';
					
					$('.select_shipping').html(str);
					$('.select_shipping').slideDown();
				}
			});
		}else{
			$('div.shipping_type_1').slideDown();
			$('div.shipping_type_0').slideDown();
			$('div.shipping_type_multiple').slideDown();
			
			//$('div.shipping_type_1').after('<br/>');
			//$('div.shipping_type_0').after('<br/>');
			
			//$('.shipping_type_div').slideDown();
			
			$('.select_shipping').html('');
			$('.select_shipping').slideUp();
		}
		$('select[name="shipping_option"]').val('');
		$('.multi_shipping').html('');
		$('input[name="shipping_type"]').prop('checked',false);
		$('div.new_shipping_div').slideUp();
	});
	
	$(document).on('change','.multiDivBox select', function(event){
		$('div.multi_div_'+$(this).attr('data')).slideUp();
		
		$('input[name="multi['+$(this).attr('data')+'][option]"]').prop('checked',false);
		
		$('select[name="shipping_option"]').val('');
	});
	
	$(document).on('click','input[name="shipping_type"]', function(event){
		//$(this).closest('div.row').find('br').remove();
		$('select[name="selectAddress"]').val('');
		$('.multi_shipping').html('');
		$('.select_shipping').html('');
		$('.select_shipping').slideUp();
		$('.same_as_billing_shipping').html('');
		$('.same_as_billing_shipping').slideUp();
		
		if($(this).val() == 0){
			//$('div.shipping_type_1').slideUp();
			$('div.shipping_type_multiple').slideUp();
			
			$('div.new_shipping_div').slideDown();
			
			$('.new_shipping_div input,.new_shipping_div select').each( function (index, data) {
				$(this).closest('div').removeClass('has-error');
				$($(this)).nextAll().remove();
			});
			
			$('input[name="same_as_billing"]').val(0);
		}else if($(this).val() == 1){
			//$('div.shipping_type_0').slideUp();
			$('div.shipping_type_multiple').slideUp();
			$('div.new_shipping_div').slideUp();
			
			$('input[name="same_as_billing"]').val(1);
			
			var str = '<address class="add_box" style="min-height: auto;">';
			
			var billingAddressType = $('input[name="billing_type"]:checked').val();
			
			if(billingAddressType == 0){
				var newBillingForm = $('input[name="billing_company_name"]').val();
				
				if($('input[name="billing_company_name"]').val() != '' && $('input[name="billing_company_name"]').val() != null)
					str += $('input[name="billing_company_name"]').val()+'<br/>';
				
				if($('input[name="billing_fname"]').val() != '' && $('input[name="billing_fname"]').val() != null)
					str += $('input[name="billing_fname"]').val();
				
				if($('input[name="billing_lname"]').val() != '' && $('input[name="billing_lname"]').val() != null)
					str += '&nbsp;'+$('input[name="billing_fname"]').val();
				
				str += "<br/>";
				
				if($('input[name="billing_address1"]').val() != '' && $('input[name="billing_address1"]').val() != null)
					str += $('input[name="billing_address1"]').val()+', ';
				
				if($('input[name="billing_address2"]').val() != '' && $('input[name="billing_address2"]').val() != null)
					str += $('input[name="billing_address2"]').val();
				
				str += '<br/>'
				
				str += $('input[name="billing_city"]').val()+', '+$('select[name="billing_state"]').val()+', '+$('input[name="billing_zipcode"]').val()+'<br/>';
				str += $('input[name="billing_country"]').val();
				
			}else{
				str += $('.billing_address_box').html();
			}
			
			str += '</address>';
			
			$('.same_as_billing_shipping').html(str);
			$('.same_as_billing_shipping').slideDown();
		}else{
			//$('div.shipping_type_0').slideUp();
			//$('div.shipping_type_1').slideUp();
			
			$('div.new_shipping_div').slideUp();
			
			if($(this).val() == 'multiple'){
				$.ajax({
					url:'{{url("multiple/addresses")}}',
					type:'post',
					dataType:'json',
					beforeSend: function() {
						$('#fade').fadeIn(); 
						//$('.spinner_multi').removeClass('hide');
						$('#loader_img').fadeIn(); 
					},
					success:function(data){
						$('#fade').fadeOut();  
						//$('.spinner_multi').addClass('hide');
						$('#loader_img').fadeOut();
						if(data.status == 'success')
						$('.multi_shipping').html(data.res);
					}
				});
				$('input[name="same_as_billing"]').val(0);
			}
		}
		$('select[name="shipping_option"]').val('');
	});
	
	$('.processorder a').click(function(event){
		var type = $(this).attr('data-type');
		var valid = validate(type);
		if(valid){
			if(!$('input[name="optionsPayment"]').is(':checked')){
				$('.error_msg').html('* Plese select one payment option.');
				$('.error_msg').removeClass('hide');
			}else{
				$('.error_msg').addClass('hide');
				$('.error_msg').html('');
				var value = $('input[name="optionsPayment"]:checked').val();
				if(value == 'paypal'){
					//$('#paypal-button-container').trigger("click");
				}else if(value == 'credit_card'){
					var on = 1;
					//if($("#save_card_option").length > 0 and ){
					if($("#save_card_option").length > 0 && $("#save_card_option").val() != ''){
						if($("#cvv_number").val() == ''){
							on = 0;
							$("#cvv_number").closest('div').addClass('has-error');
							$("<span class='help-block'>This field is required</span>").insertAfter($("#cvv_number"));
						}else{
							$("#cvv_number").closest('div').removeClass('has-error');
							$($("#cvv_number")).nextAll().remove();
						}
					}else{
						$('.authorizePayment').each( function (index, data) {
							if($(this).attr('type') == 'number'){
								$(this).closest('div').removeClass('has-error');
								$($(this)).nextAll().remove();
								var maxlength = $(this).attr('maxlength');
								var minlength = $(this).attr('minlength');					
								var vallength = $(this).val().length;

								if($(this).val() == ''){
									on = 0;
									$(this).closest('div').addClass('has-error');
									$("<span class='help-block'>This field is required</span>").insertAfter($(this));
								}
								if(vallength>=1 && vallength < minlength)
								{
									on = 0;
									$(this).closest('div').addClass('has-error');
									$("<span class='help-block'>Value should be Min. "+minlength+" digits</span>").insertAfter($(this));
								}
								if(vallength>=1 && vallength > maxlength)
								{
									on = 0;
									$(this).closest('div').addClass('has-error');
									$("<span class='help-block'>Value should be Max. "+maxlength+" digits</span>").insertAfter($(this));
								}

							}else{
								$(this).closest('.form-group').removeClass('has-error');
								$(this).closest('.form-group').find('span').remove();
								$(this).closest('.form-group').find('br').remove();
								if($(this).val() == ''){
									on = 0;
									if(!$(this).closest('.form-group').hasClass('has-error')){
										$(this).closest('.form-group').addClass('has-error');
										$(this).closest('.form-group').append("<br/><br/><span class='help-block'>This field is required</span>");
									}
								}
							}
						});
					}
					var msg = '';
					if(on == 1){
						$('.error_msg').addClass('hide');
						$('.error_msg').html('');
						$.ajax({
							url:'{{url("payment/authorize")}}',
							type:'post',
							dataType:'json',
							data:$('form.authorize_form').serialize(),
							beforeSend: function() {
								$('#fade').fadeIn();  
								$('#loader_img').fadeIn(); 
							},
							success:function(data){					
								if(data.status == 'success'){
									msg = data.transaction.message;
									$.ajax({
										url:'{{url("saveOrder")}}',
										type:'post',
										dataType:'json',
										data:{'paymentID':data.transaction.transaction_id,'auth_code':data.transaction.auth_code},
										success:function(data){
											$('#fade').fadeOut();  
											$('#loader_img').fadeOut();
											if(data.status == 'success'){
												$(location).attr('href', '{{url("uploads")}}/'+data.orderId+'?msg='+msg)
											}
										}
									});
								}else{
								    // declined transaction - save order anyway so team can follow up
								    if (data.transaction.message.indexOf('This transaction has been declined') !== -1) {
                                        $.ajax({
                                            url:'{{url("saveOrder")}}',
                                            type:'post',
                                            dataType:'json',
                                            data:{'payment_status': 4, 'paymentID':data.transaction.transaction_id,'auth_code':data.transaction.auth_code},
                                            success:function(data){
                                                console.log(data);
                                            }
                                        });
                                    }

									$('#fade').fadeOut();  
									$('#loader_img').fadeOut();
									$('.error_msg').html(data.message);
									if(data.transaction.message !="")
									{
										$('.error_msg').html(data.transaction.message);
									}
									$('.error_msg').removeClass('hide');
								}
							}
						});
					}
				}else{
					var msg = 'This transaction has been approved. Thank you for your order.';
					$.ajax({
						url:'{{url("saveOrder")}}',
						type:'post',
						dataType:'json',
						data:{'paymentID':'','payment_type':'pay_by_invoice'},
						beforeSend: function() {
							$('#fade').fadeIn();  
							$('#loader_img').fadeIn(); 
						},
						success:function(data){
							$('#fade').fadeOut();  
							$('#loader_img').fadeOut();
							if(data.status == 'success'){
								$(location).attr('href', '{{url("uploads")}}/'+data.orderId+'?msg='+msg)
							}
						}
					});
				}
			}
		}
	});
	
	$('select[name="shipping_option"]').change(function(event){
		//return false;
		var type = $(this).attr('data-type');
		if($(this).val() == '' ){
			$(this).closest('div').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter($(this));
		}else{
			$(this).closest('div').removeClass('has-error');
			$($(this)).nextAll().remove();
			var valid = validate(type);
			if(valid){
					$.ajax({
					url:'{{url("saveAddress")}}',
					type:'post',
					dataType:'json',
					data:$('#checkout').serialize(),
					beforeSend: function() {
						$('#fade').fadeIn(); 
						$('#loader_img').fadeIn(); 
						//$('.spinner_place').removeClass('hide');
					},
					success:function(data){
						$('#fade').fadeOut();
						$('#loader_img').fadeOut(); 
						if(data.status == 'success'){
							$('.shipping_tr').html('<td><span>Shipping</span></td><td><span class="text-right"> $'+data.shipping+'</span></td>');
							$('.shipping_tr').removeClass('hide');
							if(Number(data.tax) > 0){
								$('.tax_tr').html('<td><span>Sales Tax</span></td><td><span class="text-right"> $'+data.tax+'</span></td>');
								$('.tax_tr').removeClass('hide');
							}else{
								$('.tax_tr').addClass('hide');
							}
							
							$('.total_tr').html('<td><span>Total</span></td><td><span class="text-right"> $'+formatMoney(data.total)+'</span><input type="hidden" id="order_total" value="'+data.total+'"/></td>');
						}else{
							alert(data.msg);
							$('select[name="shipping_option"]').val('');
						}
						
						$('html, body').animate({
							scrollTop: $('.cart_tab').offset().top
						}, 1000, 'linear');
					}
				});
			}else{
				$(this).val('');
				alert('Please fill address properly.');
			} 
		}
	});
	
	function validate(type){
		var errorCheckPoint = 1;
		var on = 1;
		
		/*************** Billing Address Check Validation *********/
		
		if(!$('input[name="billing_type"]').is(':checked')){
			on = 0;
			$('.billing_error').html('<span class="help-block">* Select Billing Option.</span>').removeClass('hide');
		}else{
			$('.billing_error').html('').addClass('hide');
			if($('input[name="billing_type"]:checked').val() == 0){
				$('.new_billing_div input,.new_billing_div select').each( function (index, data) {
					if(!$(this).hasClass('opt')){
						$(this).closest('div').removeClass('has-error');
						$($(this)).nextAll().remove();
						if($(this).val() == ''){
							on = 0;
							$(this).closest('div').addClass('has-error');
							$("<span class='help-block'>This field is required</span>").insertAfter($(this));
						}
					}
				});
			}
		}
		
		if(on == 0){
			errorCheckPoint = 0;
			$('html, body').animate({
				scrollTop: $('.billingHeading').offset().top
			}, 1000, 'linear');
		}
		
		/*************** End Billing Address Check Validation *********/
		
		/*************** Shipping Address Check Validation *********/
		
		$('.shipping_error').html('').addClass('hide');
		if($('input[name="shipping_type"]').is(':checked')){
			if($('input[name="shipping_type"]:checked').val() === 'multiple'){
				$('.multiDivBox').each(function(i,v){
					var dataNew = $(this).attr('data');
					var dataKey = $(this).attr('data-key');
					
					if($(this).find('input[name="multi['+dataKey+'][option]"]').is(':checked')){
						$('select[name="multi['+dataKey+'][shippingAddress]"]').val('');
						var errorCheck = 1;
							
						$('.multi_div_'+dataNew).each( function (index, data) {
							$('.multi_shipping_error_'+dataNew).html('').addClass('hide');
							
							$(this).find('input,select').each( function (index, data) {
								if(!$(this).hasClass('opt')){
									$(this).closest('div').removeClass('has-error');
									$($(this)).nextAll().remove();
									
									if($(this).val() == ''){
										on = 0;
										errorCheck = 0;
										$(this).closest('div').addClass('has-error');
										$("<span class='help-block'>This field is required</span>").insertAfter($(this));
									}
								}
							});
						});
						
						if(errorCheckPoint == 1 && errorCheck == 0){
							errorCheckPoint = 0;
							$('html, body').animate({
								scrollTop: $('.multi_div_'+dataNew).offset().top
							}, 1000, 'linear');
						}
					}else{
						if($('select[name="multi['+dataKey+'][shippingAddress]"]').val() == ''){
							on = 0;
							$('.multi_shipping_error_'+dataNew).html('<span class="help-block">* Select Shipping Option.</span>').removeClass('hide');
							if(errorCheckPoint == 1){
								errorCheckPoint = 0;
								$('html, body').animate({
									scrollTop: $('.multi_shipping_error_'+dataNew).offset().top
								}, 1000, 'linear');
							}
						}else{
							$('.multi_shipping_error_'+dataNew).html('').addClass('hide');
						}
					}
				});
			}else if($('input[name="shipping_type"]:checked').val() == 0){
				var errorCheck = 1;
				$('.new_shipping_div input,.new_shipping_div select').each( function (index, data) {
					if(!$(this).hasClass('opt')){
						
						$(this).closest('div').removeClass('has-error');
						$($(this)).nextAll().remove();
						if($(this).val() == ''){
							on = 0;
							errorCheck = 0;
							$(this).closest('div').addClass('has-error');
							$("<span class='help-block'>This field is required</span>").insertAfter($(this));
						}
					}
				});
				if(errorCheckPoint == 1 && errorCheck == 0){
					errorCheckPoint = 0;
					$('html, body').animate({
						scrollTop: $('.new_shipping_div').offset().top
					}, 1000, 'linear');
				}
			}
		}else{
			if($('select[name="selectAddress"]').val() == ''){
				on = 0;
				$('.shipping_error').html('<span class="help-block">* Select Shipping Address.</span>').removeClass('hide');
				if(errorCheckPoint == 1){
					errorCheckPoint = 0;
					$('html, body').animate({
						scrollTop: $('.shippingHeading').offset().top
					}, 1000, 'linear');
				}
			}
		}
		
		/*************** End Shipping Address Check Validation *********/
		
		$('select[name="shipping_option"]').closest('div').removeClass('has-error');
		$('select[name="shipping_option"]').nextAll().remove();
		if($('select[name="shipping_option"]').val() == ''){
			on = 0;
			$('.ship_option_error').html('<span class="help-block">* Select Shipping Option.</span>').removeClass('hide');
			$('select[name="shipping_option"]').closest('div').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter($('select[name="shipping_option"]'));
		}
		
		return on;
	}
	
	$(document).on('click','input[name="same_as_billing"]', function(event){
		if($(this).is(':checked')){
			$('.multi_shipping').html('');
			//$('.new_shipping_div').addClass('hide');
			$('.new_shipping_div').slideUp();
			$('.shipp_tab').slideUp(500);
			$('input[name="shipping_type"]').prop('checked', false);
			$('input[name="shipping_type"]').prop('disabled', true);
		}else{
			$('input[name="shipping_type"]').prop('disabled', false);
			$('.shipp_tab').removeClass('hide');
			//$('.new_shipping_div').removeClass('hide');
			$('.shipp_tab').slideDown(500);
			$('.new_shipping_div').slideUp(500);
		}
		$('select[name="shipping_option"]').val('');
	});
	
	
	
	// Paypal Payment  //
	paypal.Button.render({

		env: 'production', // sandbox | production

		// PayPal Client IDs - replace with your own
		// Create a PayPal app: https://developer.paypal.com/developer/applications/create
		client: {
			sandbox:    'AfrMzgla1M7Bi6aME3F8dW8GgQd5IHVxPChr4jaV7U2kYAsYKFo0sXN5dK1c8cd2XJ0GZEV5aWmAfGVX',
			production: 'AZKxqWcY76Pt3Kqbjk4Y-9YAUCyMYdu-k4RJAa8WHNtAvNlcqNTchqFNClruq9VOi7-A6nYxBvcA7ehP'
		},

		// Show the buyer a 'Pay Now' button in the checkout flow
		commit: true,

		// payment() is called when the button is clicked
		payment: function(data, actions) {

			// Make a call to the REST api to create the payment
			return actions.payment.create({
				payment: {
					transactions: [
						{
							amount: { total: $('#order_total').val(), currency: 'USD' },
						}
					]
				}
			});
		},

		// onAuthorize() is called when the buyer approves the payment
		onAuthorize: function(data, actions) {

			// Make a call to the REST api to execute the payment
			return actions.payment.execute().then(function() {
				//window.alert('Payment Complete!');
				//console.log(data);
				$.ajax({
					url:'{{url("saveOrder")}}',
					type:'post',
					dataType:'json',
					data:data,
					beforeSend: function() {
						$('#fade').fadeIn();  
						$('#loader_img').fadeIn(); 
					},
					success:function(data){
						$('#fade').fadeOut();  
						$('#loader_img').fadeOut();
						if(data.status == 'success'){
							$(location).attr('href', '{{url("uploads")}}/'+data.orderId+'?msg=This transaction has been approved. Thank you for your order.')
						}
					}
				});
			});
		},
		onCancel: function(data) {
			alert('The payment was cancelled!,Please try again.');
		},
		style: {
			size: 'small',
			color: 'gold',
			shape: 'pill',
			label: 'checkout'
		  },

	}, '#paypal-button-container');
	
	
	$('input[name="optionsPayment"]').change(function(){
		if(validate()){
			if($(this).val() == 'paypal'){
				$('.credit_card_payment').slideUp();
				$('.placeorder_btn').addClass('hide');
				$('#paypal-button-container').removeClass('hide');
			}else if($(this).val() == 'credit_card'){
				$('.credit_card_payment').slideDown();
				$('.placeorder_btn').removeClass('hide');
				$('#paypal-button-container').addClass('hide');
			}else{
				$('.placeorder_btn').removeClass('hide');
				$('#paypal-button-container').addClass('hide');
			}
		}else{
			$(this).prop('checked',false);
		}
	});
	
	$('#save_card_option').change(function(){
		var val = $(this).val();
		if(val != ''){
			$("#cvv_number").closest('div').removeClass('has-error');
			$($("#cvv_number")).nextAll().remove();
			
			$('#account_number').closest('div').fadeOut();
			$("#expire_year").closest('div.main_box').fadeOut();
			
			$('input[name="save_card"]').closest('div').fadeOut();
			$('input[name="save_card"]').attr('checked',false);
		}else{
			$('.authorizePayment').each( function (index, data) {
				if($(this).attr('type') == 'number'){
					$(this).closest('div').removeClass('has-error');
					$($(this)).nextAll().remove();
				}else{
					$(this).closest('.form-group').removeClass('has-error');
					$(this).closest('.form-group').find('span').remove();
					$(this).closest('.form-group').find('br').remove();
				}
			});
			$('#account_number').closest('div').fadeIn();
			$("#expire_year").closest('div.main_box').fadeIn();
			
			$('input[name="save_card"]').closest('div').fadeIn();
		}
	});
	
});

</script>
<style>
.add_box {
    border: 1px solid #cccc;
    margin: 5px;
    padding: 10px;
    min-height: 180px;
}
</style>
@endsection