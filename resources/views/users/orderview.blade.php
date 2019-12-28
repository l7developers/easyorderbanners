@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Orders View</h2>
	</div>
</section>
<?php //pr($order->orderProduct);die;?>
<section class="innerpages">
	 <div class="container">
		@include('partials.front.account_nav_bar')
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-lg-12">
			 	<table class="table table-striped table-bordered table-hover orderview">
					@if(count($order)>=1)
						@php
							//pr($order->files);die;
							$status = config('constants.Order_status');
							//pr($status);
							$i = 1;
						@endphp
						<tr>
							<th>Order ID</th>
							<td>
								#{{$order->id}}
								<a href="{{url('order/print/'.$order->id)}}" target="_blank" class="printBtn"><i class="fa fa-print pull-right" style="font-size: 30px;"></i></a>
							</td>
						</tr>
						<tr>
							<th>Date</th>
							<td>{{date('m-d-Y',strtotime($order->created_at))}}</td>
						</tr>
						<tr>
								<th>Billing Address</th>
								<td>																
									@if($order->orderAddress->billing_company_name !="")
										{{$order->orderAddress->billing_company_name}}	<br/>
									@endif			
									
									@if($order->orderAddress->billing_fname != '')
										{{$order->orderAddress->billing_fname.' '.$order->orderAddress->billing_lname}}<br/>
									@endif									
									
									@if($order->orderAddress->billing_add1 != '' and $order->orderAddress->billing_add2 != '')
										{{$order->orderAddress->billing_add1}}<br/>{{$order->orderAddress->billing_add2}}	<br/>
									@elseif($order->orderAddress->billing_add1 != '')
										{{$order->orderAddress->billing_add1}}<br/>
									@endif									
									{!!$order->orderAddress->billing_city.', '.$order->orderAddress->billing_state.' '.$order->orderAddress->billing_zipcode.' '.$order->orderAddress->billing_country!!}
								</td>
							</tr>
						<tr>
							<th>Ship To</th>
							<td>
								@if($order->multiple_shipping == 0)
									@if($order->orderAddress->shipping_company_name !="")
										{{$order->orderAddress->shipping_company_name}}	<br/>
									@endif

									@if($order->orderAddress->shipping_fname != '')
										{{$order->orderAddress->shipping_fname.' '.$order->orderAddress->shipping_lname}}<br/>
									@endif

									@if($order->orderAddress->shipping_ship_in_care != '')
										<strong>Care of: </strong>{{$order->orderAddress->shipping_ship_in_care}}<br/>
									@endif

									@if($order->orderAddress->shipping_add1 != '' and $order->orderAddress->shipping_add2 != '')
										{{$order->orderAddress->shipping_add1}}<br/>{{$order->orderAddress->shipping_add2}}	<br/>
									@elseif($order->orderAddress->shipping_add1 != '')
										{{$order->orderAddress->shipping_add1}}<br/>
									@endif
									{!!$order->orderAddress->shipping_city.', '.$order->orderAddress->shipping_state.' '.$order->orderAddress->shipping_zipcode.' '.$order->orderAddress->shipping_country!!}
									@if($order->orderAddress->shipping_phone_number != '')
										<br/>{{$order->orderAddress->shipping_phone_number}}
									@endif
								@else
									<strong>{{$order->customer->fname.' '.$order->customer->lname}}</strong><br/>
									<span style="color:red">Split Shipment-See Below</span>
								@endif
							</td>
						</tr>
						@if($order->customer_po != '')
						<tr>
							<th>Customer PO #</th>
							<td>
								{{$order->customer_po}}
							</td>
						</tr>
						@endif
						<tr>
							<th>Order Products</th>
							<td>
								<table class="table table-bordered">
									<tr>
										<th>S.No.</th>
										<th>Description</th>
										
										@if($order->payment_status != 0)
											<th>Write a review</th>
										@endif
										
										<th>Quantity</th>
										<th>Total</th>
										<th>ArtWork Status</th>
										@if($order->multiple_shipping == 1)
										<th>Ship To</th>
										@endif									
										<th>Shipping</th>
									</tr>
									@foreach($order->orderProduct as $sno=>$val)
									<tr>
										<td>{{++$sno}}</td>
										<td>
											<li style="list-style: none"><strong>
											@if($val->product_name !="")		
												{{$val->product_name}}
											@else
												{{$val->product->name}}
											@endif
											</strong>
											</li>
											<!--{{$val->review}}-->
											<div class="col-xs-12">
											{!!$val->description!!}
											<ul style="margin-left: 25px">
											@foreach($order->orderProductOptions as $options)
												@if($val->id == $options->order_product_id)
													@if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height')
													{{$options->custom_option_name.'(ft)'}}: {{$options->value}}<br/>
													@else
														<li>	{{$options->custom_option_name}}: {{$options->value}}</li><br/>
													@endif	
												@endif
											@endforeach
											</ul>
											</div>
											<div class="clearfix"></div><br/>
											
											<div class="col-xs-12">
												<div class="col-sm-12">
													<strong>Due Date:</strong>
												
													@if(!empty($val->due_date))
														{{$val->due_date}}
													@else
														Not Set
													@endif
												</div>
											</div>
											<div class="clearfix"></div><br/>
											
											<div class="col-xs-12 div_project_{{$val->id}}">
												<div class="col-sm-12">
													<strong>Project Name:</strong>
												
												@if(!empty($val->project_name))
													{{$val->project_name}}
												@else
													None
												@endif</div>
											</div>
											<div class="clearfix"></div><br/>
											
											<div class="col-xs-12 div_project_{{$val->id}}">
												<div class="col-sm-12">
													<strong>Comment:</strong>
													@if(!empty($val->comments))
														{{$val->comments}}
													@else
														None
													@endif
												</div>
											</div>
											<div class="clearfix"></div><br/>
											<div class="col-sm-12">
												<div class="col-sm-2"><strong>Files:</strong></div>
												<div class="col-sm-10">
												@php
												$j = 0;
												foreach($order->files as $file){
													if($file->order_product_id == $val->id){
														//$name = explode('&$',$file->name);
												@endphp
														<li><a href='{{url("public/uploads/orders/".$file->name)}}' target="_blank">{{$file->name}}</a></li>
												@php
														$j++;
													}
												}
												if($j == 0){
													echo "None";
												}
												@endphp
												</div>
											</div>
										</td>
										@if($order->payment_status != 0)
										<td class="ratingcol">
											@if(empty($val->review))
												<a href="{{url('review/'.$order->id.'/'.$val->product->id)}}" class="doreview"><i class="fa fa-star"></i></a>
											@else
												<a href="#" class="doreview done"><i class="fa fa-star"></i></a>
											@endif
										</td>
										@endif
										<td>{{$val->qty}}</td>
										<td>${{priceFormat($val->total)}}</td>
										<td>
											@if($val->product->no_artwork_required != 1)
												@if($val->art_work_status != 0)
													<span style="background:{{config('constants.art_work_status_color.'.$val->art_work_status)}} !important;border-color:{{config('constants.art_work_status_color.'.$val->art_work_status)}} !important;    cursor: auto;" class="btn btn-xs btn-danger">
														{{ config('constants.art_work_status.'.$val->art_work_status)}}<br/>
														@if(!empty($val->art_work_date))
															{{$val->art_work_date}}
														@endif
													</span>
												@else
													File not uploaded.
												@endif
											@else
												No Art Work Required.
											@endif
										</td>
										@if($order->multiple_shipping == 1)	
											<td>												
												@if($val->shipping_company_name !="")
													{{$val->shipping_company_name}}<br/>
												@endif
												
												@if($val->shipping_fname != '')
													{{$val->shipping_fname.' '.$val->shipping_lname}}<br/>
												@endif												
												
												@if($val->shipping_add1 != '' and $val->shipping_add2 != '')
													{{$val->shipping_add1.','.$val->shipping_add2 }}<br>
												@elseif($val->shipping_add1 != '')
													{{$val->shipping_add1}}<br>	
												@endif
												
												{{$val->shipping_city.', '.$val->shipping_state.' '.$val->shipping_zipcode . ' ' . $val->shipping_country}}
											</td>
										@endif	
										<td>
											@if(!empty($val->shipping_type))
											<div class="col-xs-121">
												<div class="col-sm-121">
													<strong>{{($val->shipping_type == 3)?'Shipping Carrier':'Shipped Via'}}:</strong>{{$val->shipping_career}}</br>
													@if($val->shipping_type == 3)
													<strong>Tracking Number:</strong>{{$val->tracking_id}}
													@else
														<a href="{{$val->tracking_link}}" target="_blank" class='btn btn-xs btn-warning'>Track Shipment</a>
													@endif
												</div>
											</div>
											<div class="clearfix"></div><br/>
											@endif											
										</td>
									</tr>
									@endforeach
								</table>
							</td>
						</tr>
						<tr>
							<th>Sub-Total</th>
							<td>${{priceFormat($order->sub_total)}}</td>
						</tr>
						<tr>
							<th>Shipping</th>
							<td>${{priceFormat($order->shipping_fee)}}<strong>&nbsp;({{config('constants.Shipping_option.'.$order->shipping_option)}})</strong></td>
						</tr>
						@if(priceFormat($order->discount) > 0)
						<tr>
							<th>Discount</th>
							<td>${{priceFormat($order->discount)}}</td>
						</tr>
						@endif
						@if($order->sales_tax > 0)
						<tr>
							<th>Sales Tax @ {{config('constants.sales_tax')}}%:</th>
							<td>${{priceFormat($order->sales_tax)}}</td>
						</tr>
						@endif
						<tr>
							<th>Total</th>
							<td>${{priceFormat($order->total)}}</td>
						</tr>
						<tr>
							<th>Payment</th>
							<td>
                                <b>Payment Status: </b>
                                @php
                                    echo \App\Helpers\PaymentHelper::getPaymentStatus($order->payment_status);
                                @endphp
                                @if($order->payment_status == 0)
                                    <a href="{{url('order-payment/'.$order->id)}}" class="btn btn-xs btn-success">Make Payment</a>
                                @endif
                                <br/><b>Payment Type: </b>
                                @php
                                    echo \App\Helpers\PaymentHelper::getPaymentMethod($order->payment_method);
                                @endphp
                                <br/>
                                @if(!empty($order->payment_id))
                                    <b>Payment ID : </b> #{{$order->payment_id}}
                                @endif
							</td>
						</tr>
					@endif
				</table>
		 	</div>
	 	</div>
	 </div>
</section>

<script>
	@if(!empty($print))
		/* var printWindow = window.open(
			'{{url("order/print/".$id)}}', 
			'Print', 
			'left=200', 
			'top=200', 
			'width=950', 
			'height=500', 
			'toolbar=0', 
			'resizable=0'
		);
		printWindow.addEventListener('load', function() {
			printWindow.print();
			printWindow.close();
		}, true); */
		
		window.open('{{url("order/print/".$id)}}','POPUP WINDOW TITLE HERE','width=900,height=800').print();
	@endif
</script>
@endsection