<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{(isset($pageTitle))?$pageTitle:"Easy Order Banners"}} </title>
    <!--<title>Easy Order Banner</title>-->
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
	
	@include('partials.admin.css')
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
	
	<link rel="icon" href="{{url('public/img/admin/logo.png')}}" type="image/png"/>
	
	@include('partials.admin.javascripts')
	<script>
		var SITE_NAME='<?php echo config('constants.SITE_NAME');?>';
		var SITE_URL='<?php echo config('constants.SITE_URL');?>';
	</script>
  </head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<div class="content-wrapper">
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
									<b>Date:</b> {{date('m-d-Y',strtotime($order->created_at))}}<br>
								</div>
							</div>
						</div>
						<div class="row invoice-info main_print_div">
							<div class="col-md-4 col-sm-6 col-xs-12 invoice-col billing_add_div" style="width:25%">
								<strong>Billing Address</strong>
								<div class="add_box">
									<address class="">
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

									{{$order->orderAddress->billing_city.', '.$order->orderAddress->billing_state.' '.$order->orderAddress->billing_zipcode . ' ' . $order->orderAddress->billing_country}}<br>

									@if($order->orderAddress->billing_phone_number != '')
										{{$order->orderAddress->billing_phone_number}}<br/>
									@endif
									</address>
								</div>
							</div>
							@if($order->multiple_shipping == 0)
								<div class="col-md-4 col-sm-6 col-xs-12 invoice-col shipping_add_div" style="width:25%">
									<strong>Ship To</strong>
									<div class="add_box">
										<address class="">						
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
												{{$order->orderAddress->shipping_add1}}<br/>{{$order->orderAddress->shipping_add2 }}<br>
											@elseif($order->orderAddress->shipping_add1 != '')
												{{$order->orderAddress->shipping_add1}}<br>	
											@endif
											
											{{$order->orderAddress->shipping_city.', '.$order->orderAddress->shipping_state.' '.$order->orderAddress->shipping_zipcode . ' ' . $order->orderAddress->shipping_country}}

											@if($order->orderAddress->shipping_phone_number != '')
												<br/>{{$order->orderAddress->shipping_phone_number}}
											@endif

											<!--@if($order->orderProduct[0]->tracking_id !="")
											Tracking : {{$order->orderProduct[0]->tracking_id}}<br/>
											Shipped Via : {{$order->orderProduct[0]->shipping_career}}<br/>
											@endif-->
										</address>
									</div>
								</div>
							@else
								<div class="col-md-4 col-sm-6 col-xs-12 invoice-col" style="width:25%">
									<strong>Ship To.</strong>
									<div class="add_box">
										{{$order->customer->fname.' '.$order->customer->lname}}<br/>
										<span style="color:red">Split Shipment-See Below</span>
									</div>
								</div>
							@endif
							<div class="col-md-4 col-sm-6 col-xs-12 invoice-col" style="width:45%">
								@if(!empty($order->terms))
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
								@endif
								
								@if(array_key_exists($order->agent_id,$agents))
									{{ Form::label('representative', 'Representative',array('class'=>'col-xs-5 form-control-label'))}}
									<div class="col-xs-6">{{$agents[$order->agent_id]}}</div>
									<div class="clearfix"></div>
								@endif
								
								@if($order->payment_status != 0)
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
							<table class="table table-condensed" style="margin-left:20px;width:100%;">
								<thead>
									<tr>
										<th class="" style="width:{{($order->multiple_shipping != 0)?45:55}}%">Description</th>
										<th class="" style="width:{{($order->multiple_shipping != 0)?5:7}}%">Qty:</th>
										<th class="" style="width:{{($order->multiple_shipping != 0)?10:8}}%">Due Date:</th>
										<th class="" style="width:{{($order->multiple_shipping != 0)?5:5}}%">Total:</th>
										<th class="" style="width:{{($order->multiple_shipping != 0)?5:20}}%">Tracking Number:</th>
										@if($order->multiple_shipping != 0)
										<th class="" style="width:25%">Ship To Address:</th>
										@endif
									</tr>
								</thead>
								<tbody>
									@foreach($order->orderProduct as $val)
										@php
											$colspan = 5;
											if($order->multiple_shipping != 0)
												$colspan++;
										@endphp
										<tr>
											<td class="" style="width:{{($order->multiple_shipping != 0)?45:55}}%">
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
													<div class="col-xs-1 no-padding">
														<i class="fa fa-pencil-square-o options pull-right" order-id="{{$order->id}}" data-type='product_options' order-option="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-qty="{{$val->qty}}"></i>
													</div>
												</div>
											</td>
											<td class="" style="width:{{($order->multiple_shipping != 0)?5:7}}%">
												<div class="col-xs-12 no-padding">
													<div class="col-xs-11 no-padding div_qty_{{$val->item_id}}">
														{{$val->qty}}
													</div>
													<div class="col-xs-1 no-padding div_qty_edit_{{$val->item_id}}">
														<i class="fa fa-pencil-square-o product_values pull-right"  order-id="{{$order->id}}" data-type='qty' order-option="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->qty}}"></i>
													</div>
												</div>
											</td>
											<td class="" style="width:{{($order->multiple_shipping != 0)?10:8}}%">
												<div class="col-xs-12 no-padding">
													<div class="col-xs-11 no-padding">
														{{ (!empty($val->due_date))? $val->due_date:'Not Set'}}
													</div>
												</div>
											</td>
											<td class="" style="width:{{($order->multiple_shipping != 0)?5:5}}%">
												<div class="col-xs-12 no-padding">
													<div class="col-xs-11 no-padding div_total_{{$val->item_id}}">
														${{number_format(number_format($val->total,2,'.',''),2)}}
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
											<td class="" style="width:25%">
												<div class="add_box shipping_add_{{$val->item_id}}_div">
													<address class="col-xs-12">
														@if($val->shipping_company_name !="")
															{{$val->shipping_company_name}}<br/>
														@endif
														
														@if($val->shipping_fname != '')
															{{$val->shipping_fname.' '.$val->shipping_lname}}<br/>
														@endif
														
														@if($val->shipping_add1 != '' and $val->shipping_add2 != '')
															{{$val->shipping_add1}}<br/>{{$val->shipping_add2 }}<br>
														@elseif($val->shipping_add1 != '')
															{{$val->shipping_add1}}<br>	
														@endif
														
														{{$val->shipping_city.', '.$val->shipping_state.' '.$val->shipping_zipcode . ' ' . $val->shipping_country}}<br/>
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
							
								<div class="col-xs-6 table-responsive" style="margin-top: 20px;width:45%;float:right">
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
											<td class="main_total">${{number_format(number_format($order->total,2,'.',''),2)}}</td>
										</tr>
									</table>
								</div>
							</div>
						</div>			
					</div>
				</div>
			</section>	
		</div>
	</div>
</body>
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