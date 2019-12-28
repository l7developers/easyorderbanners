@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Order Detail</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/order/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
			</div>
		</div>
	</div>
</section>

<section class="invoice">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="row">
					<div class="col-xs-12">
						<h2 class="page-header">
							<i class="fa fa-globe"></i> {{config('constants.SITE_NAME')}}
						</h2>
					</div>
				</div>
				<div class="row invoice-info">
					<div class="col-sm-4 invoice-col">
						<address>
						<strong>Billing Address.</strong><br>
						{{$order->orderAddress->billing_add1}}<br/>{{$order->orderAddress->billing_add2}}<br>
						Zipcode: {{$order->orderAddress->billing_zipcode}}<br>
						City: {{$order->orderAddress->billing_city}}<br>
						State: {{$order->orderAddress->billing_state}}<br>
						Country: {{$order->orderAddress->billing_country}}<br>
						</address>
					</div>
					@if($order->multiple_shipping == 0)
					<div class="col-sm-4 invoice-col">
						<address>
						<strong>Shipping Address</strong><br>
						{{$order->orderAddress->shipping_add1}}<br/>{{$order->orderAddress->shipping_add2}}<br>
						Zipcode: {{$order->orderAddress->shipping_zipcode}}<br>
						City: {{$order->orderAddress->shipping_city}}<br>
						State: {{$order->orderAddress->shipping_state}}<br>
						Country: {{$order->orderAddress->shipping_country}}<br>
						</address>
					</div>
					@endif
					<div class="col-sm-4 invoice-col pull-right">
						<b>*Order ID:</b> #{{$order->id}}<br>
						<b>Date:</b> {{date('d/m/Y',strtotime($order->created_at))}}<br>
						
						<!--<b>Payment :</b> <?php if($order->payment_status==1){ echo "Paid";}else{ echo "Unpaid";} ?><br/>-->
					</div>
				</div>
				<div class="row">
					<div style="width:100%;height:30px;overflow:scroll;" class="wrapper1">
						<table style="width:1800px"><tr><td>&nbsp;</td></tr></table>	
					</div>
					<div id="order_view" class="col-xs-12 table-responsive" style="width:100%;overflow:scroll;">
						<table class="table table-striped">
							<thead>
								<tr>
									<th class="nowrap">Qty</th>
									<th class="nowrap">Item Id</th>
									<th class="nowrap">Tracking Id</th>
									<th class="nowrap">Product</th>
									<th class="nowrap">Agent</th>
									<th class="nowrap">Designer</th>
									<th class="nowrap">Vendor</th>
									<th class="nowrap">Vendor Status</th>
									<th class="nowrap">Art Work Status</th>
									<th class="nowrap">Product Base Price</th>
									<th class="nowrap">Product Price</th>
									<th class="nowrap">Subtotal</th>
								</tr>
							</thead>
							<tbody>
							@foreach($order->orderProduct as $val)
								<tr>
									<td class="nowrap">{{$val->qty}}</td>
									<td class="nowrap">#{{$val->item_id}}</td>
									<td class="nowrap">
										@if(!empty($val->tracking_id))
											#{{$val->tracking_id}}
										@endif
									</td>
									<td class="nowrap">
										<b>{{$val->product->name}}</b>
										<div class="table-responsive">
											<table class="table">
												<tr>
													<th class="text-center" colspan="3">Product Options</th>
												</tr>
												<tr>
													<th>Name</th>
													<th>Value</th>
													<th>Price</th>
												</tr>
												@foreach($order->orderProductOptions as $options)
													@if($val->product_id == $options->product_id)
													<tr>
														<td class="nowrap">{{$options->custom_option_name}} </td>
														<td class="nowrap">{{$options->value}} </td>
														<td>
															@if(!empty($options->price) and $options->price > 0)
																${{$options->price}} 
															@endif
														</td class="nowrap">
													</tr>
													@endif
												@endforeach
												@if($order->multiple_shipping != 0)
													<tr>
														<th class="text-center" colspan="3">Product Shipping Address</th>
													</tr>
													<tr>
														<td class="nowrap">Address </td>
														<td class="nowrap" colspan="2">{{$val->shipping_add1}}<br/>{{$val->shipping_add2}} </td>
													</tr>
													<tr>
														<td class="nowrap">Zipcode </td>
														<td class="nowrap" colspan="2">{{$val->shipping_zipcode}} </td>
													</tr>
													<tr>
														<td class="nowrap">City </td>
														<td class="nowrap" colspan="2">{{$val->shipping_city}} </td>
													</tr>
													<tr>
														<td class="nowrap">State </td>
														<td class="nowrap" colspan="2">{{$val->shipping_state}} </td>
													</tr>
													<tr>
														<td class="nowrap">Country </td>
														<td class="nowrap" colspan="2">{{$val->shipping_country}} </td>
													</tr>
												@endif
											</table>
										</div>
									</td>
									<td class="nowrap">
										@if(!empty($order->agent))
											{{$order->agent->fname.' '.$order->agent->lname}}
										@else
											{{'Not Assign'}}
										@endif
									</td>
									<td class="nowrap">
										@if(!empty($val->designer))
											{{$val->designer->fname.' '.$val->designer->lname}}
										@else
											{{'Not Assign'}}
										@endif
									</td>
									<td class="nowrap">
										@if(!empty($val->vendor))
											{{$val->vendor->fname.' '.$val->vendor->lname}}
										@else
											{{'Not Assign'}}
										@endif
									</td>
									<td class="nowrap">
										@if($val->vendor_status != 0)
											{{config('constants.vendor_status.'.$val->vendor_status)}}
										@else
											{{'Not Set'}}
										@endif
									</td>
									<td class="nowrap">
										@if($val->art_work_status != 0)
											{{config('constants.art_work_status.'.$val->art_work_status)}}<br/>{{'('.$val->art_work_date.')'}}
										@else
											{{'Not Set'}}
										@endif
									</td>
									<td class="nowrap">${{$val->price_default}}</td>
									<td class="nowrap">${{$val->price}}</td>
									<td class="nowrap">${{$val->total}}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6 col-xs-12">
						<p class="lead">Customer Detail:</p>
						<div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
							<b>Name: </b>{{$order->customer->fname.' '.$order->customer->lname}}</br/>
							<b>Email: </b>{{$order->customer->email}}</br/>
							<b>Phone Number: </b>{{$order->customer->phone_number}}</br/>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<p class="lead">&nbsp;</p>
						<div class="table-responsive">
							<table class="table">
								<tr>
									<th style="width:50%">Subtotal:</th>
									<td>${{$order->sub_total}}</td>
								</tr>
								<tr>
									<th style="width:50%">Total:</th>
									<td>${{$order->sub_total}}</td>
								</tr>
								<!--<tr>
									<th>Tax (9.3%)</th>
									<td>${{(9.3*$order->sub_total)/100}}</td>
								</tr>
								<tr>
									<th>Shipping:</th>
									<td>$5.80</td>
								</tr>
								<tr>
									<th>Total:</th>
									<td>${{((9.3*$order->sub_total)/100)+5.80+$order->sub_total}}</td>
								</tr>-->
							</table>
						</div>
					</div>
				</div>
				<!--<div class="row no-print">
					<div class="col-xs-12">
						<a href="" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
					</div>
				</div>-->
			</div>
		</div>
	</div>
</section>	
<script>
	$(document).ready(function() {
		$(".wrapper1").scroll(function(){
			console.log($(".wrapper1").scrollLeft());
			$("#order_view").scrollLeft($(".wrapper1").scrollLeft());
		});
		$("#order_view").scroll(function(){
			$(".wrapper1").scrollLeft($("#order_view").scrollLeft());
		});
	});
</script>
@endsection		  