@extends('layouts.app')
@section('content')
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<section class="pagestitles">
	<div class="container">
		<h2>Review and Checkout</h2>
	</div>
</section>
<section class="innerpages">
	 <div class="container">
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-lg-12">
			 	<table class="table table-striped table-bordered table-hover">
					@if(count($order)>=1)
						@php
							$status = config('constants.Order_status');							
							$i = 1;
						@endphp
						<tr>
							<th>Customer Detail</th>
							<td>
								<strong>Name : </strong>{{$order->customer->fname.' '.$order->customer->lname}}<br/>
								<strong>Email : </strong>{{$order->customer->email}}<br/>
								<strong>Phone Number : </strong>{{$order->customer->phone_number}}<br/>
								<strong>Company Name : </strong>{{$order->customer->company_name}}<br/>
							</td>
						</tr>
						<tr>
							<th>Order Products</th>
							<td>
								<table class="table table-bordered">
									<tr>
										<th>Description</th>
										<th>Quantity</th>
										<th>Total</th>									
									</tr>
									@foreach($order->Products as $val)
									<tr>
										<td>
											<li>
											@if($val->product_name !="")	
												{{$val->product_name}}
											@else
												{{$val->product->name}}
											@endif	
											</li>
											<div class="col-xs-12">
												{!!$val->description!!}
											<ol>
											@foreach($order->orderProductOptions as $options)
												@if($val->id == $options->order_product_id)
													@if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height')
													<li>{{$options->custom_option_name.'(ft)'}}: {{$options->value}}</li><br/>
													@else
														<li>	{{$options->custom_option_name}}: {{$options->value}}</li><br/>
													@endif	
												@endif
											@endforeach
											</ol>
											</div>
										</td>
										<td>{{$val->qty}}</td>
										<td>${{number_format($val->total,2,'.','')}}</td>
									</tr>
									@endforeach
								</table>
							</td>
						</tr>
						@if(\session()->has('paymentLink'))
							@php
								$order_total = \session()->get('paymentLink.total');
							@endphp
							<tr class="order_total">
								<th>Sub-Total:</th>
								<td>${{\session()->get('paymentLink.gross')}}</td>
							</tr>
							@if(\session()->get('paymentLink.free_shipping'))
							<tr>
								<th>shipping:</th>
								<td><span>${{\session()->get('paymentLink.shipping')}} (Free shipping applied on your order.)</span></td>
							</tr>
							@else
							<tr>
								<th>Discount:</th>
								<td>${{\session()->get('paymentLink.discount')}}</td>
							</tr>
							<tr>
								<th>Shipping:</th>
								<td>${{\session()->get('paymentLink.shipping')}}</td>
							</tr>
							@endif
							@if($order->sales_tax > 0)
							<tr class="">
								<th>Sales Tax:</th>
								<td>${{number_format($order->sales_tax,2,'.','')}}</td>
							</tr>
							@endif
							<tr>
								<th>Total:</th>
								<td>${{\session()->get('paymentLink.total')}}</td>
							</tr>
						@else
							@php
								$order_total = number_format($order->total,2,'.','');
							@endphp
						<tr class="order_total">
							<th>Sub-Total:</th>
							<td>${{number_format($order->sub_total,2,'.','')}}</td>
						</tr>
						@if(!empty($order->discount))
							<tr class="">
								<th>Discount:</th>
								<td>${{number_format($order->sub_total,2,'.','')}}</td>
							</tr>
						@endif
						<tr class="">
							<th>Shipping:</th>
							<td>${{number_format($order->shipping_fee,2,'.','')}}</td>
						</tr>
						@if($order->sales_tax > 0)
						<tr class="">
							<th>Sales Tax:</th>
							<td>${{number_format($order->sales_tax,2,'.','')}}</td>
						</tr>
						@endif
						<tr class="">
							<th>Total</th>
							<td>${{number_format($order->total,2,'.','')}}</td>
						</tr>
						@endif
					@endif
				</table>
		 	</div>
	 	</div>
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group coupon">
					@php
						$code = '';
						if(\session()->has('paymentLink'))
						$code = \session()->get('paymentLink.discount_code');
					@endphp
					<input type="text" class="form-control" id="coupon_code" placeholder="Enter Coupon" value="{{($code != 0 or $code != '0')?$code:''}}">
					<a href="javascript:void(0)" class="coupon_code_btn">apply coupon</a>
					@if($code != '')
						<span class="text-success">{{\session()->get('paymentLink.coupon_msg')}}</span>
					@endif
				</div>
			</div>
		</div>
		@php
			$user_addresses = \App\UserAddress::where('user_id',$order->customer->id)->where('status',1)->get();
		@endphp
		<div class="row">
			@if($order->skip_address <= 1)
			<div class="col-sm-7">
				<div class="billingdetails billingdetails_display" style="display:{{($order->orderAddress->billing_add_id!="") ? 'block' : 'none'}}">
					<h4 class="billingHeading">Billing Details</h4>
					<div class="clearfix"></div>
					<address class="billing_address_box">
						@if(!empty($order->orderAddress->billing_company_name))
							{{$order->orderAddress->billing_company_name}}<br/>
						@endif							
						@if(!empty($order->orderAddress->billing_fname))
							{{$order->orderAddress->billing_fname}}
						@endif
						@if(!empty($order->orderAddress->billing_lname))
							&nbsp;{{$order->orderAddress->billing_lname}}
						@endif
						<br/>
						{{$order->orderAddress->billing_add1}},&nbsp;
						@if(!empty($order->orderAddress->billing_add2))
							{{$order->orderAddress->billing_add2}}
						@endif
						<br/>
						{{$order->orderAddress->billing_city.', '.$order->orderAddress->billing_state.', '.$order->orderAddress->billing_zipcode}}<br/>
						{{$order->orderAddress->billing_country}}
					</address>
					<div class="clearfix"></div>
					
					<h4 class="shippingHeading">Shipping Details</h4>
					<div class="clearfix"></div>					
					<address class="billing_address_box" style="min-height:auto">
						@if(!empty($order->orderAddress->shipping_company_name))
							{{$order->orderAddress->shipping_company_name}}<br/>
						@endif							
						@if(!empty($order->orderAddress->shipping_fname))
							{{$order->orderAddress->shipping_fname}}
						@endif
						@if(!empty($order->orderAddress->shipping_lname))
							&nbsp;{{$order->orderAddress->shipping_lname}}
						@endif
						<br/>
						{{$order->orderAddress->shipping_add1}},&nbsp;
						@if(!empty($order->orderAddress-shipping_add2))
							{{$order->orderAddress->shipping_add2}}
						@endif
						<br/>
						{{$order->orderAddress->shipping_city.', '.$order->orderAddress->shipping_state.' '.$order->orderAddress->shipping_zipcode . ' ' . $order->orderAddress->shipping_country}}
					</address>
					<div class="clearfix"></div>
					
					<h4>Shipping Type</h4>					
					<div class="clearfix"></div>
					{{config('constants.Shipping_option')[$order->shipping_option]}}							
					<div class="space"></div>
					<div class="clearfix"></div>					
					<div class="form-group col-sm-6 text-center">							
						<button class="btn btn-success changebilling">Change Billing & Shipping Details</button>
						<div class="space"></div>
					</div>	

				</div>

				<div class="billingdetails billingdetails_edit" style="display:{{($order->orderAddress->billing_add_id=="") ? 'block' : 'none'}}">
				{{Form::model('OrderAddress',['id'=>'OrderAddress'])}}
					{{Form::hidden('order_id',$id)}}
					<h4 class="billingHeading">Billing Details</h4>
					<div class="form-group col-sm-12 billing_error has-error hide"></div>
					<div class="clearfix"></div>
					@if(count($user_addresses) > 0)
						@php 
						$i = 0;
						@endphp
						@foreach($user_addresses as $user_address)
							@if($user_address->type == 1)
								@php
									$i++;
									$checked = '';
									/* if($i == 1){ */
									if($order->orderAddress->billing_add_id == $user_address->id){
										$checked = 'checked';
									}
								@endphp
								<div class="form-group col-sm-6">
									<div class="radio">
										<label>
											<input type="radio" name="billing_type" value="{{$user_address->id}}" {{$checked}}/> 
											<address class="billing_address_box">
												@if(!empty($user_address->company_name))
													{{$user_address->company_name}}<br/>
												@endif	
												
												@if(!empty($user_address->fname))
													{{$user_address->fname}}
												@endif
												@if(!empty($user_address->lname))
													&nbsp;{{$user_address->lname}}
												@endif
												<br/>
												{{$user_address->add1}},&nbsp;
												@if(!empty($user_address->add2))
													{{$user_address->add2}}
												@endif
												<br/>
												{{$user_address->city.', '.$user_address->state.', '.$user_address->zipcode}}<br/>
												{{$user_address->country}}
											</address>
										</label>
									</div>
								</div>																
							@endif
						@endforeach
					@endif
					<div class="form-group col-sm-6">
						<div class="radio">
							<label><input type="radio" name="billing_type" value="0" /> New Address</label>
						</div>
					</div>
					<div class="col-sm-12 new_billing_div" style="display:none;">
						<div class="form-group col-sm-6">
							{{Form::label('billing_company_name','Company Name')}}		{{Form::text('billing_company_name','',['class'=>'form-control companyInput','placeholder'=>'Company Name'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('billing_phone_number','Phone Number')}}		{{Form::text('billing_phone_number','',['class'=>'form-control','placeholder'=>'Phone Number'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('billing_fname','First Name')}}		{{Form::text('billing_fname','',['class'=>'form-control','placeholder'=>'First Name'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('billing_lname','Last Name')}}		{{Form::text('billing_lname','',['class'=>'form-control','placeholder'=>'Last Name'])}}
						</div>
						<div class="form-group col-sm-12">
							{{Form::label('','Street address')}} {{Form::text('billing_address1','',['class'=>'form-control','placeholder'=>'Street address 1'])}}
						</div>
						<div class="form-group col-sm-12">				{{Form::text('billing_address2','',['class'=>'form-control street2','placeholder'=>'Street address 2','data'=>'add2'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('billing_zipcode','Zipcode')}}		{{Form::number('billing_zipcode','',['class'=>'form-control','placeholder'=>'Zipcode'])}}
						</div>
						<div class="form-group col-sm-6">                               
							{{Form::label('billing_city','Town / City')}} {{Form::text('billing_city','',['class'=>'form-control','placeholder'=>'City'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('billing_state','State')}} 
							{{Form::select('billing_state',$states,'',['class'=>'form-control','placeholder'=>'State'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('billing_country','Country')}} {{Form::text('billing_country','US',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
						</div>
					</div>
					<div class="clearfix"></div>
					
					<h4 class="shippingHeading">Shipping Details</h4>
					
					<div class="form-group col-sm-12 shipping_error has-error hide"></div>
					
					<div class="clearfix"></div>
					
					<div class="col-sm-8 selectAddressDiv">
						{{ Form::select('selectAddress',$userAddress,(\session()->has('selectAddress'))?\session()->get('selectAddress'):'',['class'=>'form-control','placeholder'=>'Select Address','data-type'=>''])}}
						
						<div class="clearfix"></div>
						<div class="select_shipping">
						</div><br/>
					</div><br/>
					
					<input type="hidden" name="same_as_billing" value="0"/>
					
					<div class="clearfix"></div>
					
					<div class="col-sm-12 shipping_type_div shipp_tab">
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
						<!--<div class="col-sm-6 col-xs-12 radio shipping_type_multiple no-padding">
							<label>
								<input type="radio" name="shipping_type" value="multiple" {{(\session()->has('orderAddresses') and \session()->get('orderAddresses')['shipping_type'] == 'multiple')?'checked':''}}> Split Shipping to Multiple Addresses
							</label>
							<i class="fa fa-spinner fa-spin spinner_multi hide" style="font-size:24px"></i>
						</div>-->
					</div>					
					
					@php
						$display = 'display:none;';
						if(\session()->has('orderAddresses') && \session()->get('orderAddresses')['shipping_type'] == 0){
							$display = 'display:block';
						}
					@endphp
					<div class="col-sm-12 new_shipping_div no-padding" style="{{$display}}">
						<div class="form-group col-sm-6">
							{{Form::label('shipping_company_name','Company Name')}}		{{Form::text('shipping_company_name','',['class'=>'form-control companyInput opt','placeholder'=>'Company Name'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('shipping_phone_number','Phone Number')}}	{{Form::text('shipping_phone_number','',['class'=>'form-control opt','placeholder'=>'Phone Number'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('','First Name')}}		{{Form::text('shipping_fname','',['class'=>'form-control','placeholder'=>'First Name'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('','Last Name')}}			{{Form::text('shipping_lname','',['class'=>'form-control','placeholder'=>'Last Name'])}}
						</div>
						<div class="form-group col-sm-12">
							{{Form::label('','Street address')}} {{Form::text('shipping_address1','',['class'=>'form-control','placeholder'=>'Street address 1'])}}
						</div>
						<div class="form-group col-sm-12">
							{{Form::text('shipping_address2','',['class'=>'form-control street2 opt','placeholder'=>'Street address 2','data'=>'add2'])}}
						</div>
						<div class="form-group col-sm-12">
							{{Form::label('shipping_ship_in_care','Ship in care of')}}				{{Form::text('shipping_ship_in_care','',['class'=>'form-control street2 opt','placeholder'=>'Ship in care of','data'=>'add2'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('shipping_zipcode','Zipcode')}}		{{Form::number('shipping_zipcode','',['class'=>'form-control','placeholder'=>'Zipcode'])}}
						</div>
						<div class="form-group col-sm-6">                               
							{{Form::label('shipping_city','Town / City')}} {{Form::text('shipping_city','',['class'=>'form-control','placeholder'=>'City'])}}
						</div>
						<div class="form-group col-sm-6">
							{{Form::label('shipping_state','State')}} {{Form::select('shipping_state',$states,'',['class'=>'form-control','placeholder'=>'State'])}}
						</div>
						
						<div class="form-group col-sm-6">
							{{Form::label('shipping_country','Country')}} {{Form::text('shipping_country','US',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
						</div>
						<div class="form-group col-sm-12">
							{{Form::label('shipping_address_name','Name This Address')}}		{{Form::text('shipping_address_name','',['class'=>'form-control','placeholder'=>'Name This Address'])}}
						</div>
					</div>
					
					<div class="row multi_shipping"></div>
					
					<div class="clearfix"></div>
					
					<h4>Shipping Option</h4>
					<div class="form-group col-sm-12 ship_option_error has-error hide"></div>
					<div class="clearfix"></div>
					<div class="form-group col-sm-6">	
						@php
							$val = $order->shipping_option;
						@endphp	{{Form::select('shipping_option',config('constants.Shipping_option'),$val,['class'=>'form-control','placeholder'=>'Select Shipping','data-type'=>'option'])}}
					</div>
					<div class="space"></div>
					<div class="clearfix"></div>
					
				{{Form::close()}}
				</div>
			</div>
			@endif
			<div class="col-sm-5">
				<div class="order-summary">
					<h4>Payment for Order</h4>
					<div class="order_info">
						<div class="placeorder">
							<table class="table">
								<tbody>
									@if($order->customer->pay_by_invoice == 1)
									<tr>
										<td colspan="2">
											<div class="radio">
												<label><input type="radio" name="optionsPayment" id="optionsRadios3" value="pay_by_invoice"/>Pay By Invoice</label>
											</div>
										</td>
									</tr>
									@endif
									<tr>
										<td>
											<div class="radio">
												<label><input type="radio" name="optionsPayment" id="optionsRadios3" value="credit_card" />Credit Card</label>
												<img src="{{url('public/img/front/card_04.jpg')}}" alt="" />
												<img src="{{url('public/img/front/card_03.jpg')}}" alt="" />
												<img src="{{url('public/img/front/card_02.jpg')}}" alt="" />
												<img src="{{url('public/img/front/card_01.jpg')}}" alt="" />
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="radio">
												<label><input type="radio" name="optionsPayment" id="optionsRadios3" value="paypal" />PayPal</label>
												<img src="{{url('public/img/front/paypal.jpg')}}" alt="" />
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="cart_tab">
					<div class="error_msg"></div>
				</div>
				@if($order->customer->pay_by_invoice == 1)
				<div class="pay_by_invoice" style="display:none;">
					<div class="order-summary">
						<h4>Place Order</h4>
						<div class="order_info">
							<div class="placeorder">
								<div class="processorder">
									<a class="placeorder_btn" href="javascript:void(0)" data-type="process">PLACE ORDER</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				@endif
				<div class="paypal_payment" style="display:none;">
					<div class="order-summary">
						<h4>Payment By Paypal</h4>
						<div class="order_info">
							<div class="form-group col-sm-12 payment_error has-error hide"></div>
							<div class="placeorder">
								<div class="processorder">
									<div id="paypal-button-container"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="credit_card_payment" style="display:none;">
					<div class="order-summary">
						<h4>Payment By Credit Card</h4>
						<div class="clearfix"></div>
						<div class="placeorder">
							{{Form::model('authorized_payment',['class'=>'contactpage authorize_form'])}}
								{{Form::hidden('order_id',$order->id)}}
								{{Form::hidden('user_id',$order->user_id)}}
								{{Form::hidden('amount',number_format($order_total,2,'.',''),['id'=>'amount'])}}
								@if(!empty($card_options))
								<div class="form-group col-xs-12 no-padding">
									{{Form::label('save_card_option','Saved Cards')}}	{{Form::select('save_card_option',$card_options,'',['class'=>'form-control','id'=>'save_card_option','placeholder'=>'Select Card Number'])}}
								</div>
								@endif
								<div class="form-group col-xs-12 no-padding">
									{{Form::label('card_number','Card Number')}}	{{Form::number('card_number','',['class'=>'form-control authorizePayment','id'=>'account_number','placeholder'=>'Card Number','maxlength'=>'19','minlength'=>'11'])}}
								</div>
								<div class="form-group col-md-8 col-xs-12 no-padding main_box">
									{{Form::label('expiry_date','Expiry Date')}}<div class="clearfix"></div>
									<div class="payment_select col-xs-5">
									{{Form::select('expire_month',[''=>'Select Month']+$months,old('expire_month'),['class'=>'form-control authorizePayment','id'=>'expire_month'])}}
									</div>
									<div class="payment_select col-xs-5">
									{{Form::select('expire_year',[''=>'Select Year']+$years,old('expire_year'),['class'=>'form-control authorizePayment','id'=>'expire_year'])}}
									</div>									
								</div>
								<div class="form-group col-md-4 col-xs-12 no-padding pull-right">
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
								<button type="submit" class="btn btn-default pay_authorize">Pay</button>
							{{Form::close()}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
$(document).ready(function() {
		
	$(document).on('click','.changebilling', function(event){
		$('.billingdetails_display').slideUp();
		$('.billingdetails_edit').slideDown();
	});	
	// New Shipping functionality Add //

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
	
	$(document).on('click','input[name="same_as_billing"]', function(event){
		if($(this).is(':checked')){
			$('.multi_shipping').html('');
			$('.new_shipping_div').slideUp();
			$('.shipp_tab').slideUp(500);
			$('input[name="shipping_type"]').prop('checked', false);
			$('input[name="shipping_type"]').prop('disabled', true);
		}else{
			$('input[name="shipping_type"]').prop('disabled', false);
			$('.shipp_tab').removeClass('hide');
			$('.shipp_tab').slideDown(500);
			$('.new_shipping_div').slideDown(500);
		}
		$('select[name="shipping_option"]').val('');
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
					url:'{{url("order-pending/saveAddress")}}',
					type:'post',
					dataType:'json',
					data:$('#OrderAddress').serialize(),
					beforeSend: function() {
						$('#fade').fadeIn(); 
						$('#loader_img').fadeIn(); 
						//$('.spinner_place').removeClass('hide');
					},
					success:function(data){
						$('#fade').fadeOut();
						$('#loader_img').fadeOut(); 
						if(data.status == 'success'){
							var str = '<tr class="order_total"><th>Sub-total:</th><td>$'+data.gross+'</td></tr>';
							
							str += '<tr><th>Discount:</th><td>$'+data.discount+'</td></tr>';
							str += '<tr><th>Shipping:</th><td>$'+data.shipping+'</td></tr>';
							if(Number(data.tax) > 0){
								str += '<tr><th>Tax:</th><td>$'+data.tax+'</td></tr>';
							}
							
							str += '<tr><th>Total:</th><td>$'+data.total+'</td></tr>';
							
							$('.order_total').nextAll().remove();
							$('.order_total').replaceWith(str);
							$('#amount').val(data.total);
						}else{
							alert(data.msg);
							$('select[name="shipping_option"]').val('');
						}
						$('html, body').animate({
							scrollTop: $('.billingHeading').offset().top
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
		
		@if($order->skip_address == 0)
			return on;
		@endif
		
		/*************** Billing Address Check Validation *********/
		
		if(!$('input[name="billing_type"]').is(':checked')){
			on = 0;
			$('.billing_error').html('<span class="help-block">* Select Billing Option.</span>').removeClass('hide');
		}else{
			$('.billing_error').html('').addClass('hide');
			if($('input[name="billing_type"]:checked').val() == 0){
				$('.new_billing_div input,.new_billing_div select').each( function (index, data) {
					if(!$(this).hasClass('companyInput') && !$(this).hasClass('street2')){
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
				$('.shipping_error').html('<span class="help-block">* Select Shipping Option.</span>').removeClass('hide');
				if(errorCheckPoint == 1){
					errorCheckPoint = 0;
					$('html, body').animate({
						scrollTop: $('.shippingHeading').offset().top
					}, 1000, 'linear');
				}
			}
		}
		
		/*************** End Shipping Address Check Validation *********/
		
		
		
		
		
		
		
		
		/* if(!$('input[name="same_as_billing"]').is(':checked')){
			$('.shipping_error').html('').addClass('hide');
			
			if($('input[name="shipping_type"]:checked').val() === 'multiple'){
				$('.multi_product_add').each( function (index, data) {
					var data_new = $(this).attr('data');
					
					$('.multi_shipping_error_'+data_new).html('').addClass('hide');
					
					$(this).find('input,select').each( function (index, data) {
						if(!$(this).hasClass('companyInput') && !$(this).hasClass('street2') && !$(this).hasClass('ship_in_care')){
							$(this).closest('div').removeClass('has-error');
							$($(this)).nextAll().remove();
							
							if($(this).val() == ''){
								on = 0;
								$(this).closest('div').addClass('has-error');
								$("<span class='help-block'>This field is required</span>").insertAfter($(this));
							}
						}
					});
				});
			}else{
				$('.new_shipping_div input,.new_shipping_div select').each( function (index, data) {
					if(!$(this).hasClass('companyInput') && !$(this).hasClass('street2')){
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
		}else{
			$('.multi_shipping').html('');
			$('input[name="shipping_type"]').prop('checked', false)
		} */
		
		$('select[name="shipping_option"]').closest('div').removeClass('has-error');
		$('select[name="shipping_option"]').nextAll().remove();
		if($('select[name="shipping_option"]').val() == ''){
			on = 0;
			$('select[name="shipping_option"]').closest('div').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter($('select[name="shipping_option"]'));
		}
		
		return on ;
	}
	
	/* $(document).on('change','.selectAddressDiv select', function(event){
		if($(this).val() != ''){
			$('.shipping_type_div').slideUp();			
		}else{
			$('div.shipping_type_1').slideDown();
			$('div.shipping_type_0').slideDown();
			$('div.shipping_type_multiple').slideDown();
			
			$('div.shipping_type_1').after('<br/>');
			$('div.shipping_type_0').after('<br/>');
			
			$('.shipping_type_div').slideDown();
		}
		$('select[name="shipping_option"]').val('');
		$('.multi_shipping').html('');
		$('input[name="shipping_type"]').prop('checked',false);
		$('div.new_shipping_div').slideUp();
	}); */
	
	$(document).on('click','input[name="shipping_type"]', function(event){
		var order_id = $('input[name="order_id"]').val();
		
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
				str += $('.billingdetails_edit .billing_address_box').html();
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
					url:'{{url("pending-order/multiple/addresses")}}',
					type:'post',
					data:'order_id='+order_id,
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
	
	$(document).on('change','.multiDivBox select', function(event){
		$('div.multi_div_'+$(this).attr('data')).slideUp();
		
		$('input[name="multi['+$(this).attr('data')+'][option]"]').prop('checked',false);
		
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
	
	// End New Shipping functionality Add //
	
	

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
							amount: { total: $('#amount').val(), currency: 'USD' },
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
				//alert(data.paymentID);
				msg = 'This transaction has been approved.';
				$.ajax({
					url:'{{url("order/updatePayment")}}',
					type:'post',
					dataType:'json',
					data:{'order_id':'{{$order->id}}','paymentID':data.paymentID,'msg':msg},
					beforeSend: function() {
						$('#fade').fadeIn(); 
						$('#loader_img').fadeIn(); 
						//$('.spinner_place').removeClass('hide');
					},
					success:function(data){
						$('#fade').fadeOut();  
						$('#loader_img').fadeOut();
						if(data.status == 'success'){
							//$(location).attr('href', '{{url("/")}}/');
							$(location).attr('href', '{{url("uploads")}}/'+data.orderId+'?msg=This transaction has been approved. Thank you for your order.')
						}
					}
				});
			});
		}

	}, '#paypal-button-container');
	
	$("form.authorize_form").submit(function(e){
        e.preventDefault();
		var on = 1;
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
			$.ajax({
				url:'{{url("payment-link/authorize")}}',
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
							url:'{{url("order/updatePayment")}}',
							type:'post',
							dataType:'json',
							data:{'order_id':'{{$order->id}}','paymentID':data.transaction.transaction_id,'auth_code':data.transaction.auth_code,'msg':msg},
							success:function(data){
								$('#fade').fadeOut();  
								$('#loader_img').fadeOut();
								if(data.status == 'success'){
									//$(location).attr('href', '{{url("/")}}/');
									$(location).attr('href', '{{url("uploads")}}/'+data.orderId+'?msg='+msg)
								}
							}
						});
					}else{
						$('#fade').fadeOut();  
						$('#loader_img').fadeOut();
						//$('.error_msg').html(data.message+'('+data.transaction.message+')');
						$('.error_msg').html(data.message);
						if(data.transaction.message !="")
						{
							$('.error_msg').html(data.transaction.message);
						}						
						
					}
				}
			});
		}
    });
	
	$('input[name="optionsPayment"]').change(function(){
		if(validate()){
			if($(this).val() == 'paypal'){
				$('.credit_card_payment').slideUp();
				$('.pay_by_invoice').slideUp();
				$('.paypal_payment').slideDown();
			}else if($(this).val() == 'credit_card'){
				$('.paypal_payment').slideUp();
				$('.pay_by_invoice').slideUp();
				$('.credit_card_payment').slideDown();
			}else{
				$('.paypal_payment').slideUp();
				$('.credit_card_payment').slideUp();
				$('.pay_by_invoice').slideDown();
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
	
	$('.coupon_code_btn').click(function(){
		var code = $('#coupon_code').val();
		if(code != ''){
			$('#coupon_code').closest('.form-group').removeClass('has-error');
			$('.coupon_code_btn').nextAll().remove();
			$.ajax({
					url:'{{url("payment/link/applycoupon")}}',
					type:'post',
					dataType:'json',
					data:{'code':code,'order_id':'{{$order->id}}'},
					beforeSend: function() {
						//$('#fade').fadeIn();
					},
					success:function(data){
						$('#fade').fadeOut();
						if(data.status == 'success'){
							if(data.code_apply == 1){
								$("<span class='text-success'>"+data.msg+"</span>").insertAfter($('.coupon_code_btn'));
								
								var str = '<tr class="order_total"><th>Sub-total:</th><td>$'+data.gross+'</td></tr>';
								if(data.code_type == 'free_shipping'){
									str += '<tr><th>Shiping:</th><td><span class="help-block">$'+data.shipping+" ("+data.msg+")</span>"+'</td></tr>';
								}else{
									str += '<tr><th>Discount:</th><td>$'+data.discount_amount+'</td></tr>';
									str += '<tr><th>Shiping:</th><td>$'+data.shipping+'</td></tr>';
								}
								str += '<tr><th>Total:</th><td>$'+data.total+'</td></tr>';
								
								$('.order_total').nextAll().remove();
								$('.order_total').replaceWith(str);
								$('#amount').val(data.total);
							}else{
								$('#coupon_code').closest('.form-group').addClass('has-error');
								$("<span class='help-block'>"+data.msg+"</span>").insertAfter($('.coupon_code_btn'));
							}
						}
					}
				});
		}else{
			$('#coupon_code').closest('.form-group').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter($('.coupon_code_btn'));
		}
	});
	
	$(".placeorder_btn").click(function(){
		var msg = 'This transaction has been approved. Thank you for your order.';
		$.ajax({
			url:'{{url("order/updatePayment")}}',
			type:'post',
			dataType:'json',
			data:{'order_id':'{{$order->id}}','paymentID':'','payment_type':'pay_by_invoice','msg':msg},
			success:function(data){
				$('#fade').fadeOut();  
				$('#loader_img').fadeOut();
				if(data.status == 'success'){
					//$(location).attr('href', '{{url("/")}}/');
					$(location).attr('href', '{{url("uploads")}}/'+data.orderId+'?msg='+msg)
				}
			}
		});
	});
	
	$( ".date-picker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		minDate:0,
	});
	
	var userAddresses = <?php echo json_encode($userAddresses->toArray(),JSON_PRETTY_PRINT);?>;
	
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
						str += '&nbsp;'+v.lname+'';
					
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