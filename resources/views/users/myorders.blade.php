@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>My Orders</h2>
	</div>
</section>

@if(isset($_GET['msg']))
<section class="product_cart" style="margin-bottom: 62px;">
	<div class="container">
		<div class="cart_tab" style="margin-bottom: 0px;">
			<div class="text-success"><h3>{{$_GET['msg']}}</h3></div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="clt_review">
					<h3>Your Order submitted Successfully!</h3>
					<em> <b>Thank you for order !</b></em>
				</div>
			</div>
		</div>
	</div>
</section>
@endif

<section class="innerpages">
	 <div class="container">
		@include('partials.front.account_nav_bar')
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-lg-12">
			 	<table class="table table-striped table-bordered table-hover">
					<thead class="thead-dark">
						<tr>
							<th scope="col">#ID</th>
							<th scope="col" width="100px">Date</th>
							<th scope="col">Products</th>
							<th scope="col">Write a review</th>
							<th scope="col">Total</th>
							<th scope="col">#PaymentID</th>					
							<th scope="col">ArtWork</th>							
							<th scope="col">Action</th>
							<th scope="col">Shipping</th>
						</tr>
					</thead>
					<tbody>
						@if(count($orders)>=1)
							@php
								$status = config('constants.Order_status');
							@endphp
							@foreach($orders as $order)
								@php
								$FilesNames = [];
									foreach($order->files as $v){
										$FilesNames[$v['order_product_id']][$v['side']] = $v['name'];
									}									
								@endphp
								<tr>
									<td>
										@if($order->payment_status == 0)
											#E-{{$order->id}}
										@else
											#{{$order->id}}
										@endif
									</td>
									<td>{{date('m-d-Y',strtotime($order->created_at))}}</td>
									<td>
										@php
										$no_artwork_required = array();
										foreach($order->products as $product){
											if($product->product->no_artwork_required != 1){
												$no_artwork_required[] = $product->product->id;
											}
										@endphp
											<li>
												@if($product->product_name !="")		
													{{$product->product_name}}.
												@else
													{{$product->product->name}}.
												@endif
												
												<br/>												
											</li>
										@php
										}
										@endphp
									</td>
									<td class="ratingcol">
										@if($order->payment_status != 0)
											@foreach($order->products as $product)
												@if(empty($product->review))
													<a href="{{url('review/'.$order->id.'/'.$product->product_id)}}" class="doreview"><i class="fa fa-star"></i></a><br/>
												@else
													<a href="#" class="doreview done"><i class="fa fa-star"></i></a><br/>
												@endif
											@endforeach
										@endif
									</td>
									<td>${{priceFormat($order->total)}}</td>
									<td>
										@if($order->payment_status==0)
											<div class="badge badge-danger">Unpaid</div><br/>
											<a href="{{url('order-payment/'.$order->id)}}" class="btn btn-xs btn-success">Make Payment</a>
										@else
											<b>Payment Type : </b>
											@php
											if($order->payment_method=='authorized')
												echo 'Credit Card';
											else if($order->payment_method=='pay_by_invoice')
												echo 'Pay by Invoice';
											else
												echo ucwords(str_replace('_',' ',$order->payment_method));
											@endphp
											<br/>
											@if(!empty($order->payment_id))
											<div class="badge badge-primary"><b>Payment ID : </b> #{{$order->payment_id}}</div>
											@endif
										@endif
									</td>
									@php
									$file_count = count($order->files);
									$upload_file_exist = false;
									$counter = count($no_artwork_required);
									@endphp
									
									<?php /* @if(count($FilesNames) > 0 && count($FilesNames) <= $counter)
									<td>
										<div class="clearfix"></div>
										<a href="{{url('uploads/'.$order->id)}}" class='btn btn-xs btn-success'>Proceed</a>
									</td>
									@elseif($counter > 0 && count($FilesNames) == 0)
									<td>
										<div class="clearfix"></div>
										<a href="{{url('uploads/'.$order->id)}}" class='btn btn-xs btn-primary'>Upload</a>
									</td>
									@else
										<td></td>
									@endif */ ?>

									@if($counter > 0 && count($FilesNames) < $counter)
									<td>
										<div class="clearfix"></div>
										<a href="{{url('uploads/'.$order->id)}}" class='btn btn-xs btn-primary'>Upload</a>
									</td>
									@else
										<td></td>
									@endif
									
									<td class="actioncol">
										<a href="{{url('order/view/'.$order->id)}}" class='btn btn-xs btn-info'>View</a>
										<a href="{{url('order/print/'.$order->id)}}" target="_blank" class='btn btn-xs btn-default btn-gray'>Print</a>
									</td>
									<td>
										@foreach($order->products as $product)
											@if($product->shipping_type != 3 && !empty($product->tracking_id))
													<a href="{{$product->tracking_link}}" target="_blank" class='btn btn-xs btn-warning trackship'>Track Shipment</a><br/>
											@elseif($product->shipping_type == 3)
												<b>Shipping Carrier : </b>{{$product->shipping_career}}<br/>
												<b>Tracking Number : </b>{{$product->tracking_id}}<br/>
											@endif
										@endforeach	
										<?php /*
										@if($order->status == 13)
											@if(empty($order->order_review))
												<a href="{{url('order/review/'.$order->id)}}" class=''><i class="fa fa-thumbs-up" aria-hidden="true" style="color:#f39c12"></i></a>
											@else
												<i class="fa fa-thumbs-up" aria-hidden="true" style="color:#d2d6de;"></i>
											@endif
										@endif */ ?>

									</td>
								</tr>
						@endforeach 
						@else
							<tbody>
								<tr>
									<td colspan="8"><center><b>No Data Found here</b></center></td>
								</tr>
							</tbody>
						@endif
				</table>
				<div class="pull-left">  {{ $orders->links() }} </div>
		 	</div>
	 	</div>
	 </div>
</section>
@endsection