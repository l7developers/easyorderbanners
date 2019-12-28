@extends('layouts.admin_layout')
@section('content')

<!--    Jquery for top scroll and table scroll drag ------------>
<script src="{{ asset('public/js/admin/dragscroll.js') }}"></script>

<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Order/Estimate Details</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{($order->customer_status==0)?url('admin/order/estimates'):url('admin/order/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
				<a target="_blank" href="{{url('admin/order/print_view/'.$order->id)}}" class=" pull-right"><i class="fa fa-print" style="font-size: 30px;"></i></a>
				<a href="{{url('admin/order/order-mail/'.$order->id)}}" class="pull-right"><i class="fa fa-envelope" style="font-size: 30px;"></i></a>
				<a href="{{url('admin/order/createinvoice/'.$order->id)}}" class=" pull-right"><i class="fa fa-file-pdf-o" style="font-size: 30px;"></i></a>
				<a href="javascript:void(0)" class=" pull-right select_save_all" order-id="{{$order->id}}"><i class="fa fa-save" style="font-size: 30px;"></i></a>
			</div>
		</div>
	</div>
</section>

<section id="printableArea" class="invoice">
	<div class="row">
		<div class="col-xs-12">
			<div class="row page-header">
				<div class="col-sm-6 col-xs-12">
					<h2>
						<img src="{{URL::to('public/img/admin/logo.png')}}" alt="Logo"/>
					</h2>
				</div>
				<div class="col-sm-6 col-xs-12">
					@if($type == null)
						@if($order->payment_status == 0)
							<h3 class="text-right">Estimate #{{$order->id}}</h3>
						@else
							<h3 class="text-right">Invoice #{{$order->id}}</h3>
						@endif
					@else
						@php
							$po_code = explode('-',$type);
						@endphp
						<div class="form-group">
							<i class="btn btn-primary fa fa-save select_save pull-right" order-id="{{$order->id}}" order-PO="{{$type}}" data-type="PO"></i>
							<div class="col-sm-6 pull-right">
								<div class="input-group po_div">
									<span class="input-group-addon">PO</span>	{{Form::text('po',$po_code[1].'-'.$po_code[2],['class'=>'form-control','placeholder'=>'PO number','id'=>'po'])}}
								</div>
							</div>
							@if(isset($_GET['msg']))
								<div class="col-sm-12 pull-right text-right">
									<span class="text-success" style="font-size:15px">{{$_GET['msg']}}</span>
								</div>
								<div class="clearfix"></div>
							@endif
						</div>
						<br/><br/>
						<div class="clearfix"></div>
					@endif
					<div class="date text-right">
						<b>Date:</b> {{date('m-d-Y',strtotime($order->created_at))}}<br>
					</div>
				</div>
			</div>
			<div class="row invoice-info main_print_div">
				<div class="col-md-4 col-sm-6 col-xs-12 invoice-col billing_add_div">
					<strong>Billing Address</strong>
					<div class="col-xs-12 add_box">
						@if($order->orderAddress->billing_city !="" && $order->orderAddress->billing_state !="" && $order->orderAddress->billing_zipcode !="")
							<address class="col-xs-10">
							@if($order->orderAddress->billing_company_name !="")
								{{$order->orderAddress->billing_company_name}}<br/>
							@endif

							@if($order->orderAddress->billing_fname != '')
								{{$order->orderAddress->billing_fname.' '.$order->orderAddress->billing_lname}}<br/>
							@endif

							@if($order->orderAddress->billing_phone_number !="")
								{{$order->orderAddress->billing_phone_number}}<br/>
							@endif

							@if($order->orderAddress->billing_add1 != '' and $order->orderAddress->billing_add2 != '')
								{{$order->orderAddress->billing_add1}}<br/>{{$order->orderAddress->billing_add2 }}<br>
							@elseif($order->orderAddress->billing_add1 != '')
								{{$order->orderAddress->billing_add1}}<br>
							@endif

							{{$order->orderAddress->billing_city.', '.$order->orderAddress->billing_state.', '.$order->orderAddress->billing_zipcode}}<br>
							{{$order->orderAddress->billing_country}}<br>

							</address>
						@else
							<address class="col-xs-10">Billing Address Not Available</address>
						@endif
						<div class="col-xs-2"><i class="fa fa-pencil-square-o address pull-right" order-id="{{$order->id}}" data-type='billing_add' order-product-table-id="0"></i></div>
					</div>
				</div>
				@if($order->multiple_shipping == 0)
					<div class="col-md-4 col-sm-6 col-xs-12 invoice-col shipping_add_div">
						<strong>Ship To</strong>
						<div class="col-xs-12 add_box">
							@if($order->orderAddress->shipping_city !="" && $order->orderAddress->shipping_state !="" && $order->orderAddress->shipping_zipcode !="")
								<address class="col-xs-10">
									@if($order->orderAddress->shipping_company_name !="")
										{{$order->orderAddress->shipping_company_name}}<br/>
									@endif

									@if($order->orderAddress->shipping_fname != '' and $order->orderAddress->shipping_lname != '')
										{{$order->orderAddress->shipping_fname.' '.$order->orderAddress->shipping_lname}}<br/>
									@endif

									@if($order->orderAddress->shipping_ship_in_care != '')
										<strong>Care of: </strong>{{$order->orderAddress->shipping_ship_in_care}}<br/>
									@endif

									@if($order->orderAddress->shipping_phone_number != '' and $order->orderAddress->shipping_phone_number != '')
										{{$order->orderAddress->shipping_phone_number}}<br/>
									@endif

									@if($order->orderAddress->shipping_add1 != '' and $order->orderAddress->shipping_add2 != '')
										{{$order->orderAddress->shipping_add1}}<br/>{{$order->orderAddress->shipping_add2 }}<br>
									@elseif($order->orderAddress->shipping_add1 != '')
										{{$order->orderAddress->shipping_add1}}<br>
									@endif

									{{$order->orderAddress->shipping_city.', '.$order->orderAddress->shipping_state.', '.$order->orderAddress->shipping_zipcode}}<br>
									{{$order->orderAddress->shipping_country}}<br>
								</address>
							@else
								<address class="col-xs-10">Shipping Address Not Available</address>
							@endif
							<div class="col-xs-1"><i class="fa fa-pencil-square-o address pull-right" order-id="{{$order->id}}" data-type='shipping_add' order-multiple="0" order-product-table-id="0"></i></div>
						</div>
					</div>
				@else
					<div class="col-md-4 col-sm-6 col-xs-12 invoice-col">
						<strong>Ship To</strong>
						<div class="col-xs-12 add_box">
							<strong>{{$order->customer->fname.' '.$order->customer->lname}}</strong><br/>
							<span style="color:red">Split Shipment-See Below</span>
						</div>
					</div>
				@endif
				<div class="col-md-4 col-sm-6 col-xs-12 invoice-col no-padding" >
					<div class="form-group col-sm-12">
						{{ Form::label('terms', 'Terms',array('class'=>'col-xs-4 form-control-label'))}}
						<div class="col-xs-8">
							{{Form::select('terms',config('constants.terms'),$order->terms,['class'=>'form-control','id'=>'terms'])}}
						</div>
					</div>
					<?php
					$class = 'hide';
					if(!empty($order->new_terms) or $order->new_terms != ''){
						$class = '';
					}
					?>
					<div class="form-group col-sm-12 {{$class}}">
						{{ Form::label('', '&nbsp;',array('class'=>'col-xs-4 form-control-label'))}}
						<div class="col-xs-8">	{{Form::text('new_terms',$order->new_terms,['class'=>'form-control','placeholder'=>'Enter New Terms','id'=>'new_terms'])}}
						</div>
					</div>
					@if(\Auth::user()->role_id == 1)
						<div class="form-group col-sm-12">
							{{ Form::label('representative', 'Representative',array('class'=>'col-xs-4 form-control-label'))}}
							<div class="col-xs-8">
								{{Form::select('representative',[''=>'Select Representative']+$agents,$order->agent_id,['class'=>'form-control','id'=>'representative'])}}
							</div>
						</div>
					@endif
					<div class="form-group col-sm-12">
						{{ Form::label('customer_po', 'Customer PO#',array('class'=>'col-xs-4 form-control-label'))}}
						<div class="col-xs-8">	{{Form::text('customer_po',$order->customer_po,['class'=>'form-control','id'=>'customer_po'])}}
						</div>
					</div>

					@if($order->payment_status != 0)
						@php
							//$edit_class = 'hide';
							$edit_class = '';
						@endphp
					<div class="form-group col-sm-12">
						{{ Form::label('', 'Payment By',array('class'=>'col-xs-4 form-control-label'))}}
						<div class="col-xs-8">
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
						<div class="form-group col-sm-12">
							{{ Form::label('', 'Payment ID',array('class'=>'col-xs-4 form-control-label'))}}
							<div class="col-xs-8">{{$order->payment_id}}</div>
						</div>
						@endif
					@else
						@php
							$edit_class = '';
						@endphp
					@endif
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 table-responsive options_products_div">
					<div class="dragscroll" id="container2">
						<table class="table table-condensed">
							@php
								$j = 1;
							@endphp

							@foreach($order->orderProduct as $val)
								@if($j == 1)
									<thead>
										<tr>
											<th class="">Description</th>
											<th class="nowrap">Qty:</th>
											<!--<th class="nowrap">Base Price:</th>-->
											<th class="nowrap">Price:</th>
											<th class="nowrap">Gross Total:</th>
											<th class="nowrap">Price Discount:</th>
											<th class="nowrap">Total:</th>

											<th class="nowrap">Tracking Number:</th>

											@if($order->multiple_shipping != 0)
											<th class="nowrap">Ship To Address:</th>
											@endif
										</tr>
									</thead>
									<tbody>
								@endif

								@php
									$colspan = 7;
									if($order->multiple_shipping != 0)
										$colspan++;
								@endphp
								<tr>
									<td class="" style="width:25%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-1 no-padding {{$edit_class}}">
												<i class="fa fa-pencil-square-o options pull-right" order-id="{{$order->id}}" data-type='product_options' order-item="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-qty="{{$val->qty}}"></i>
											</div>
											<div class="col-xs-11 product_options_{{$val->item_id}}">
												<strong>
													@if($val->product_name !="")
														{{$val->product_name}}
													@else
														{{$val->product->name}}
													@endif
												</strong>
												<div class="col-xs-12">
													{!!$val->description!!}

												@foreach($order->orderProductOptions as $options)
													@if($val->id == $options->order_product_id)
														@if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height')
														{{$options->custom_option_name.'(ft)'}}: {{$options->value}}<br/>
														@else
															{{$options->custom_option_name}}: {{$options->value}}<br/>
														@endif
													@endif
												@endforeach
												</div>
											</div>
										</div>
									</td>
									<td class="nowrap" style="width:10%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-2 no-padding div_qty_edit_{{$val->item_id}} {{$edit_class}}">
												<i class="fa fa-pencil-square-o product_values pull-left"  order-id="{{$order->id}}" data-type='qty' order-item="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->qty}}"></i>
											</div>
											<div class="col-xs-10 div_qty_{{$val->item_id}}">
												{{$val->qty}}
											</div>
										</div>
									</td>
									<?php /*<td class="nowrap" style="width:15%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-1 no-padding div_rate_edit_{{$val->item_id}} {{$edit_class}}">
												<i class="fa fa-pencil-square-o product_values pull-left"  order-id="{{$order->id}}" data-type='rate' order-item="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->price_default}}"></i>
											</div>
											<div class="col-xs-11 div_rate_{{$val->item_id}}">
												${{number_format(number_format($val->price_default,2,'.',''),2)}}
											</div>
										</div>
									</td> */?>
									<td class="nowrap" style="width:15%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-2 no-padding div_gross_price_edit_{{$val->item_id}} {{$edit_class}}">
												<i class="fa fa-pencil-square-o product_values pull-left"  order-id="{{$order->id}}" data-type='price' order-item="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->price}}"></i>
											</div>
											<div class="col-xs-10 no-padding div_gross_price_{{$val->item_id}}">
												${{number_format(number_format($val->price,2,'.',''),2)}}
											</div>
										</div>
									</td>
									<td class="nowrap" style="width:15%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-2 no-padding div_gross_total_edit_{{$val->item_id}} {{$edit_class}}">
												<i class="fa fa-pencil-square-o product_values pull-left"  order-id="{{$order->id}}" data-type='gross_total' order-item="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->gross_total}}"></i>
											</div>
											<div class="col-xs-10 no-padding div_gross_total_{{$val->item_id}}">
												${{number_format(number_format($val->gross_total,2,'.',''),2)}}
											</div>
										</div>
									</td>
									<td class="nowrap" style="width:15%">
										<div class="col-xs-12 no-padding">
											<div class="col-xs-2 no-padding div_qty_discount_edit_{{$val->item_id}} {{$edit_class}}">
												<i class="fa fa-pencil-square-o product_values pull-left"  order-id="{{$order->id}}" data-type='qty_discount' order-item="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->qty_discount}}"></i>
											</div>
											<div class="col-xs-10 no-padding div_qty_discount_{{$val->item_id}}">
												${{number_format(number_format($val->qty_discount,2,'.',''),2)}}
											</div>
										</div>
									</td>
									<td class="nowrap" style="width:15%">
										<div class="col-xs-112 no-padding">
											<div class="col-xs-2 no-padding div_total_edit_{{$val->item_id}} {{$edit_class}}">
												<i class="fa fa-pencil-square-o product_values pull-left"  order-id="{{$order->id}}" data-type='total' order-item="{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-value="{{$val->total}}"></i>
											</div>
											<div class="col-xs-10 no-padding div_total_{{$val->item_id}}">
												${{number_format(number_format($val->total,2,'.',''),2)}}
											</div>
										</div>
									</td>

									<td class="nowrap">
										@if($val->tracking_id !="")
											<b>Tracking : </b>{{$val->tracking_id}}<br/>
											<b>Shipped Via : </b>{{$val->shipping_career}}<br/>
										@else
											Not-Set
										@endif
									</td>

									@if($order->multiple_shipping != 0)
									<td class="nowrap" style="width:30%">
										<div class="col-xs-12 add_box shipping_add_{{$val->item_id}}_div">
											<div class="col-xs-1 no-padding"><i class="fa fa-pencil-square-o address pull-left" order-id="{{$order->id}}" data-type='shipping_add' order-multiple="shipping_add_{{$val->item_id}}" order-product-id="{{$val->product_id}}" order-product-table-id="{{$val->id}}"></i></div>
											<address class="col-xs-10">
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

												{{$val->shipping_city.', '.$val->shipping_state.', '.$val->shipping_zipcode}}<br>
												{{$val->shipping_country}}<br>
											</address>
										</div>
									</td>
									@endif
								</tr>
								<tr class="bordernone">
									<td colspan="{{$colspan}}">
										<div class="col-xs-12 div_project_{{$val->id}}">
											<div class="col-xs-2 no-padding">
												<strong>Project Name:</strong>
											</div>
											@if(!empty($val->project_name))
												<div class="col-xs-10" style="overflow-x: auto;max-height: 300px;overflow-y: auto;">
													<div class="no-padding">
														<i class="fa fa-pencil-square-o project_comment_btn pull-left" data-value="{{$val->project_name}}" data-id="{{$val->id}}" data-type="project_name"  data-btn="edit" id="project_{{$val->id}}"></i>
													</div>
													<div class="val_div_project_{{$val->id}}">
														{{$val->project_name}}
													</div>
												</div>
											@else
												<div class="col-xs-10">
													<button type="button" class="btn btn-xs bg-olive project_comment_btn" data-value="{{$val->project_name}}" data-id="{{$val->id}}" data-type="project_name"  data-btn="add" id="project_{{$val->id}}">Add</button>
												</div>
											@endif
										</div>
										<div class="clearfix"></div><br/>
										<div class="col-xs-12 div_comment_{{$val->id}}">
											<div class="col-xs-2 no-padding">
												<strong>Comment:</strong>
											</div>
											@if(!empty($val->comments))
												<div class="col-xs-10" style="overflow-x: auto;max-height: 300px;overflow-y: auto;">
													<div class="no-padding">
														<i class="fa fa-pencil-square-o project_comment_btn pull-left" data-value="{{$val->comments}}" data-id="{{$val->id}}" data-type="comments" data-btn="edit" id="comment_{{$val->id}}"></i>
													</div>
													<div class="val_div_comment_{{$val->id}}">
														{{$val->comments}}
													</div>
												</div>
											@else
												<div class="col-xs-10">
													<button type="button" class="btn btn-xs bg-olive project_comment_btn" data-value="{{$val->comments}}" data-id="{{$val->id}}" data-type="comments" data-btn="add" id="comment_{{$val->id}}">Add</button>
												</div>
											@endif
										</div>
										<div class="clearfix"></div><br/>
										@if($val->tflow_job_id >= 1 && $val->art_work_status==6)
											<div class="col-xs-12">
												<div class="col-xs-2 no-padding">
													<strong>ArtWork File:</strong>
												</div>
												<div class="col-xs-10">
													<a target="_blank" href="{{'http://108.61.143.179:9016/application/job/'.$val->tflow_job_id.'/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT'}}">View ArtWork File</a>
												</div>
											</div>
										@endif
									</td>
								</tr>
								@php
									$j++;
								@endphp
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<hr/>
			<div class="row">
				<div class="col-xs-12 form-group">
					{{ Form::label('coupon_code', 'Coupon Code',array('class'=>'col-xs-12 col-sm-3 col-md-2 col-lg-2 form-control-label'))}}
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
						<div class="input-group">
							{{ Form::text('coupon_code',$order->discount_code,['class' => 'form-control','placeholder' => 'Enter Coupon Code','id'=>'coupon_code'])}}
							<span class="input-group-btn">
								<button onclick="applyCoupon()" type="button" class="btn btn-info btn-lrg ajax" title="Apply Coupon">Apply Coupon</button>
							</span>
						</div>
						@if(!empty($order->discount_code) && !empty($order->discount))
						<span class='text-success'>This coupon code applied on your order.</span>
						@endif
					</div>
				</div>
			</div>
			<hr/>
			<div class="row">
				<div class="col-sm-6 col-xs-12">
					<p class="lead order_view_head">Thank you for your business!</p>
					<div class="text-muted" style="margin-top: 10px;">
						<h4><b>Easy Order Banners</b></h4>
						<div class="col-xs-12">
							<p>P.O.Box 432</p>
							<p>Hatfield,PA 19440</p><br/>
							<p>Phone: 800-920-9527</p>
							<p>Fax: 267-244-1056</p>
							<p>E-mail: info@easyorderbanners.com</p>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-xs-12">
					<p class="lead">&nbsp;</p>
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th style="width:50%">Subtotal:</th>
								<td class="sub_total">${{priceFormat($order->sub_total)}}</td>
							</tr>
							<tr>
								<th>Discount</th>
								<td class="discount">
									${{ priceFormat( $order->discount ) }}
								</td>
							</tr>
							<tr>
								<th>Shipping Amount:</th>
								<td class="shipping">
									<div class="col-xs-4 input-group">
										<span class="input-group-addon">$</span>
										{{Form::text('shipping_amount',priceFormat($order->shipping_fee),['class'=> 'form-control','onchange'=>'setTwoNumberDecimal(this)','onblur'=>'setTwoNumberDecimal(this)','id'=>'shipping_amount','data-subtotal' => $order->sub_total,'data-discount' => $order->discount,'data-tax' => (!empty($order->sales_tax) and $order->sales_tax > 0)?$order->sales_tax:0])}}
									</div>
								</td>
							</tr>
							<tr>
								<th>Shipping Type:</th>
								<td class="shipping">
									<div class="col-xs-8 input-group">	{{Form::select('shipping_option', config('constants.Shipping_option'), str_pad($order->shipping_option, 2, "0", STR_PAD_LEFT),array('class'=>'form-control shipping_option','id'=>'shipping_option','placeholder'=>'Select Shipping Type'))}}
									</div>
								</td>
							</tr>
							<tr class="sales_tax" style="display:{{(!empty($order->sales_tax) and $order->sales_tax > 0)?'':'none'}}">
								<th>Sales Tax @ {{config('constants.sales_tax')}}%:</th>
								<td class="tax">
									${{(!empty($order->sales_tax) and $order->sales_tax > 0)?priceFormat($order->sales_tax):0}}
								</td>
							</tr>
							<tr>
								<th>Total:</th>
								<td class="main_total">${{priceFormat($order->total)}}</td>
							</tr>
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
</section>
@include('partials.order.order_edit')
<script>
	function removeCommas(str) {
    	while (str.search(",") >= 0) {
        	str = (str + "").replace(',', '');
    	}
    	return str;
	};

	$(document).ready(function() {
		$(".wrapper1").scroll(function(){
			console.log($(".wrapper1").scrollLeft());
			$("#order_view").scrollLeft($(".wrapper1").scrollLeft());
		});
		$("#order_view").scroll(function(){
			$(".wrapper1").scrollLeft($("#order_view").scrollLeft());
		});
	});

	$(document).on('blur','#shipping_amount',function(){
		var sub_total = Number(removeCommas($(this).attr('data-subtotal')));
		var discount = Number($(this).attr('data-discount'));
		var tax = Number($(this).attr('data-tax'));
		var shipping = Number($(this).val());
		var total = 0;

		total = (sub_total - discount)+shipping+tax;
		total = formatMoney(total.toFixed(2));

		//alert(sub_total+" and "+discount+" and "+tax+" and "+shipping+" and "+total)

		$('.main_total').html('$'+total);
	});

	$(document).on('change','#terms',function(){
		/* if($(this).val() == 3){
			$('#new_terms').closest('.form-group').removeClass('hide');
		}else{
			$('#new_terms').closest('.form-group').addClass('hide');
		} */
	});

	$(document).on('click','.select_save_all',function(){

		var params = new Object();
		params._token = '{{csrf_token()}}';
		params.order_id = $(this).attr('order-id');
		params.terms = $('#terms').val();
		params.new_terms = $('#new_terms').val();
		params.agent = $('#representative').val();
		params.customer_po = $('#customer_po').val();
		params.shipping_amount = $('#shipping_amount').val();
		params.shipping_option = $('#shipping_option').val();

		$.ajax({
			url:'{{url("admin/order/order_save")}}',
			type:'post',
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			data:params,
			success:function(data){
			}
		});

	});

	$(document).on('click','.select_save',function(){
		var type = $(this).attr('data-type');
		var order_id = $(this).attr('order-id');
		var on = 1;
		if(type == 'terms'){
			$('#terms').closest('.form-group').removeClass('has-error');
			$('#terms').nextAll().remove();
			$('#new_terms').closest('.form-group').removeClass('has-error');
			$('#new_terms').nextAll().remove();
			var term_value = $('#terms').val();
			var new_term_value = 0;
			if(term_value != ''){
				if(term_value == 3){
					new_term_value = $('#new_terms').val();
					if(new_term_value == ''){
						$('#new_terms').closest('.form-group').addClass('has-error');
						$("<span class='help-block'>this is required</span>").insertAfter("#new_terms");
						on = 0;
					}
				}
			}else{
				$('#terms').closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter("#terms");
				on = 0;
			}
			if(on){
				$.ajax({
					url:'{{url("admin/order/order_changes")}}',
					type:'post',
					dataType:'json',
					beforeSend: function () {
					  $.blockUI();
					},
					complete: function () {
					  $.unblockUI();
					},
					data:{'type':'terms','order_id':order_id,'terms':term_value,'new_terms':new_term_value},
					success:function(data){

					}
				});
			}
		}
		else if(type == 'PO'){
			var po = $(this).attr('order-po');
			var po_val = $('#po').val();
			if(po_val == ''){
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block' style='font-size:15px'>this is required</span>").insertAfter(".po_div");
				on = 0;
			}
			if(on){
				$.ajax({
					url:'{{url("admin/order/order_changes")}}',
					type:'post',
					dataType:'json',
					beforeSend: function () {
					  $.blockUI();
					},
					complete: function () {
					  $.unblockUI();
					},
					data:{'type':'po','order_id':order_id,'old_po':po,'new_po':po_val},
					success:function(data){
						if(data.status == 'success'){
							window.location.href = data.html;
						}
					}
				});
			}
		}
		else if(type == 'customer_po'){
			$('#customer_po').closest('.form-group').removeClass('has-error');
			$('#customer_po').nextAll().remove();
			var customer_po = $('#customer_po').val();
			if(customer_po == ''){
				$('#customer_po').closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter("#customer_po");
				on = 0;
			}
			if(on){
				$.ajax({
					url:'{{url("admin/order/order_changes")}}',
					type:'post',
					dataType:'json',
					beforeSend: function () {
					  $.blockUI();
					},
					complete: function () {
					  $.unblockUI();
					},
					data:{'type':'customer_po','order_id':order_id,'customer_po':customer_po},
					success:function(data){

					}
				});
			}
		}
		else{
			$('#representative').closest('.form-group').removeClass('has-error');
			$('#representative').nextAll().remove();
			var agent = $('#representative').val();
			if(agent == ''){
				$('#representative').closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter("#representative");
				on = 0;
			}
			if(on){
				$.ajax({
					url:'{{url("admin/order/order_changes")}}',
					type:'post',
					dataType:'json',
					beforeSend: function () {
					  $.blockUI();
					},
					complete: function () {
					  $.unblockUI();
					},
					data:{'type':'agent','order_id':order_id,'agent':agent},
					success:function(data){

					}
				});
			}
		}
	});
</script>
@endsection