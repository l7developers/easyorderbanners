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
				<div class="col-sm-6 col-xs-12 pull-right" style="width:40%">
					<h3 class="text-right">#{{$order_po->po_id}}</h3>
					<div class="date text-right">
						<b>Date:</b> {{date('m/d/Y',strtotime($order_po->created_at))}}<br>
					</div>
				</div>
			</div>
			<div class="row invoice-info main_print_div">
				<div class="col-md-3 col-sm-6 col-xs-12 invoice-col billing_add_div">
					<strong>Assign Vendor</strong>
					<div class="col-xs-12 add_box">
						<div class="vendor_detail">
							@if(!empty($order_po->vendor))
								<strong>Company : </strong>{{$order_po->vendor->company_name}}<br/>
								<strong>Name : </strong>{{$order_po->vendor->fname.' '.$order_po->vendor->lname}}<br/>
								<strong>Address : </strong>{{$order_po->vendor->company_address}}
							@endif
						</div>
					</div>
				</div>
				@if($order->multiple_shipping == 0)
					<div class="col-md-3 col-sm-6 col-xs-12 invoice-col shipping_add_div">
						<strong>Ship To</strong>
						<div class="col-xs-12 add_box">
							@if($order->orderPOAddress->shipping_company_name !="")
								{{$order->orderPOAddress->shipping_company_name}}<br/>
							@endif
							
							@if($order->orderPOAddress->shipping_fname != '' and $order->orderPOAddress->shipping_lname != '')
								{{$order->orderPOAddress->shipping_fname.' '.$order->orderPOAddress->shipping_lname}}<br/>						
							@endif

							@if($order->orderPOAddress->shipping_ship_in_care != '')
								<strong>Care of: </strong>{{$order->orderPOAddress->shipping_ship_in_care}}<br/>
							@endif

							@if($order->orderPOAddress->shipping_phone_number != '')
								{{$order->orderPOAddress->shipping_phone_number}}<br/>
							@endif

							{{$order->orderPOAddress->shipping_add1}}<br/>{{$order->orderPOAddress->shipping_add2}}<br>
														
							{{$order->orderPOAddress->shipping_city.', '.$order->orderPOAddress->shipping_state.', '.$order->orderPOAddress->shipping_zipcode}}<br>
							{{$order->orderPOAddress->shipping_country}}<br>
						
						</div>
					</div>
				@else
					<div class="col-md-3 col-sm-6 col-xs-12 invoice-col">
						<strong>Ship To</strong>
						<div class="col-xs-12 add_box">
							<strong>{{$order->customer->fname.' '.$order->customer->lname}}</strong><br/>
							<span style="color:red">Split Shipment-See Below</span>
						</div>
					</div>
				@endif
				<div class="col-md-6 col-sm-6 col-xs-12 invoice-col">
					<br/>
					@php
						$term = '';
						$terms = config('constants.terms');
						if(!empty($order_po->terms) and array_key_exists($order_po->terms,$terms)){
							$term = $terms[$order_po->terms];
						}
					@endphp
					<div class="row form-group">
						{{ Form::label('terms', 'Terms',array('class'=>'col-xs-5 form-control-label'))}}
						<div class="col-xs-7">{{$term}}</div>
					</div>
					
					<div class="row form-group">
						{{ Form::label('representative', 'Representative',array('class'=>'col-xs-5 form-control-label'))}}
						<div class="col-xs-7">{{(count($order->agent) > 0)?$order->agent->fname.' '.$order->agent->lname:''}}</div>
					</div>
				</div>
			</div>
			<div class="row">
				<table class="table table-condensed" style="margin-left:20px">
					<thead>
						<tr>
							<th class="" style="width:{{($order->multiple_shipping != 0)?40:50}}%">Description</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">Qty:</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">Rate:</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:10}}%">Amount:</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">Due Date:</th>
							<th class="nowrap" style="width:{{($order->multiple_shipping != 0)?10:25}}%">Shipping Via:</th>
							@if($order->multiple_shipping != 0)
							<th class="nowrap" style="width:30%">Ship To:</th>
							@endif
						</tr>
					</thead>
					<tbody>
					@foreach($po_products as $val)
						@php
							$qty = number_format($val->qty,2);
							$rate = priceFormat($val->rate);
							$amount = priceFormat($val->amount);
							$shipping_option = $order->shipping_option;
							if(isset($val->PoProduct)){
								$due_date = date('M-d-Y', strtotime(date('M-d-Y',strtotime($val->PoProduct->created_at)).' +3 days'));
								
								if(!empty($val->due_date))
									$due_date = $val->due_date;
								if(!empty($val->shipping_option))
									$shipping_option = $val->shipping_option;
							}
							
							if(strlen($shipping_option) < 2){
								$shipping_option = '0'.$shipping_option;
							}
							
							$colspan = 7;
							if($order->multiple_shipping != 0)
								$colspan++;
						@endphp
						<tr>
							<td class="" style="width:{{($order->multiple_shipping != 0)?40:50}}%">
								@if(isset($val->PoProduct))
								<div class="col-xs-12 no-padding">
									<div class="product_options_{{$val->PoProduct->item_id}}">	
										<strong>{{$val->product_name}}</strong>
										<div class="col-xs-12">
											{!! $val->description !!}
										</div>
										<div class="col-xs-12 product_options_{{$val->PoProduct->product_id}}">
										@php
											$order_vendor_options = \DB::table('order_po_options')->where('order_id',$order->id)->where('order_product_id',$val->PoProduct->id)->get();
											//pr($order_vendor_options);
										@endphp
										@foreach($order_vendor_options as $options)
											@if($options->option_name == 'Width' or $options->option_name == 'Height')
												<li>{{$options->option_name.'(ft)'}}: {{$options->option_value}}</li>
											@else
												<li>{{$options->option_name}}: {{$options->option_value}}</li>
											@endif
										@endforeach
										</div>
									</div>
								</div>
								@else
									<div class="col-xs-12 no-padding">
										<strong>{{$val->product_name}}</strong>
										<div class="col-xs-12">
											{!! $val->description !!}
										</div>
									</div>
								@endif
							</td>
							<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%">
								<div class="col-xs-12 no-padding">
									{{$qty}}
								</div>
							</td>
							<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:10}}%">
								<div class="form-group col-xs-12 no-padding">${{$rate}}</div>
							</td>
							<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:10}}%">
								<div class="amount_div_{{$val->product_id}}">${{$amount}}</div>
							</td>
							@if(isset($val->PoProduct))
								<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?10:10}}%">
									<div class="form-group col-xs-12 no-padding">{{$due_date}}
									</div>
								</td>
								<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?10:25}}%">{{config('constants.Shipping_option.'.$shipping_option)}}</td>
								
								@if($order->multiple_shipping != 0)
								<td class="nowrap" style="width:30%">
									<div class="col-xs-12 add_box">
									@if($val->PoProduct->OrderPOAddress->shipping_company_name !="")
										{{$val->PoProduct->OrderPOAddress->shipping_company_name}}<br/>
									@endif
										
									@if($val->PoProduct->orderPOAddress->shipping_fname != '' and $val->PoProduct->orderPOAddress->shipping_lname != '')
										{{$val->PoProduct->orderPOAddress->shipping_fname.' '.$val->PoProduct->orderPOAddress->shipping_lname}}<br/>						
									@endif

                                    @if($order->orderPOAddress->shipping_ship_in_care != '' and $order->orderPOAddress->shipping_ship_in_care != '')
                                        <strong>Care of: </strong>{{$order->orderPOAddress->shipping_ship_in_care}}<br/>
                                    @endif

									{{$val->PoProduct->orderPOAddress->shipping_add1}}<br/>{{$val->PoProduct->shipping_add2}}<br>
																	
									{{$val->PoProduct->orderPOAddress->shipping_city.', '.$val->PoProduct->orderPOAddress->shipping_state.', '.$val->PoProduct->orderPOAddress->shipping_zipcode}}<br/>
									{{$val->PoProduct->orderPOAddress->shipping_country}}<br/>
									</div>
								</td>
								@endif
							@else
								<td class="nowrap" colspan="2" style="width:25%"></td>
								@if($order->multiple_shipping != 0)
									<td class="nowrap" style="width:30%"></td>
								@endif
							@endif
						</tr>
						<tr class="bordernone">
							<td colspan="{{$colspan}}">
								<div class="col-sm-12">
									<div class="col-xs-2 no-padding">
										<strong>Project Name:</strong>
									</div>
									<div class="col-xs-10">
										@if(!empty($val->project_name))
											{{$val->project_name}}
										@else
											None
										@endif
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-xs-12">
									<div class="col-xs-2 no-padding">
										<strong>Comment:</strong>
									</div>
									<div class="col-xs-10">
										@if(!empty($val->comments))
											{{$val->comments}}
										@else
											None
										@endif
									</div>
								</div>
								@if($val->tflow_job_id >= 1 && $val->art_work_status==6)
									<div class="col-xs-12">
										<div class="col-xs-2 no-padding">
											<strong>ArtWork File:</strong>
										</div>
										<div class="col-xs-10">{{'http://108.61.143.179:9016/application/job/'.$val->tflow_job_id.'/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT'}}
										</div>
									</div>
								@endif
							</td>
						</tr>
					@endforeach
						@if(!empty($order_po->notes))
						<tr>
							<td class="" style="width:{{($order->multiple_shipping != 0)?40:50}}%;">
								<div class="col-xs-12 no-padding">
									<strong>Order Notes.</strong>
									<div class="col-xs-12">{{$order_po->notes}}</div>
								</div>
							</td>
							<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:5}}%"></td>
							<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:10}}%"></td>
							<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?5:10}}%"></td>
							<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?10:10}}%"></td>
							<td class="nowrap" style="width:{{($order->multiple_shipping != 0)?10:15}}%"></td>
							@if($order->multiple_shipping != 0)
								<td class="nowrap" style="width:25%"></td>
							@endif
						</tr>
						@endif
					</tbody>
				</table>
			</div>
			<div class="row">
			
				<div class="col-xs-12" style="width:100%">
					<div class="col-xs-9 col-sm-3 text-muted" style="margin-top: 15px;width:45%;">
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
				
					<div class="col-sm-3 col-xs-12 pull-right" style="margin-top: 15px;width:30%;">
						<p class="lead">&nbsp;</p>
						<div class="table-responsive">
							<table class="table">
								<tr>
									<th style="width:40%">Sub Total:</th>
									<td style="width:60%" id="subtotal_amount">${{priceFormat($order_po->subtotal)}}</td>
								</tr>
								<tr>
									<th style="width:40%">Shipping:</th>
									<td style="width:60%">${{priceFormat($order_po->shipping)}}</td>
								</tr>
								<tr>
									<th style="width:40%">Total:</th>
									<td style="width:60%" id="total_amount">${{priceFormat($order_po->subtotal+$order_po->shipping)}}</td>
								</tr>
							</table>
						</div>
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