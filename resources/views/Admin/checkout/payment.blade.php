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
				<li class="active"> <a href="{{url('checkout')}}"><span>2</span> Billing, shipping</a></li>
				<li class="active"> <a href="{{url('payment')}}"><span>3</span> Payment</a></li>
				<li class=""> <a href="javascript:void(0)"><span>4</span> Uploads</a></li>
			</ul>
		</div>
		
		<!--<div class="row">
			<div class="col-sm-12">
				<button class="check">Click</button>
			</div>
		</div>-->
		<div class="row">
			<div class="col-sm-5">
				<div class="order-summary">
					<h4>Order Summary</h4>
					<div class="order_info">
						<table class="table">
							<tbody>
								<tr>
									<td>
										<span>Subtotal</span>
									</td>
									<td>
										<span class="text-right"> ${{\session()->get('carttotal.gross')}}</span>
									</td>
								</tr>
								
								@if(\session()->get('carttotal.discount') != 0 or \session()->get('carttotal.discount') != '0')
									<tr>
										<td>
											<span>Discount</span>
										</td>
										<td>
											<span class="text-right"> ${{\session()->get('carttotal.discount')}}</span>
										</td>
									</tr>
								@endif								
								<tr>
									<td>
										<span>Shipping</span>
									</td>
									<td>
										<span class="text-right"> ${{\session()->get('carttotal.shipping')}}</span>
									</td>
								</tr>
								<tr>
									<td>
										<span>Total</span>
									</td>
									<td>
										<span class="text-right"> ${{ \session()->get('carttotal.total')}}</span>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="order-summary">
					<h4>Payment for Order</h4>
					<div class="order_info">
						<div class="placeorder">
							<table class="table">
								<tbody>
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
			</div>
		</div>
		<div class="cart_tab">
			<div class="error_msg"></div>
		</div>
		<div class="row paypal_payment hide">
			<div class="col-sm-12">
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
		</div>
		<div class="row credit_card_payment hide">
			<div class="col-sm-6">
				<div class="order-summary">
					<h4>Payment By Credit Card</h4>
					<div class="clearfix"></div>
					<div class="placeorder">
						{{Form::model('authorized_payment',['class'=>'contactpage authorize_form'])}}
							<div class="form-group col-xs-12 no-padding">
								{{Form::label('card_number','Card Number')}}	{{Form::text('card_number','',['class'=>'form-control authorizePayment','id'=>'account_number','placeholder'=>'Card Number'])}}
							</div>
							<div class="form-group col-md-7 col-xs-12 no-padding main_box">
								{{Form::label('expiry_date','Expiry Date')}}<div class="clearfix"></div>
								<div class="payment_select col-xs-5">
								{{Form::select('expire_year',[''=>'Select Year']+$years,old('expire_year'),['class'=>'form-control authorizePayment'])}}
								</div>
								<div class="payment_select col-xs-5">
								{{Form::select('expire_month',[''=>'Select Month']+$months,old('expire_month'),['class'=>'form-control authorizePayment'])}}
								</div>
							</div>
							<div class="form-group col-md-5 col-xs-12 no-padding pull-right">
								{{Form::label('cvv_number','Card CVV Number')}}	{{Form::text('cvv_number','',['class'=>'form-control authorizePayment','id'=>'cvv_number','placeholder'=>'Card CVV Number'])}}
							</div>
							<div class="clearfix"></div>
							<button type="submit" class="btn btn-default pay_authorize">Pay</button>
						{{Form::close()}}
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
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
							amount: { total: '{{\session()->get("carttotal.total")}}', currency: 'USD' },
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
							$(location).attr('href', '{{url("uploads")}}/'+data.orderId+'?msg=This transaction has been approved.')
						}
					}
				});
			});
		},
		onCancel: function(data) {
			alert('The payment was cancelled!,Please try again.');
		}

	}, '#paypal-button-container');
	
	$('.check').click(function(){
		$.ajax({
			url:'{{url("saveOrder")}}',
			type:'post',
			dataType:'json',
			data:{'paymentID':"paypal123456"},
			success:function(data){
				if(data.status == 'success'){
					//$(location).attr('href', '{{url("uploads")}}/'+data.orderId)
				}
			}
		});
	});
	
	$('input[name="optionsPayment"]').change(function(){
		if($(this).val() == 'paypal'){
			$('.paypal_payment').removeClass('hide');
			$('.credit_card_payment').slideUp();
			setTimeout(function(){ $('.paypal_payment').slideDown(); },500);
		}else{
			$('.credit_card_payment').removeClass('hide');
			$('.paypal_payment').slideUp();
			setTimeout(function(){ $('.credit_card_payment').slideDown(); },500);
		}
	});
	
	$("form.authorize_form").submit(function(e){
        e.preventDefault();
		var on = 1;
		
		$('.authorizePayment').each( function (index, data) {
			if($(this).attr('type') == 'text'){
				$(this).closest('div').removeClass('has-error');
				$($(this)).nextAll().remove();
				if($(this).val() == ''){
					on = 0;
					$(this).closest('div').addClass('has-error');
					$("<span class='help-block'>This field is required</span>").insertAfter($(this));
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
		var msg = '';
		if(on == 1){
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
					} else {

                        // declined transaction - save order anyway so team can follow up
                        if (data.transaction.message == 'This transaction has been declined') {
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
						$('.error_msg').html(data.message+'('+data.transaction.message+')');
					}
				}
			});
		}
    });
	
	$( ".date-picker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		minDate:0,
	});
</script>
@endsection