<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{(isset($pageTitle))?$pageTitle:"Easy Order Banners"}} </title>
	@yield('meta')        
	<link rel="icon" href="{{url('public/img/admin/logo.png')}}" type="image/png"/>
    <!-- CSS -->
	@include('partials.front.css')
	
	<!-- JavaScripts -->
	@include('partials.front.javascripts')

	@php
	echo get_setting('header_script');
	@endphp
	
</head>
<body>

	<section id="printableArea" class="invoice" style="width:99%;">
		<div class="row">
			<div class="col-xs-12">
				<div class="row page-header">
					<div class="col-sm-6 col-xs-12" style="width:50%;">
						<h2>
							<img src="{{URL::to('public/img/admin/logo.png')}}" alt="Logo"/>
						</h2>
					</div>
					<div class="col-sm-6 col-xs-12" style="width:50%;">
						@if($order->payment_status == 0)
							<h3 class="text-right">Estimate #{{$order->id}}</h3>
						@else
							<h3 class="text-right">Invoice #{{$order->id}}</h3>
						@endif
						<div class="date text-right">
							<b>Date:</b> {{date('m/d/Y',strtotime($order->created_at))}}<br>
						</div>
						@if($order->customer_po != '')
						<div class="date text-right">
							<b>Customer PO </b>#{{$order->customer_po}}<br>
						</div>
						@endif
					</div>
				</div>
				<div class="row invoice-info main_print_div">
					<div class="col-md-3 col-sm-6 col-xs-12 invoice-col billing_add_div" style="width:25%">
						<strong>Billing Address.</strong>
						<div class="add_box">
							<address class="">
							@if($order->orderAddress->billing_company_name !="")
							{{$order->orderAddress->billing_company_name}}<br/>
							@endif
							
							@if($order->orderAddress->billing_fname != '' and $order->orderAddress->billing_lname != '')
								{{$order->orderAddress->billing_fname.' '.$order->orderAddress->billing_lname}}<br/>
							@endif

							@if($order->orderAddress->billing_add1 != '' and $order->orderAddress->billing_add2 != '')
								{{$order->orderAddress->billing_add1}}<br/>{{$order->orderAddress->billing_add2}}<br/>
							@else
								{{$order->orderAddress->billing_add1}}<br/>
							@endif

							{{$order->orderAddress->billing_city.', '.$order->orderAddress->billing_state.' '.$order->orderAddress->billing_zipcode . ' ' . $order->orderAddress->billing_country}}

							</address>
						</div>
					</div>
					@if($order->multiple_shipping == 0)
						<div class="col-md-3 col-sm-6 col-xs-12 invoice-col shipping_add_div" style="width:25%">
							<strong>Ship To.</strong>
							<div class="add_box">
								<address class="">						
									@if($order->orderAddress->shipping_company_name !="")
										{{$order->orderAddress->shipping_company_name}}<br/>
									@endif
									
									@if($order->orderAddress->shipping_fname != '' and $order->orderAddress->shipping_lname != '')
										{{$order->orderAddress->shipping_fname.' '.$order->orderAddress->shipping_lname}}<br/>								
									@endif

									@if($order->orderAddress->shipping_ship_in_care != '')
										<strong>Care of: </strong>{{$order->orderAddress->shipping_ship_in_care}}<br/>
									@endif

									{{$order->orderAddress->shipping_add1}}<br/>{{$order->orderAddress->shipping_add2}}<br/>
									{{$order->orderAddress->shipping_city.','.$order->orderAddress->shipping_state.' '.$order->orderAddress->shipping_zipcode . ' ' . $order->orderAddress->shipping_country}}

									@if($order->orderAddress->shipping_phone_number !="")
										<br/>{{$order->orderAddress->shipping_phone_number}}
									@endif
								</address>
							</div>
						</div>
					@else
						<div class="col-md-3 col-sm-6 col-xs-12 invoice-col" style="width:25%">
							<strong>Ship To.</strong>
							<div class="add_box">
								{{$order->customer->fname.' '.$order->customer->lname}}<br/>
								<span style="color:red">Split Shipment-See Below</span>
							</div>
						</div>
					@endif
					<div class="col-md-6 col-sm-6 col-xs-12 invoice-col" style="width:45%">
						{{ Form::label('terms', 'Terms',array('class'=>'col-xs-5 form-control-label'))}}
						<div class="col-xs-6">
                            <?php
                            $terms = config('constants.terms');
                            if(!empty($order->terms) and array_key_exists($order->terms,$terms)){
                                echo $terms[$order->terms];
                            }
                            ?>
						</div>
						<div class="clearfix"></div>
						
						{{ Form::label('representative', 'Representative',array('class'=>'col-xs-5 form-control-label'))}}
						<div class="col-xs-6">
							<?php
								if(array_key_exists($order->agent_id,$agents)){
									echo $agents[$order->agent_id];
								}
							?>
						</div>
						<div class="clearfix"></div>
						
						@if($order->payment_status != 0)
						
							{{ Form::label('', 'Payment Status',array('class'=>'col-xs-5 form-control-label'))}}
							<div class="col-xs-6">
								@php
									echo \App\Helpers\PaymentHelper::getPaymentStatus($order->payment_status);
								@endphp
								@if($order->payment_status == 0)
									<a href="{{url('order-payment/'.$order->id)}}" class="btn btn-xs btn-success">Make Payment</a>
								@endif
							</div>
							<div class="clearfix"></div>
							{{ Form::label('', 'Payment Type',array('class'=>'col-xs-5 form-control-label'))}}
							<div class="col-xs-6">
								@php
									echo \App\Helpers\PaymentHelper::getPaymentMethod($order->payment_method);
								@endphp
							</div>
							<div class="clearfix"></div>
							@if(!empty($order->payment_id))
								{{ Form::label('', 'Payment ID',array('class'=>'col-xs-5 form-control-label'))}}
								<div class="col-xs-6">{{$order->payment_id}}</div>
								<div class="clearfix"></div>
							@endif
						@endif
					</div>
				</div>
				<div class="row">
					<table class="table table-condensed" style="margin-left:20px;width:95%;">
						<thead>
							<tr>
								<th class="" style="width:{{($order->multiple_shipping != 0)?45:50}}%">Description</th>
								<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:7}}%">Qty:</th>
								<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?10:8}}%">Due Date:</th>
								<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">Total:</th>
								<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:20}}%">Tracking Number:</th>
								@if($order->multiple_shipping != 0)
								<th class="nowrap" style="width:30%">Ship To Address:</th>
								@endif
							</tr>
						</thead>
						<tbody>
							@foreach($order->orderProduct as $val)
								<tr>
									<td class="" style="width:{{($order->multiple_shipping != 0)?45:50}}%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-11 product_options_{{$val->item_id}}">
												<strong>
												@if($val->product_name !="")		
													{{$val->product_name}}.
												@else
													{{$val->product->name}}.
												@endif
												</strong>
												<div class="col-xs-12">
												{!!$val->description!!}
												@foreach($order->orderProductOptions as $options)
													@if($val->product_id == $options->product_id)
														@if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height')
															<li>{{$options->custom_option_name.'(ft)'}}: {{$options->value}}</li>
														@else
															<li>{{$options->custom_option_name}}: {{$options->value}}</li>
														@endif	
													@endif
												@endforeach
												</div>
											</div>
										</div>
										<div class="clearfix"></div><br/>
										<div class="col-xs-12 div_project_{{$val->id}}">
											<div class="col-sm-4">
												<strong>Project Name:</strong>
											
											@if(!empty($val->project_name))
												{{$val->project_name}}
											@else
												None
											@endif</div>
										</div>
										<div class="clearfix"></div><br/>
										<div class="col-xs-12 div_project_{{$val->id}}">
											<div class="col-sm-4">
												<strong>Comment:</strong>
												@if(!empty($val->comments))
													{{$val->comments}}
												@else
													None
												@endif
											</div>
										</div>
									</td>
									<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:7}}%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-11 no-padding div_qty_{{$val->item_id}}">
												{{$val->qty}}
											</div>
										</div>
									</td>
									<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?10:8}}%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-11 no-padding">
												{{ (!empty($val->due_date))? $val->due_date:'Not Set'}}
											</div>
										</div>
									</td>
									<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-11 no-padding div_total_{{$val->item_id}}">
												${{priceFormat($val->total)}}
											</div>
										</div>
									</td>
							
									<td class="" style="width:{{($order->multiple_shipping != 0)?5:20}}%">
										@if($val->tracking_id !="")
											<b>Tracking : </b>{{$val->tracking_id}}<br/>
											<b>Shipped Via : </b>{{$val->shipping_career}}<br/>
										@else
											Not-Set
										@endif
									</td>
									
									@if($order->multiple_shipping != 0)
									<td class="nowrap" style="width:30%">
										<div class="add_box shipping_add_{{$val->item_id}}_div">
											<address class="col-xs-12">
												@if($val->shipping_company_name !="")
													<strong>{{$val->shipping_company_name}}</strong><br/>
												@endif
												@if($val->shipping_phone_number !="")
													{{$val->shipping_phone_number}}<br/>
												@endif	
												@if($val->shipping_fname != '' and $val->shipping_lname != '')
													<strong>{{$val->shipping_fname.' '.$val->shipping_lname}}</strong><br/>
												@endif
												{{$val->shipping_add1}}<br/>{{$val->shipping_add2}}<br>
												@if($val->shipping_ship_in_care !="")
													{{$val->shipping_ship_in_care}}<br/>
												@endif
												City: {{$val->shipping_city}}<br>
												State: {{$val->shipping_state}}<br>
												Zipcode: {{$val->shipping_zipcode}}<br>
												Country: {{$val->shipping_country}}<br>
											</address>
										</div>
									</td>
									@endif
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="row">
				
					<div class="col-xs-12" style="width:100%">
						<div class="col-xs-6 text-muted" style="margin-top: 15px;width:45%;">
							<p class="order_view_head" style="margin-bottom:0;font-size: 22px;color: #3f91e8;">Thank you for your business!</p>
							<h4><b>Easy Order Banners</b></h4>
							<div class="col-xs-12">
								<p>P.O.Box 432</p>
								<p>Hatfield,PA 19440</p><br/>
								<p>Phone: 800-920-9527</p>
								<p>Fax: 267-244-1056</p>
								<p>E-mail: info@easyorderbanners.com</p>
							</div>
						</div>
					
						<div class="col-xs-6 table-responsive" style="margin-top: 20px;width:45%;float:right">
							@php
								$tax_value = priceFormat($order->sales_tax);
							@endphp
							<table class="table">
								<tr>
									<th style="width:50%">Subtotal:</th>
									<td class="sub_total">${{priceFormat($order->sub_total)}}</td>
								</tr>
								<tr>
									<th style="width:50%">Discount:</th>
									<td class="sub_total">${{priceFormat($order->discount)}}</td>
								</tr>
								<tr>
									<th style="width:50%">Shipping:</th>
									<td class="sub_total">${{priceFormat($order->shipping_fee)}}</td>
								</tr>
								@if(!empty($tax_value) and $tax_value > 0)
								<tr>
									<th>Sales Tax @ {{config('constants.sales_tax')}}%:</th>
									<td class="tax">
										${{$tax_value}}
									</td>
								</tr>
								@endif
								<tr>
									<th>Total:</th>
									<td class="main_total">${{priceFormat($order->total)}}</td>
								</tr>
							</table>
						</div>
					</div>
				</div>			
			</div>
		</div>
	</section>	
	<style type="text/css">
	i.fa-pencil-square-o,
	i.fa-save
	{
		display:none;
	}
	.add_box 
	{
		border:none;
	}

	</style>
<script>
	print();
	setTimeout(function(){close();},200);
</script>

</body>
</html>