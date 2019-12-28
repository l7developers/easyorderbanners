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
					@if($order->customer_status == 0)
						<h3 class="text-right">Estimate ID #{{$order->id}}</h3>
					@else
						<h3 class="text-right">Order ID #{{$order->id}}</h3>
					@endif
					@if($order->customer_po != '')
					<div class="date text-right">
						<b>Customer PO #</b> {{$order->customer_po}}<br>
					</div>
					@endif
					<div class="date text-right">
						<b>Date:</b> {{date('m-d-Y',strtotime($order->created_at))}}<br>
					</div>
				</div>
			</div>
			<div class="row invoice-info main_print_div">
				<div class="col-md-3 col-sm-6 col-xs-12 invoice-col billing_add_div">
					<strong>Billing Address</strong>
					<div class="col-xs-12 add_box">
						@if($order->orderAddress->billing_city !="" && $order->orderAddress->billing_state !="" && $order->orderAddress->billing_zipcode !="")
							<address class="col-xs-12">
							@if($order->orderAddress->billing_company_name !="")
								{{$order->orderAddress->billing_company_name}}<br/>
							@endif
							
							@if($order->orderAddress->billing_fname != '')
								{{$order->orderAddress->billing_fname.' '.$order->orderAddress->billing_lname}}<br/>						
							@endif	

							@if($order->orderAddress->billing_add1 != '' and $order->orderAddress->billing_add2 != '')
								{{$order->orderAddress->billing_add1}}<br/>{{$order->orderAddress->billing_add2 }}<br>
							@elseif($order->orderAddress->billing_add1 != '')
								{{$order->orderAddress->billing_add1}}<br>	
							@endif
													
							{{$order->orderAddress->billing_city.', '.$order->orderAddress->billing_state.' '.$order->orderAddress->billing_zipcode . ' '. $order->orderAddress->billing_country}}<br/>

							</address>
						@else
							Billing Address Not Available 
						@endif
					</div>
				</div>
				@if($order->multiple_shipping == 0)
					<div class="col-md-3 col-sm-6 col-xs-12 invoice-col shipping_add_div">
						<strong>Ship To</strong>
						<div class="col-xs-12 add_box">
							@if($order->orderAddress->shipping_city !="" && $order->orderAddress->shipping_state !="" && $order->orderAddress->shipping_zipcode !="")
								<address class="col-xs-12">	
									@if($order->orderAddress->shipping_company_name !="")
										{{$order->orderAddress->shipping_company_name}}<br/>
									@endif
									
									@if($order->orderAddress->shipping_fname != '')
										{{$order->orderAddress->shipping_fname.' '.$order->orderAddress->shipping_lname}}<br/>						
									@endif

									@if($order->orderAddress->shipping_ship_in_care != '')
										<strong>Care of: </strong>{{$order->orderAddress->shipping_ship_in_care}}<br/>
									@endif
									
									@if($order->orderAddress->shipping_add1 != '' and $order->orderAddress->shipping_add2 != '')
										{{$order->orderAddress->shipping_add1}}<br>
										{{$order->orderAddress->shipping_add2 }}<br>
									@elseif($order->orderAddress->shipping_add1 != '')
										{{$order->orderAddress->shipping_add1}}<br>	
									@endif

									{{$order->orderAddress->shipping_city.', '.$order->orderAddress->shipping_state.' '.$order->orderAddress->shipping_zipcode . ' ' . $order->orderAddress->shipping_country}}

									@if($order->orderAddress->shipping_phone_number != '')
										<br/>{{$order->orderAddress->shipping_phone_number}}
									@endif
								</address>
							@else
								Shipping Address Not Available 
							@endif
						</div>
					</div>
				@else
					<div class="col-md-3 col-sm-6 col-xs-12 invoice-col">
						<strong>Ship To.</strong>
						<div class="col-xs-12 add_box">
							{{$order->customer->fname.' '.$order->customer->lname}}<br/>
							<span style="color:red">Split Shipment-See Below</span>
						</div>
					</div>
				@endif
				<div class="col-md-6 col-sm-6 col-xs-12 invoice-col">
					@if(!empty($order->terms))
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
					@endif
					
					@if(array_key_exists($order->agent_id,$agents))
					<div class="row form-group">
						{{ Form::label('representative', 'Representative',array('class'=>'col-xs-5 form-control-label'))}}
						<div class="col-xs-6">{{$agents[$order->agent_id]}}</div>
					</div>
					@endif
					
					@if($order->payment_status != 0)
					<div class="row form-group">
						{{ Form::label('', 'Payment By',array('class'=>'col-xs-5 form-control-label'))}}
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
							{{ Form::label('', 'Payment ID',array('class'=>'col-xs-5 form-control-label'))}}
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
							<th class="" style="width:{{($order->multiple_shipping != 0)?40:55}}%">Description</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">Qty:</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">Price:</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">Discount:</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">Total:</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?10:25}}%">Tracking Number:</th>
							@if($order->multiple_shipping != 0)
							<th class="nowrap" style="width:30%">Ship To Address:</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach($order->orderProduct as $val)
							@php
								$colspan = 6;
								if($order->multiple_shipping != 0)
									$colspan++;
							@endphp
							<tr>
								<td class="" style="width:{{($order->multiple_shipping != 0)?40:55}}%">
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
												<?php /* @if($val->product_id == $options->product_id) */ ?>
												@if($val->id == $options->order_product_id)
													@if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height')
													<li>{{$options->custom_option_name.'(ft)'}}: {{$options->value}}</li>
													@else
														<li>{{$options->custom_option_name}}: {{$options->value}}</li>
													@endif	
												@endif
											@endforeach
											</div>
										</div>
										<div class="col-xs-1 no-padding">
											<i class="fa fa-pencil-square-o options pull-right" order-id="{{$order->id}}" data-type='product_options' order-option="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-qty="{{$val->qty}}"></i>
										</div>
									</div>
								</td>
								<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 no-padding div_qty_{{$val->item_id}}">
											{{$val->qty}}
										</div>
										<div class="col-xs-1 no-padding div_qty_edit_{{$val->item_id}}">
											<i class="fa fa-pencil-square-o product_values pull-right"  order-id="{{$order->id}}" data-type='qty' order-option="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->qty}}"></i>
										</div>
									</div>
								</td>
								<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 no-padding div_total_{{$val->item_id}}">
											${{priceFormat($val->price)}}
										</div>
									</div>
								</td>
								<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">
									<div class="col-xs-12 no-padding">
										<div class="col-xs-11 no-padding div_total_{{$val->item_id}}">
											${{priceFormat($val->qty_discount)}}
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
								
								<td class="" style="width:{{($order->multiple_shipping != 0)?10:25}}%">
									@if($val->tracking_id !="")
										<b>Tracking : </b>{{$val->tracking_id}}<br/>
										<b>Shipped Via : </b>{{$val->shipping_career}}<br/>
									@else
										Not-Set
									@endif
								</td>
								
								@if($order->multiple_shipping != 0)
								<td class="" style="width:30%">
									<div class="col-xs-12 add_box shipping_add_{{$val->item_id}}_div">
										<address class="col-xs-12">
											@if($val->shipping_company_name !="")
												{{$val->shipping_company_name}}<br/>
											@endif
											
											@if($val->shipping_fname != '')
												{{$val->shipping_fname.' '.$val->shipping_lname}}<br/>											
											@endif

											@if($val->shipping_ship_in_care != '')
												<strong>Care of: </strong>{{$val->shipping_ship_in_care}}<br/>
											@endif

											@if($val->shipping_add1 != '' and $val->shipping_add2 != '')
												{{$val->shipping_add1}}<br/>{{$val->shipping_add2 }}<br>
											@elseif($val->shipping_add1 != '')
												{{$val->shipping_add1}}<br>	
											@endif
																						
											{{$val->shipping_city.', '.$val->shipping_state.' '.$val->shipping_zipcode . ' ' . $val->shipping_country}}
											<br/>
											<b>Shipping type</b> : {{config('constants.Shipping_option.'.$order->shipping_option)}}<br>
										</address>
									</div>
								</td>
								@endif
							</tr>
							<tr class="bordernone">
								<td colspan="{{$colspan}}">
									<div class="col-xs-12">
										<strong>Project Name:</strong>
									
										@if(!empty($val->project_name))
											{{$val->project_name}}
										@else
											None
										@endif
									</div>
									<div class="clearfix"></div><br/>
									<div class="col-xs-12">
										<strong>Comment:</strong>
										@if(!empty($val->comments))
											{{$val->comments}}
										@else
											None
										@endif
									</div>
									<div class="clearfix"></div><br/>
									@if($val->tflow_job_id >= 1 && $val->art_work_status==6)
										<div class="col-xs-12">
											<strong>ArtWork File : </strong>{{'http://108.61.143.179:9016/application/job/'.$val->tflow_job_id.'/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT'}}
										</div>
									@endif
								</td>
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
							$tax_value = priceFormat($order->sales_tax);
						@endphp
						<table class="table">
							<tr>
								<th style="width:50%">Subtotal:</th>
								<td class="sub_total">${{priceFormat($order->sub_total)}}</td>
							</tr>
							
							@if(!empty($order->discount) and $order->discount > 0)
							<tr>
								<th style="width:50%">Discount:</th>
								<td class="sub_total">${{priceFormat($order->discount)}}</td>
							</tr>
							@endif
							
							<tr>
								<th style="width:50%">Shipping:</th>
								<td class="sub_total">
									<span style="float:left;">${{priceFormat($order->shipping_fee)}}</span>
									<strong style="float:left;">&nbsp;({{config('constants.Shipping_option.'. $order->shipping_option)}})</strong>
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
@endsection		