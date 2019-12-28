@extends('layouts.admin_layout')
@section('content')

<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Order PO Detail</h1></div>
		<div class="col-xs-6 full_w">
			@php
				$class = 'hide';
				if(count($order_po) > 0){
					$class = '';
				}
			@endphp
			<div class="top_btns {{$class}}">
				<a href="{{url('admin/order/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>			
				<a target="_blank" href="{{url('admin/order/po/print/'.$id)}}" class=" pull-right"><i class="fa fa-print" style="font-size: 30px;" aria-hidden="true"></i></a>				
				<a href="{{url('admin/order/po/mail/'.$id)}}" onclick="return po_mail()" class="pull-right"><i class="fa fa-envelope" style="font-size: 30px;" aria-hidden="true"></i></a>				
				<a href="{{url('admin/order/po/pdf/'.$id.'/create')}}" class=" pull-right"><i class="fa fa-file-pdf-o" style="font-size: 30px;" aria-hidden="true"></i></a>
				<a href="javascript:void(0)" class=" pull-right save_po" order-id="{{$order->id}}"><i class="fa fa-save" style="font-size: 30px;"></i></a>
			</div>
		</div>
	</div>
</section>

<section id="printableArea" class="invoice">
	<div class="row">
		{{Form::model('po_form',['id'=>'po_form'])}}
		<div class="col-xs-12">
			<div class="row page-header">
				<div class="col-sm-6 col-xs-12">
					<h2>
						<img src="{{URL::to('public/img/admin/logo.png')}}" alt="Logo"/>
					</h2>
				</div>
				<div class="col-sm-6 col-xs-12">
					{{Form::hidden('po_id',$id)}}
					{{Form::hidden('order_id',$order->id,['id'=>'order_id'])}}
					<h3 class="text-right">#{{$id}}</h3>
					<div class="date text-right">
						<b>Date:</b> {{date('m/d/Y',strtotime($orderPO->created_at))}}<br>
					</div>
				</div>
			</div>
			<div class="row invoice-info main_print_div">
				<div class="col-md-4 col-sm-6 col-xs-12 invoice-col billing_add_div">
					<strong>Assign Vendor</strong>
					<div class="col-xs-12 add_box">
						<div class="form-group col-xs-10">
							{{Form::label('vendor','Select Vendor',['class'=>'form-control-label'])}}	{{Form::select('vendor',$vendorList,$orderPO->vendor_id,['class'=>'form-control','id'=>'vendor','placeholder'=>"Vendor",'data-company'=>$vendor->company_name,'data-name'=>$vendor->fname.' '.$vendor->lname,'data-address'=>$vendor->company_address])}}
						</div>
						<?php /*<div class="col-xs-2">
							{{Form::label('','&nbsp;',['class'=>'form-control-label'])}}	<br/>
							<i class="btn btn-primary fa fa-save vendor_change" order-id="{{$order->id}}"></i>
						</div>*/?>
						
						<div class="clearfix"></div>
						<br/>
						<div class="col-xs-12 vendor_detail">
							@if(!empty($vendor))
								<strong>Company : </strong>{{$vendor->company_name}}<br/>
								<strong>Name : </strong>{{$vendor->fname.' '.$vendor->lname}}<br/>
								<strong>Address : </strong>{{$vendor->company_address}}
							@endif
						</div>
					</div>
				</div>
				@if($order->multiple_shipping == 0)
					<div class="col-md-4 col-sm-6 col-xs-12 invoice-col shipping_add_div">
						<strong>Ship To</strong>
						<div class="col-xs-12 add_box">
							<div class="col-xs-1"><i class="fa fa-pencil-square-o address pull-right" order-po-address-id="{{$po_products[0]->PoProduct->OrderPOAddress->id}}" order-id="{{$order->id}}" order-multiple="0"></i></div>
							<address class="col-xs-10" id="shipp_add">
								@if($order->orderPOAddress->shipping_company_name !="")
									{{$order->orderPOAddress->shipping_company_name}}<br/>
								@endif

								@if($order->orderPOAddress->shipping_fname != '' and $order->orderPOAddress->shipping_lname != '')
									{{$order->orderPOAddress->shipping_fname.' '.$order->orderPOAddress->shipping_lname}}<br/>					
								@endif

								@if($order->orderPOAddress->shipping_ship_in_care != '')
									<strong>Care of: </strong>{{$order->orderPOAddress->shipping_ship_in_care}}<br/>
								@endif

								@if($order->orderPOAddress->shipping_add1 != '' and $order->orderPOAddress->shipping_add2 != '')
									{{$order->orderPOAddress->shipping_add1}}<br/>{{$order->orderPOAddress->shipping_add2 }}<br>
								@elseif($order->orderPOAddress->shipping_add1 != '')
									{{$order->orderPOAddress->shipping_add1}}<br>
								@endif

								{{$order->orderPOAddress->shipping_city.', '.$order->orderPOAddress->shipping_state.' '.$order->orderPOAddress->shipping_zipcode . ' ' . $order->orderPOAddress->shipping_country}}
								@if($order->orderPOAddress->shipping_phone_number != '')
									<br/>{{$order->orderPOAddress->shipping_phone_number}}
								@endif
							</address>
						</div>
					</div>
				@else
					<div class="col-md-4 col-sm-6 col-xs-12 invoice-col">
						<strong>Ship To</strong>
						@if(count($po_products) > 1)
							<div class="col-xs-12 add_box">
								<strong>{{$order->customer->fname.' '.$order->customer->lname}}</strong><br/>
								<span style="color:red">Split Shipment-See Below</span>
							</div>
						@else
							<div class="col-xs-12 add_box no-padding shipping_add_{{$po_products[0]->PoProduct->item_id}}_div">
								<div class="col-xs-1">
									<i class="fa fa-pencil-square-o address" order-id="{{$order->id}}" order-multiple='1' order-product-id="{{$po_products[0]->PoProduct->id}}"></i>
								</div>
								<div class="col-xs-10" id="shipp_add_{{$po_products[0]->PoProduct->id}}">
								@if($po_products[0]->PoProduct->OrderPOAddress->shipping_company_name !="")
									{{$po_products[0]->PoProduct->OrderPOAddress->shipping_company_name}}<br/>
								@endif
																					
								@if($po_products[0]->PoProduct->OrderPOAddress->shipping_fname != '' and $po_products[0]->PoProduct->OrderPOAddress->shipping_lname != '')
									{{$po_products[0]->PoProduct->OrderPOAddress->shipping_fname.' '.$po_products[0]->PoProduct->OrderPOAddress->shipping_lname}}<br/>			
								@endif
								{{$po_products[0]->PoProduct->OrderPOAddress->shipping_add1}}<br/>{{$po_products[0]->PoProduct->OrderPOAddress->shipping_add2}}<br/>
																			
								{{$po_products[0]->PoProduct->OrderPOAddress->shipping_city.', '.$po_products[0]->PoProduct->OrderPOAddress->shipping_state.' '.$po_products[0]->PoProduct->OrderPOAddress->shipping_zipcode . ' ' . $po_products[0]->PoProduct->OrderPOAddress->shipping_country}}

								</div>
							</div>
						@endif
					</div>
				@endif
				<div class="col-md-4 col-sm-6 col-xs-12 invoice-col">
					<br/>
					@php
						$class = 'hide';
						$term = $orderPO->terms;
						$new_terms = $orderPO->new_terms;
						/*if($orderPO->terms == 3){
								$class = '';							
						} */						
					@endphp
					<div class="form-group">
						{{ Form::label('terms', 'Terms',array('class'=>'col-xs-4 form-control-label'))}}
						<div class="col-xs-8">
							{{Form::select('terms',config('constants.terms'),$term,['class'=>'form-control','id'=>'terms'])}}
						</div>
						<?php /*<div class="col-xs-2">
							<i class="btn btn-primary fa fa-save select_save" data-type="terms" data-value="terms" order-id="{{$order->id}}"></i>
						</div> */ ?>
					</div>
					<br/>
					<div class="form-group {{$class}}">
						{{ Form::label('', '&nbsp;',array('class'=>'col-xs-4 form-control-label'))}}
						<div class="col-xs-8">	{{Form::text('new_terms',$new_terms,['class'=>'form-control','placeholder'=>'Terms','id'=>'new_terms'])}}
						</div>
					</div>
					<br/><br/>
					@if(\Auth::user()->role_id == 1)
						<div class="form-group">
							{{ Form::label('representative', 'Representative',array('class'=>'col-xs-4 form-control-label'))}}
							<div class="col-xs-8">
								{{Form::select('representative',[''=>'Select Representative']+$agents,$order->agent_id,['class'=>'form-control','id'=>'representative'])}}
							</div>
							<?php /*<div class="col-xs-2">
								<i class="btn btn-primary fa fa-save select_save" data-type="representative" data-value="agent" order-id="{{$order->id}}"></i>
							</div> */ ?>
						</div>
					@endif
					
					@if($order->payment_status != 0)
						@php
							$edit_class = 'hide';
						@endphp
					@else
						@php
							$edit_class = '';
						@endphp
					@endif
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 table-responsive options_products_div">
					<div class="" id="">
						<table class="table table-condensed">
							<thead>
								<tr>
									<th class="" style="width:25%">Description</th>
									<th class="nowrap" style="width:10%;min-width:70px;">Qty:</th>
									<th class="nowrap" style="width:10%;min-width:120px;">Rate:</th>
									<th class="nowrap" style="width:10%">Amount:</th>
									<th class="nowrap" style="width:15%;min-width:120px;">Due Date:</th>
									<th class="nowrap" style="width:10%;min-width:120px;">Shipping Via:</th>
									@if(count($po_products) > 1 && $order->multiple_shipping != 0)
									<th class="nowrap" style="width:20%">Ship To:</th>
									@endif
								</tr>
							</thead>
							<tbody>
							
							@foreach($po_products as $val)
								@php
									$qty = $val->qty;
									$rate = $val->rate;
									$amount = $val->amount;
									$shipping_option = $order->shipping_option;
									if(isset($val->PoProduct)){
										$due_date = date('M-d-Y', strtotime(date('Y-m-d',strtotime($val->created_at)).' +3 days'));
										
										if(!empty($val->due_date))
											$due_date = $val->due_date;
										if(!empty($val->shipping_option))
											$shipping_option = $val->shipping_option;
									}
									
									$colspan = 7;
									if(count($po_products) > 1 && $order->multiple_shipping != 0)
										$colspan++;
								@endphp
								<tr class="po_line_item po_line_item_{{$val->id}}" rel="{{$val->id}}">
									<td class="" style="width:25%">
										@if(isset($val->PoProduct))
										<div class="col-xs-12 no-padding">
											<div class="col-xs-2 no-padding">
												<i class="fa fa-pencil-square-o options" order-id="{{$order->id}}" data-product-id="{{$val->PoProduct->product_id}}" data-type='product_options' order-product-id="{{$val->PoProduct->id}}" order-product-name="{{$val->product_name}}"></i>
												<i class="fa fa-trash product_delete" order-id="{{$order->id}}" data-product-id="{{$val->PoProduct->product_id}}" data-type='product_delete' order-product-id="{{$val->PoProduct->id}}" order-product-name="{{$val->product_name}}"></i>
											</div>
											<div class="col-xs-10 product_options_{{$val->PoProduct->item_id}}">	
												<strong class="poProductName{{$val->PoProduct->id}}">{{$val->product_name}}</strong>
												<div class="col-xs-12 product_options_{{$val->PoProduct->id}}">
													{!!$val->description!!}
												@php
													$order_vendor_options = \DB::table('order_po_options')->where('order_id',$order->id)->where('order_product_id',$val->PoProduct->id)->get();								
												@endphp
												@foreach($order_vendor_options as $options)
													@if($options->option_name == 'Width' or $options->option_name == 'Height')	{{$options->option_name.'(ft)'}}: {{$options->option_value}}<br/>
													@else
													{{$options->option_name}}: {{$options->option_value}}<br/>
													@endif
												@endforeach
													<br/>
												</div>
											</div>
										</div>
										@else
											<div class="col-xs-12 no-padding">
												<div class="col-xs-2 no-padding">
												<i class="fa fa-pencil-square-o options custom_line_item" order-id="{{$order->id}}" data-type='product_options' order-product-id="{{$val->id}}" order-product-name="{{$val->product_name}}"></i>
												<i class="fa fa-trash product_delete custom_line_item" order-id="{{$order->id}}" data-type='product_delete' order-product-id="{{$val->id}}" order-product-name="{{$val->product_name}}"></i>
												</div>
												<div class="col-xs-10">
													<strong class="poProductName{{$val->id}}">{{$val->product_name}}.</strong>
													<div class="col-xs-12 product_options_{{$val->id}}">
														{!! $val->description !!}
													</div>
												</div>
											</div>	
										@endif										
									</td>
									<!--<td class="" style="width:5%">
										@if($val->tflow_job_id != '' && $val->art_work_status == 6)
											<b>{{$val->product_name}}: </b><a target="_blank" href="http://108.61.143.179:9016/application/job/{{$val->tflow_job_id}}/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT">Click Here To View Production Ready Art Link</a>
										@else
											Not-Set
										@endif			
									</td>-->
									<td class="nowrap" style="width:5%">
										<div class="col-xs-12 no-padding">	{{Form::number('qty['.$val->id.']',$qty,['class'=>'form-control val_change','placeholder'=>'Quantity','min'=>1,'id'=>'qty','data-type'=>'qty','data-product-id'=>$val->id])}}
										</div>
									</td>
									<td class="nowrap" style="width:10%">
										<div class="form-group col-xs-12 no-padding">	{{Form::number('rate['.$val->id.']',priceFormat($rate),['class'=>'form-control val_change rate','placeholder'=>'Vendor Rate','min'=>0.01,'data-type'=>'rate','onchange'=>'setTwoNumberDecimal(this)','onblur'=>'setTwoNumberDecimal(this)','step'=>'0.01','data-product-id'=>$val->id])}}
										</div>
									</td>
									<td class="nowrap" style="width:10%">{{Form::hidden('amount['.$val->id.']',$amount,['class'=>'amount'])}}
										<div class="amount_div_{{$val->id}}">${{number_format($amount,2)}}</div>
									</td>
									@if(isset($val->PoProduct))
										<td class="nowrap" style="width:15%">
											<div class="form-group col-xs-12 no-padding">{{Form::text('due_date['.$val->id.']',$due_date,['class'=>'form-control due_date','placeholder'=>'Due Date'])}}
											</div>
										</td>
										<td class="nowrap" style="width:15%">{{Form::select('shipping_option['.$val->id.']', config('constants.Shipping_option'), str_pad($shipping_option, 2, "0", STR_PAD_LEFT),array('class'=>'form-control shipping_option'))}}
										</td>
										
										@if(count($po_products) > 1 && $order->multiple_shipping != 0)
										<td class="nowrap" style="width:20%">
											<div class="col-xs-12 add_box no-padding shipping_add_{{$val->PoProduct->item_id}}_div">
												<div class="col-xs-1">
													<i class="fa fa-pencil-square-o address" order-id="{{$order->id}}" order-multiple='1' order-product-id="{{$val->PoProduct->id}}"></i>
												</div>
												<div class="col-xs-10" id="shipp_add_{{$val->PoProduct->id}}">
												@if($val->PoProduct->OrderPOAddress->shipping_company_name !="")
													{{$val->PoProduct->OrderPOAddress->shipping_company_name}}<br/>
												@endif
																								
												@if($val->PoProduct->OrderPOAddress->shipping_fname != '' and $val->PoProduct->OrderPOAddress->shipping_lname != '')
													{{$val->PoProduct->OrderPOAddress->shipping_fname.' '.$val->PoProduct->OrderPOAddress->shipping_lname}}<br/>			
												@endif
												{{$val->PoProduct->OrderPOAddress->shipping_add1}}<br/>{{$val->PoProduct->OrderPOAddress->shipping_add2}}<br>
												
												{{$val->PoProduct->OrderPOAddress->shipping_city.', '.$val->PoProduct->OrderPOAddress->shipping_state.' '.$val->PoProduct->OrderPOAddress->shipping_zipcode . ' ' . $val->PoProduct->OrderPOAddress->shipping_country}}

												</div>
											</div>
										</td>
										@endif
									@else
										<td class="nowrap" colspan="2" style="width:10%"></td>
										@if($order->multiple_shipping != 0)
											<td class="nowrap" style="width:20%"></td>
										@endif
									@endif
								</tr>
								<tr class="bordernone po_line_item po_line_item_{{$val->id}}" rel="{{$val->id}}">
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
												<div class="col-xs-10">
													<a target="_blank" href="{{'http://108.61.143.179:9016/application/job/'.$val->tflow_job_id.'/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT'}}">View ArtWork File</a>
												</div>
											</div>
										@endif
									</td>
								</tr>
							@endforeach
							<tr class="additional_line_item">
								<td class="nowrap" style="width:25%">
									<button class="btn btn-primary add_line_item" type="button"> Add Additional Line Item</button>
								</td>
								<td class="nowrap" colspan="5" style="width:10%"></td>
								@if($order->multiple_shipping != 0)
									<td class="nowrap" style="width:20%"></td>
								@endif
							</tr>
							<tr>
								<td class="nowrap" style="width:25%">
									<label class="form-control-label">Order Notes</label>
									<textarea class="form-control" name="order_notes" placeholder="Enter Order Notes Here...">{{$order_po->notes}}</textarea>
								</td>
								<td class="nowrap" style="width:5%"></td>
								<td class="nowrap" style="width:10%"></td>
								<td class="nowrap" style="width:10%"></td>
								<td class="nowrap" style="width:15%"></td>
								<td class="nowrap" style="width:15%"></td>
								@if($order->multiple_shipping != 0)
									<td class="nowrap" style="width:20%"></td>
								@endif
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-7 col-xs-12">
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
				<div class="col-sm-5 col-xs-12">
					<p class="lead">&nbsp;</p>
					<div class="table-responsive">
						<input type="hidden" value="{{number_format($order_po->subtotal,2)}}" name="po_sub_total" id="po_sub_total"/>
						<table class="table">
							<tr>
								<th class="nowrap" style="width:30%">Sub Total:</th>
								<td class="nowrap" style="width:70%" id="subtotal_amount">${{number_format($order_po->subtotal,2)}}</td>
							</tr>
							<tr>
								<th class="nowrap" style="width:30%">Shipping:</th>
								<td class="nowrap" style="width:70%">
									<div class="input-group"><span class="input-group-addon">$</span>
										<input class="form-control" value="{{priceFormat($order_po->shipping)}}" name="shipping_amount" id="shipping_amount" placeholder="Shipping Amount" onchange='setTwoNumberDecimal(this)' onblur = 'setTwoNumberDecimal(this)' step = '0.01'/>   
									</div>									
								</td>
							</tr>
							<tr>
								<th class="nowrap" style="width:30%">Total:</th>
								<td class="nowrap" style="width:70%" id="total_amount">${{number_format($order_po->subtotal+$order_po->shipping,2)}}</td>
							</tr>
						</table>
					</div>
					<button type="button" class="btn btn-lg btn-success pull-right save_po">Save</button>
				</div>
			</div>
		</div>
		{{Form::close()}}
	</div>
</section>	
<style>
.fa-pencil-square-o{
	padding: 5px;
}
input[type=number].rate::-webkit-inner-spin-button, 
input[type=number].rate::-webkit-outer-spin-button { 
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0; 
}
</style>

@include('Admin.orders.PO.po_edit')

@endsection		  