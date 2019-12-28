@extends('layouts.pdf_layout')
@section('content')
&nbsp;
<section class="invoice">
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
						<h3 class="text-right">Estimate #E-{{$order->id}}</h3>
					@else
						<h3 class="text-right">Invoice #{{$order->id}}</h3>
					@endif
					<div class="date text-right">
						<b>Date:</b> {{date('m/d/Y',strtotime($order->created_at))}}<br>
					</div>
				</div>
			</div>
			<div class="row invoice-info main_print_div">
				<div class="col-md-3 col-sm-6 col-xs-12 invoice-col billing_add_div">
					<strong>Billing Address.</strong>
					<div class="col-xs-12 add_box">
						<address class="col-xs-12">
						@if($order->orderAddress->billing_company_name !="")
						<strong>{{$order->orderAddress->billing_company_name}}</strong><br/>
						@endif

						@if($order->orderAddress->billing_fname != '' and $order->orderAddress->billing_lname != '')
							<strong>{{$order->orderAddress->billing_fname.' '.$order->orderAddress->billing_lname}}</strong><br/>
						@endif

						@if($order->orderAddress->billing_add1 != '' and $order->orderAddress->billing_add2 != '')
							{{$order->orderAddress->billing_add1}}<br/>{{$order->orderAddress->billing_add2}}<br>
						@else
							{{$order->orderAddress->billing_add1}}<br/>
						@endif

						{{$order->orderAddress->billing_city.', '.$order->orderAddress->billing_state.' '.$order->orderAddress->billing_zipcode . ' ' . $order->orderAddress->billing_country}}

						@if($order->orderAddress->billing_phone_number !="")
							<br/>{{$order->orderAddress->billing_phone_number}}
						@endif
						</address>
					</div>
				</div>
				@if($order->multiple_shipping == 0)
					<div class="col-md-3 col-sm-6 col-xs-12 invoice-col shipping_add_div">
						<strong>Ship To</strong>
						<div class="col-xs-12 add_box">
							<address class="col-xs-12">
								@if($order->orderAddress->shipping_company_name !="")
									<strong>{{$order->orderAddress->shipping_company_name}}</strong><br/>
								@endif

								@if($order->orderAddress->shipping_fname != '' and $order->orderAddress->shipping_lname != '')
									<strong>{{$order->orderAddress->shipping_fname.' '.$order->orderAddress->shipping_lname}}</strong><br/>								
								@endif

								@if($order->orderAddress->shipping_ship_in_care != '')
									<strong>Care of: </strong>{{$order->orderAddress->shipping_ship_in_care}}<br/>
								@endif

								@if($order->orderAddress->shipping_phone_number != '')
									{{$order->orderAddress->shipping_phone_number}}<br/>
								@endif

								<strong>{{$order->customer->fname.' '.$order->customer->lname}}</strong><br/>{{$order->orderAddress->shipping_add1}}<br/>{{$order->orderAddress->shipping_add2}}<br>

								City: {{$order->orderAddress->shipping_city}}<br>
								State: {{$order->orderAddress->shipping_state}}<br>
								Zipcode: {{$order->orderAddress->shipping_zipcode}}<br>
								Country: {{$order->orderAddress->shipping_country}}<br>

								@if($order->orderProduct[0]->tracking_id !="")
								Tracking : {{$order->orderProduct[0]->tracking_id}}<br/>
								Shipped Via : {{$order->orderProduct[0]->shipping_career}}<br/>
								@endif
							</address>
						</div>
					</div>
				@else
					<div class="col-md-3 col-sm-6 col-xs-12 invoice-col">
						<strong>Ship To</strong>
						<div class="col-xs-12 add_box">
							{{$order->customer->fname.' '.$order->customer->lname}}<br/>
							<span style="color:red">Split Shipment-See Below</span>
						</div>
					</div>
				@endif
				<div class="col-md-6 col-sm-6 col-xs-12 invoice-col">
					<br/>
					<div class="row form-group">
						{{ Form::label('terms', 'Terms',array('class'=>'col-xs-5 form-control-label'))}}
						<div class="col-xs-6">
                            <?php
                            $terms = config('constants.terms');
                            if(!empty($order->terms) and array_key_exists($order->terms,$terms)){
                                echo $terms[$order->terms];
                            }
                            ?>
						</div>
					</div>
					
					<div class="row form-group">
						{{ Form::label('representative', 'Representative',array('class'=>'col-xs-5 form-control-label'))}}
						<div class="col-xs-6">
							<?php
								if(array_key_exists($order->agent_id,$agents)){
									echo $agents[$order->agent_id];
								}
							?>
						</div>
					</div>
					@if($order->payment_status != 0)
					<div class="row form-group">
						{{ Form::label('', 'Payment By',array('class'=>'col-xs-6 form-control-label'))}}
						<div class="col-xs-6">
							@php
								if($order->payment_method == 'paypal')
									echo "Paypal";
								elseif($order->payment_method == 'authorized')
									echo "Credit Card";
								else
									echo "Pay By Invoice";
							@endphp
						</div>
					</div>
					<div class="clearfix"></div>
						@if(!empty($order->payment_id))
						<div class="row form-group">
							{{ Form::label('', 'Payment ID',array('class'=>'col-xs-6 form-control-label'))}}
							<div class="col-xs-6">{{$order->payment_id}}</div>
						</div>
						@endif
					@endif
				</div>
			</div>
			<div class="row">
				<table class="table table-condensed" style="margin-left:20px">
					<thead>
						<tr>
							<th class="nowrap">Description</th>
							<th class="nowrap">Qty:</th>
							<th class="nowrap">Due Date:</th>
							<!--<th class="nowrap">Rate:</th>
							<th class="nowrap">Gross Total:</th>
							<th class="nowrap">Qty Discount:</th>-->
							<th class="nowrap">Total:</th>
							@if($order->multiple_shipping != 0)
							<th class="nowrap">Ship To Address:</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach($order->orderProduct as $val)
							<tr>
								<td class="nowrap" style="width:25%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 product_options_{{$val->item_id}}">
											<strong>{{$val->product->name}}.</strong>
											<div class="col-xs-12">
											@foreach($order->orderProductOptions as $options)
												@if($val->product_id == $options->product_id)
													@if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height')
													{{$options->custom_option_name.'(ft)'}}: {{$options->value}}<br/>
													@else
														{{$options->custom_option_name}}: {{$options->value}}<br/>
													@endif	
												@endif
											@endforeach
											</div>
										</div>
										<div class="col-xs-1 no-padding">
											<i class="fa fa-pencil-square-o options pull-right" order-id="{{$order->id}}" data-type='product_options' order-option="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-qty="{{$val->qty}}"></i>
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
										<div class="clearfix"></div>
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
									</div>
								</td>
								<td class="nowrap" style="width:10%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 no-padding div_qty_{{$val->item_id}}">
											{{$val->qty}}
										</div>
										<div class="col-xs-1 no-padding div_qty_edit_{{$val->item_id}}">
											<i class="fa fa-pencil-square-o product_values pull-right"  order-id="{{$order->id}}" data-type='qty' order-option="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->qty}}"></i>
										</div>
									</div>
								</td>
								<td class="nowrap" style="width:10%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 no-padding">
											{{ (!empty($val->due_date))? $val->due_date:'Not Set'}}
										</div>
									</div>
								</td>
								<?php /*<td class="nowrap" style="width:15%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 no-padding div_rate_{{$val->item_id}}">
											${{number_format(number_format($val->price_default,2,'.',''),2)}}
										</div>
										<div class="col-xs-1 no-padding div_rate_edit_{{$val->item_id}}">
											<i class="fa fa-pencil-square-o product_values pull-right"  order-id="{{$order->id}}" data-type='rate' order-option="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->price_default}}"></i>
										</div>
									</div>
								</td>
								<td class="nowrap" style="width:15%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 no-padding div_gross_total_{{$val->item_id}}">
											${{number_format(number_format($val->gross_total,2,'.',''),2)}}
										</div>
									</div>
								</td>
								<td class="nowrap" style="width:15%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 no-padding div_qty_discount_{{$val->item_id}}">
											${{number_format(number_format($val->qty_discount,2,'.',''),2)}}
										</div>
									</div>
								</td>*/ ?>
								<td class="nowrap" style="width:15%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 no-padding div_total_{{$val->item_id}}">				${{number_format(number_format($val->total,2,'.',''),2)}}
										</div>
									</div>
								</td>
								@if($order->multiple_shipping != 0)
								<td class="nowrap" style="width:30%">
									<div class="col-xs-12 add_box shipping_add_{{$val->item_id}}_div">
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
												<strong>Care of: </strong>{{$val->shipping_ship_in_care}}<br/>
											@endif											
											City: {{$val->shipping_city}}<br>
											State: {{$val->shipping_state}}<br>
											Zipcode: {{$val->shipping_zipcode}}<br>
											Country: {{$val->shipping_country}}<br>
											<b>Shipping type</b> : {{config('constants.Shipping_option.'. $order->shipping_option)}}<br>

											@if($val->tracking_id !="")
												Tracking : {{$val->tracking_id}}<br/>
												Shipped Via : {{$val->shipping_career}}<br/>
											@endif
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
				
					<div class="col-xs-6 table-responsive" style="margin-top: 15px;width:45%;float:right">
						@php
							$tax_value = number_format(number_format((float)($order->sales_tax), 2, '.', ''),2);
						@endphp
						<table class="table">
							<tr>
								<th style="width:50%">Subtotal:</th>
								<td class="sub_total">${{number_format(number_format($order->sub_total,2,'.',''),2)}}</td>
							</tr>
							<tr>
								<th style="width:50%">Discount:</th>
								<td class="sub_total">${{number_format(number_format($order->discount,2,'.',''),2)}}</td>
							</tr>
							<tr>
								<th style="width:50%">Shipping:</th>
								<td class="sub_total">${{number_format(number_format($order->shipping_fee,2,'.',''),2)}}
								<strong>&nbsp;({{config('constants.Shipping_option.'. $order->shipping_option)}})</strong>	
								</td>
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
								<td class="main_total">${{number_format(number_format($order->total,2,'.',''),2)}}</td>
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
@endsection		