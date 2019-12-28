@extends('layouts.admin_layout')
@section('content')

<!--    Jquery for top scroll and table scroll drag ------------>
<script src="{{ asset('public/js/admin/dragscroll.js') }}"></script>
<script src="{{ asset('public/js/admin/top_scroll.js') }}"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Estimates List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/order/add')}}" class="btn btn-primary btn-md">Create a New Estimate</a>
				</div>
				<div class="panel-body">
					<form role="form" method="POST" action="{{ url('admin/order/estimates') }}">
					{{ csrf_field() }}
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('id', 'Estimate ID#',array('class'=>'form-control-label'))}}	{{Form::text('id',\session()->get('orders.id'),['class'=>'form-control','placeholder'=>'Search by Estimate ID#'])}}
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('search', 'Customer Name/Email/Company',array('class'=>'form-control-label'))}}	{{Form::text('search',\session()->get('orders.search'),['class'=>'form-control','placeholder'=>'Search by Name/Email/Company'])}}
						</div>
					</div>					
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('agent', 'Agent',array('class'=>'form-control-label'))}}	
							{{Form::select('agent', [''=>'Select Agent']+$agents, \session()->get('orders.agent'),array('class'=>'form-control','id'=>'agent'))}}
						</div>
					</div>					
					<div class="clearfix"></div>					
					<?php /*<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('designer', 'Designer',array('class'=>'form-control-label'))}}	
							{{Form::select('designer', [''=>'Select Designer']+$designers, \session()->get('orders.designer'),array('class'=>'form-control','id'=>'designer'))}}
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('vendor', 'Vendor',array('class'=>'form-control-label'))}}	
							{{Form::select('vendor', [''=>'Select vendor']+$vendors, \session()->get('orders.vendor'),array('class'=>'form-control','id'=>'vendor'))}}
						</div>
					</div> */?>
					<div class="clearfix"></div>
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('from_date', 'From Date',array('class'=>'form-control-label'))}}	{{Form::text('from_date',\session()->get('orders.from_date'),['class'=>'form-control','placeholder'=>'From date'])}}
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('end_date', 'End Date',array('class'=>'form-control-label'))}}	{{Form::text('end_date',\session()->get('orders.end_date'),['class'=>'form-control','placeholder'=>'From date'])}}
						</div>
					</div>
					<?php /*<div class="col-md-2 col-sm-6">
						<div class="form-group">
							<?php $types = [1=>'SALES ORDERS',0=>'ESTIMATES'];?>
							{{ Form::label('order_type', 'Order Type',array('class'=>'form-control-label'))}}	
							{{Form::select('order_type', $types, \session()->get('orders.order_type'),array('class'=>'form-control','id'=>'order_type','placeholder'=>'All ORDERS'))}}
						</div>
					</div> */?>
					
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							<?php $limits = [10=>10,25=>25,50=>50,100=>100,250=>250,''=>'--All--'];?>
							{{ Form::label('page_size', 'Page Size',array('class'=>'form-control-label'))}}	
							{{Form::select('page_size', $limits,$limit, array('class'=>'form-control','id'=>'page_size'))}}
							
						</div>
					</div>
					<div class="col-md-2 col-sm-6">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary" title="Search"><i class="fa fa-search"></i></button>
								<a href="{{url('/admin/order/estimates')}}" class="btn btn-sm btn-info" title="Reset">Reset</a>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
		@php
			if($sort == 'ASC'){
				$sort = 'DESC';
				$arrow = '<i class="fa fa-arrow-up"></i>';
			}else{
				$sort = 'ASC';
				$arrow = '<i class="fa fa-arrow-down"></i>';
			}
		@endphp
	 
		<div class="col-xs-12">
			<div class="box box-primary">
				<div id="order_list" class="box-body">
					<div class="dragscroll" id="container2">
						<table class="table table-bordered table-hover table-striped addedfeature order_tabel" border="1" style="width:100%;border-collapse:collapse;">
							<thead>
								<tr>
									<th class="nowrap">S.No.</th>									
									<th class="nowrap">
										<a href="{{url('admin/order/lists/date/'.$sort)}}" class="sort_link">Date</a>
										@if($field == 'created_at')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">
										<a href="{{url('admin/order/lists/name/'.$sort)}}" class="sort_link">Customer</a>
										@if($field == 'name')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>								
									<th class="nowrap">
										<a href="{{url('admin/order/lists/order/'.$sort)}}" class="sort_link">Estimate#</a>
										@if($field == 'id' and $extra_admin == 'order')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">Assign</th>
									<th class="nowrap">
										<a href="{{url('admin/order/lists/customer-status/'.$sort)}}" class="sort_link">Customer Status</a>
										@if($field == 'customer_status')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">
										<a href="{{url('admin/order/lists/payment-status/'.$sort)}}" class="sort_link">Payment Status</a>
										@if($field == 'payment_status')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">Total</th>									
									<th class="nowrap">Action</th>
								</tr>
							</thead>
							<tbody>
							@if(count($orders)>=1)
								@php 
									$status = config('constants.Order_status');
									if(isset($_GET['page'])){
										$i=($limit*$_GET['page'])-$limit;
									}
									else{
										$i=0;
									}
								@endphp
								
								@foreach($orders as $order)
									<tr>
										<td class="nowrap">
											<strong>{{ ++$i }}</strong>&nbsp;&nbsp;
											@php
												$icon_class = 'fa-plus';
												$rel = 0;
												$btn_class = 'btn-success';
												$child_row = 'display:none;';
												if(\session()->get('orders.vendor') != ''){
													$icon_class = 'fa-minus';
													$rel=1;
													$btn_class = 'btn-danger';
													$child_row = '';
												}
											@endphp
											<button type="button" class="btn {{$btn_class}} btn-xs order_row" data-id="{{$order->id}}" rel="{{$rel}}" style="border-radius: 12px !important;"><i class="fa {{$icon_class}}"></i></button>
										</td>										
										<td class="nowrap">{{date('m-d-Y',strtotime($order->created_at))}}</td>
										<td class="nowrap" scope="row">
											<a href="{{url('admin/users/edit/'.$order->user_id)}}" target="_blank">
											<b>Company Name: </b>{{$order->customer_company_name}}<br/>
											<b>Name: </b>{{$order->customer_name}}<br/>
											<b>Email: </b>{{$order->customer_email}}<br/>
											<b>Phone Number: </b>{{$order->customer_phone_number}}
											</a>
										</td>									
										<td class="nowrap" scope="row">
											<a href="{{url('/admin/order/edit/'.$order->id)}}" class="btn btn-xs bg-olive margin">#E-{{$order->id}}</a>
											<?php /*@if($order->payment_status == 0)
												<a href="{{url('/admin/order/edit/'.$order->id)}}" class="btn btn-xs bg-olive margin">#E-{{$order->id}}</a>
											@else
												<a href="{{url('/admin/order/edit/'.$order->id)}}" class="btn btn-xs bg-olive margin">#{{$order->id}}</a>
											@endif */?>
										</td>
										<td class="nowrap">
											@if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2)
											<div class='agent_{{$order->id}}'>
												<b>Agent</b><br/>
												@if(!empty($order->agent_name))
													<button class="btn btn-xs btn-primary agent_btn" data="{{$order->agent_id}}" order-id="{{$order->id}}" data-toggle="modal" data-target="#assign_agent_model" title="Assign Agent">{{$order->agent_name}}</button>
												@else
													<button class="btn btn-xs btn-warning agent_btn" data="0" order-id="{{$order->id}}" data-toggle="modal" data-target="#assign_agent_model" title="Assign Agent">Assign Agent</button>
												@endif
											</div>
											@else
												<b>Agent</b><br/>
												{{$order->agent_name}}
											@endif
											<?php /*<br/>
											<div id='designer_{{$order->id}}'>
												<b>Designer</b><br/>
												@if(!empty($order->designer_id))
													<div class="btn btn-xs btn-primary designer_btn" data="{{$order->designer_id}}" order-product-id="{{$order->id}}" order-id="{{$order->id}}" data-toggle="modal" data-target="#assign_designer_model" title="Assign Designer">{{$order->designer_name}}</div>
												@else
													<div class="btn btn-xs btn-warning designer_btn" data="0" order-id="{{$order->id}}" data-toggle="modal" data-target="#assign_designer_model" title="Assign Designer">Assign Designer</div>
												@endif
											</div> */?>
										</td>
										<td class="nowrap customer_status_{{$order->id}}">
											@if($order->customer_status != 0)
												<button style="background:{{config('constants.customer_status_color.'.$order->customer_status)}} !important;border-color:{{config('constants.customer_status_color.'.$order->customer_status)}} !important" type="button" class="btn btn-xs bg-olive margin customer_status" data="{{$order->customer_status}}" order-id="{{$order->id}}" data-toggle="modal" data-target="#customer_status" title="Set Customer Status">{{config('constants.customer_status.'.$order->customer_status)}}</button>
											@else
												<button style="background:{{config('constants.customer_status_color.0')}} !important;border-color:{{config('constants.customer_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin customer_status" data="0" order-id="{{$order->id}}" data-toggle="modal" data-target="#customer_status" title="Set customer Status">Set Status</button>
											@endif
										</td>
										<td class="nowrap payment_status_{{$order->id}}">
											@if($order->payment_status != 0)
												<button style="background:{{config('constants.payment_status_color.'.$order->payment_status)}} !important;border-color:{{config('constants.payment_status_color.'.$order->payment_status)}} !important" type="button" class="btn btn-xs bg-navy margin payment_status" data="{{$order->payment_status}}" order-id="{{$order->id}}" product_item_id="{{$order->id}}" data-toggle="modal" data-target="#payment_status" title="Set Payment Status">{{config('constants.payment_status.'.$order->payment_status)}}
												</button><br/>
												<b>Payment By :</b> 
												@php
													if($order->payment_method == 'paypal')
														echo "Paypal";
													elseif($order->payment_method == 'authorized')
														echo "Credit Card";
													else
														echo "Pay By Invoice";
												@endphp<br/>
												@if(!empty($order->payment_id))
													<b>Payment Id :</b> {{$order->payment_id}}
												@endif
											@else
												<button style="background:{{config('constants.payment_status_color.0')}} !important;border-color:{{config('constants.payment_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin payment_status" data="0" order-id="{{$order->id}}" product_item_id="{{$order->id}}" data-toggle="modal" data-target="#payment_status" title="Set Payment Status">Set Status</button>
											@endif
										</td>
										<td class="nowrap">${{number_format($order->total,2)}}</td>										
										<td class="nowrap">	
											@if($order->status == 1)
												<a href="{{url('/admin/order/change-status/'.$order->id.'/2')}}" class="btn btn-xs btn-warning" title="Archive" onclick="return confirm('Are you sure you want to Archive this Estimate?')"><i class="fa fa-file-archive-o"></i></a>
											@else
												<a href="{{url('/admin/order/change-status/'.$order->id.'/1')}}" class="btn btn-xs btn-warning" title="UnArchive" onclick="return confirm('Are you sure you want to UnArchive this Estimate?')"><i class="fa fa-file-archive-o"></i></a>
											@endif
											<a href="{{url('/admin/order/delete/'.$order->id)}}" class="btn btn-xs btn-danger" title="Delete order" onclick="return confirm('Are you sure to delete this ?')"><i class="fa fa-times" aria-hidden="true"></i></a>
											
											@if($order->qb_id == "" && $order->payment_method !="")
											<a href="#" onClick="window.open('{{url('/admin/quickbook/exporttoqb/'.$order->id)}}','qbwindow','height=600,width=800,top=10,left=100')" class="btn btn-xs btn-info" title="{{$order->id}}">QB</a>
											@endif
										</td>
									</tr>
									@php
										$j = 1;
									@endphp
									@foreach($order->orderProductsDetails as $order_product)
										@if($j == 1)
											<tr class="child_row_{{$order->id}}" style="{{$child_row}};background-color: #ccc;">
												<th class="nowrap">Sr.No</th>
												<th class="nowrap" colspan="7">Product Name</th>							
												<th class="nowrap">Action</th>
											</tr>
										@endif
										<tr class="child_row_{{$order->id}}" style="{{$child_row}};background-color: #ccc;">
											<td class="nowrap">{{$j}}</td>
											<td class="nowrap" colspan="7">
												<b>Product: </b>
												@if($order_product->product_name !="")		
													{{$order_product->product_name}}
												@else
													{{$order_product->productName}}
												@endif
												<br/>
												@if($order_product->tflow_job_id >= 1)
												<b>Art Link: </b><a target="_blank" href="http://108.61.143.179:9016/application/job/{{$order_product->tflow_job_id}}/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT">Click Here o View Production Ready Art Link</a>
												@endif										
											</td>																		
											<td class="nowrap">	
												@php
													if(count($order_product->notes) > 0){ 
														$count = '('.count($order_product->notes).')';
													}else{
														$count = '';
													}
												@endphp	
												{{Form::button('<span class="note_count_'.$order_product->item_id.'">'.$count.'</span><i class="fa fa-text-width"></i>',['class'=>'btn btn-xs btn-primary notes_btn','data'=>$order_product->item_id,'data-target'=>'#order_notes','title'=>'Notes'])}}
												
												{{Form::button('<i class="fa fa-calendar"></i>',['class'=>'btn btn-xs btn-danger events_btn','data'=>$order_product->item_id,'data-target'=>'#order_events','title'=>'Events'])}}
												
												{{Form::button('<i class="fa fa-clone"></i>',['class'=>'btn btn-xs btn-success productClone','data-id'=>$order_product->id,'data-type'=>'estimates','title'=>'Duplicate this item'])}}

												{{Form::button('<i class="fa fa-trash"></i>',['class'=>'btn btn-xs btn-danger deleteItem','data-order'=>$order_product->order_id,'data-id'=>$order_product->id,'data-po'=>$order_product->po_id,'title'=>'Delete this item','onclick' =>"return confirm('Are you sure to delete this ?')"])}}
											</td>
										</tr>
										@php
										$j++
										@endphp
									@endforeach
								@endforeach 
							@else
								<tr>
									<td colspan="11"><center><b>No Data Found here</b></center></td>
								</tr>
							@endif
							</tbody>
						</table>
						
						</div>						
						<div class="pull-left orderlistaction"> {{ $orders->links() }} </div>
				</div>
			</div>
		</div>
	</div>
</section> 

@include('partials.order.assign_agent')
@include('partials.order.assign_designer')
@include('partials.order.assign_vendor')
@include('partials.order.order_notes')
@include('partials.order.order_event')
@include('partials.order.order_status')

@endsection		  