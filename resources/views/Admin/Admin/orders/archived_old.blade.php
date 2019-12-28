@extends('layouts.admin_layout')
@section('content')

<!--    Jquery for top scroll and table scroll drag ------------>
<script src="{{ asset('public/js/admin/dragscroll.js') }}"></script>
<script src="{{ asset('public/js/admin/top_scroll.js') }}"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Archived Orders List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/order/add')}}" class="btn btn-primary btn-md">Create a New order</a>
				</div>
				<div class="panel-body">
					<form role="form" method="POST" action="{{ url('admin/order/archived') }}">
					{{ csrf_field() }}
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('id', 'Order ID#',array('class'=>'form-control-label'))}}	{{Form::text('id',\session()->get('orders.id'),['class'=>'form-control','placeholder'=>'Search by Order ID#'])}}
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('search', 'Customer Name/Email',array('class'=>'form-control-label'))}}	{{Form::text('search',\session()->get('orders.search'),['class'=>'form-control','placeholder'=>'Search by Name/Email'])}}
						</div>
					</div>
					<div class="clearfix"></div>
					@if(\Auth::user()->role_id == 1)
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('agent', 'Agent',array('class'=>'form-control-label'))}}	
							{{Form::select('agent', [''=>'Select Agent']+$agents, \session()->get('orders.agent'),array('class'=>'form-control','id'=>'agent'))}}
						</div>
					</div>
					@endif
					<div class="col-md-3 col-sm-6">
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
					</div>
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
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							<?php //$status = config('constants.Order_status');?>
							<?php $status = [1=>'Active',2=>'Archived'];?>
							{{ Form::label('status', 'Status',array('class'=>'form-control-label'))}}	
							{{Form::select('status', [''=>'--All--']+$status, \session()->get('orders.status'),array('class'=>'form-control','id'=>'status'))}}
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary" title="Search"><i class="fa fa-search"></i></button>
								<a href="{{url('/admin/order/lists')}}" class="btn btn-sm btn-info" title="Reset">Reset</a>
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
	 
		<style>
		#container2{
			overflow: auto;
			white-space: nowrap;
			cursor:move;
		}
		</style>
		<div class="col-xs-12">
			<div class="box box-primary">
				<div id="order_list" class="box-body">
					<div class="dragscroll" id="container2">
						<table class="table table-bordered table-hover table-striped addedfeature order_tabel" border="1" style="width:100%;border-collapse:collapse;" id="container2">
							<thead>
								<tr>
									<th class="nowrap">S.No.</th>
									<th class="nowrap">Date
										<a href="{{url('admin/archived/date/'.$sort)}}" class="sort_link">Date</a>
										@if($field == 'created_at')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">
										<a href="{{url('admin/archived/name/'.$sort)}}" class="sort_link">Customer</a>
										@if($field == 'name')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>								
									<th class="nowrap">
										<a href="{{url('admin/archived/order/'.$sort)}}" class="sort_link">Order#</a>
										@if($field == 'order_id' and $extra_admin == 'order')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">
										<a href="{{url('admin/archived/po/'.$sort)}}" class="sort_link">PO#</a>
										@if($field == 'order_id' and $extra_admin == 'po')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">Assign</th>
									<th class="nowrap">
										<a href="{{url('admin/archived/customer-status/'.$sort)}}" class="sort_link">Customer Status</a>
										@if($field == 'customer_status')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">
										<a href="{{url('admin/archived/artwork-status/'.$sort)}}" class="sort_link">ArtWork Status</a>
										@if($field == 'art_work_status')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">
										<a href="{{url('admin/archived/vendor-status/'.$sort)}}" class="sort_link">Vendor Status</a>
										@if($field == 'vendor_status')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">
										<a href="{{url('admin/archived/payment-status/'.$sort)}}" class="sort_link">Payment Status</a>
										@if($field == 'payment_status')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">
										<a href="{{url('admin/archived/due-date/'.$sort)}}" class="sort_link">Due Date</a>
										@if($field == 'due_date')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th class="nowrap">Total</th>
									<th class="nowrap">Order Tracking Number</th>
									<!--<th class="nowrap">Book Shipping</th>
									<th class="nowrap">Status</th>-->
									<th class="nowrap">tFlow Job Id</th>
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
										<td class="nowrap">{{ ++$i }}</td>
										<td class="nowrap">{{date('M-d-Y',strtotime($order->created_at))}}</td>
										<td class="nowrap" scope="row">
											<b>Company Name: </b>{{$order->customer_company_name}}<br/>
											<b>Name: </b>{{$order->customer_name}}<br/>
											<b>Email: </b>{{$order->customer_email}}<br/>
											<b>Phone Number: </b>{{$order->customer_phone_number}}
											<br/><br/>
												<b>Product: </b>{{$order->product->name}}<br/>
												<b>Link: </b><a target="_blank" href="{{url($order->product->slug)}}">{{url($order->product->slug)}}</a>
										</td>									
										<td class="nowrap" scope="row">
											@if($order->payment_status == 0)
												<a href="{{url('/admin/order/edit/'.$order->order->id)}}" class="btn btn-xs bg-olive margin">#E-{{$order->item_id}}</a>
											@else
												<a href="{{url('/admin/order/edit/'.$order->order->id)}}" class="btn btn-xs bg-olive margin">#{{$order->item_id}}</a>
											@endif
										</td>
										<td class="nowrap" scope="row">
											<a href="{{url('/admin/order/edit/'.$order->order->id.'/'.$order->po_id)}}" class="btn btn-xs bg-purple margin">{{$order->po_id}}</a>
										</td>
										<td class="nowrap">
											@if(\Auth::user()->role_id == 1)
											<div class='agent_{{$order->order->id}}'>
												<b>Agent</b><br/>
												@if(!empty($order->agent_name))
													<button class="btn btn-xs btn-primary agent_btn" data="{{$order->order->agent_id}}" order-id="{{$order->order->id}}" data-toggle="modal" data-target="#assign_agent_model" title="Assign Agent">{{$order->agent_name}}</button>
												@else
													<button class="btn btn-xs btn-warning agent_btn" data="0" order-id="{{$order->order->id}}" data-toggle="modal" data-target="#assign_agent_model" title="Assign Agent">Assign Agent</button>
												@endif
											</div>
											@endif
											<br/>
											<div id='designer_{{$order->id}}'>
												<b>Designer</b><br/>
												@if(!empty($order->designer_name))
													<div class="btn btn-xs btn-primary designer_btn" data="{{$order->designer_id}}" order-product-id="{{$order->id}}" order-id="{{$order->order->id}}" data-toggle="modal" data-target="#assign_designer_model" title="Assign Designer">{{$order->designer_name}}</div>
												@else
													<div class="btn btn-xs btn-warning designer_btn" data="0" order-product-id="{{$order->id}}" order-id="{{$order->order->id}}" data-toggle="modal" data-target="#assign_designer_model" title="Assign Designer">Assign Designer</div>
												@endif
											</div>
											<br/>
											<div id='vendor_{{$order->id}}'>
												<b>Vendor</b><br/>
												@if(!empty($order->vendor_name))
													<div class="btn btn-xs btn-primary vendor_btn" data="{{$order->vendor_id}}" order-product-id="{{$order->id}}" order-id="{{$order->order->id}}" data-toggle="modal" data-target="#assign_vendor_model" title="Assign Vendor">{{$order->vendor_name}}</div>
												@else
													<div class="btn btn-xs btn-warning vendor_btn" data="0" order-product-id="{{$order->id}}" order-id="{{$order->order->id}}" data-toggle="modal" data-target="#assign_vendor_model" title="Assign Vendor">Assign Vendor</div>
												@endif
											</div>
										</td>
										<td class="nowrap customer_status_{{$order->item_id}}">
											@if($order->customer_status != 0)
												<button style="background:{{config('constants.customer_status_color.'.$order->customer_status)}} !important;border-color:{{config('constants.customer_status_color.'.$order->customer_status)}} !important" type="button" class="btn btn-xs bg-olive margin customer_status" data="{{$order->customer_status}}" order-id="{{$order->order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#customer_status" title="Set Customer Status">{{config('constants.customer_status.'.$order->customer_status)}}</button>
											@else
												<button style="background:{{config('constants.customer_status_color.0')}} !important;border-color:{{config('constants.customer_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin customer_status" data="0" order-id="{{$order->order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#customer_status" title="Set customer Status">Set Status</button>
											@endif
										</td>
										<td class="nowrap" id="art_work_status_{{$order->item_id}}">
											@if($order->art_work_status != 0)
												<button style="background:{{config('constants.art_work_status_color.'.$order->art_work_status)}} !important;border-color:{{config('constants.art_work_status_color.'.$order->art_work_status)}} !important" type="button" class="btn btn-xs btn-danger margin art_work_status" data="{{$order->art_work_status}}" date="{{$order->art_work_date}}" order-product-id="{{$order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#art_work_status" title="Set Art Work Status">{{config('constants.art_work_status.'.$order->art_work_status)}}<br/>
												@if(!empty($order->art_work_date))
													{{date('d-m-Y',strtotime($order->art_work_date))}}
												@endif
												</button>
											@else
												<button style="background:{{config('constants.art_work_status_color.0')}} !important;border-color:{{config('constants.art_work_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin art_work_status" data="0" date="" order-product-id="{{$order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#art_work_status" title="Set Art Work Status">Set Status</button>
											@endif
										</td>
										<td class="nowrap" id="vendor_status_{{$order->item_id}}">
											@if($order->vendor_status != 0)
												<button style="background:{{config('constants.vendor_status_color.'.$order->vendor_status)}} !important;border-color:{{config('constants.vendor_status_color.'.$order->vendor_status)}} !important" type="button" class="btn btn-xs btn-info margin vendor_status" data="{{$order->vendor_status}}" order-product-id="{{$order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#vendor_status" title="Set Vendor Status">{{config('constants.vendor_status.'.$order->vendor_status)}}</button>
											@else
												<button style="background:{{config('constants.vendor_status_color.0')}} !important;border-color:{{config('constants.vendor_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin vendor_status" data="0" order-product-id="{{$order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#vendor_status" title="Set Vendor Status">Set Status</button>
											@endif
										</td>
										<td class="nowrap payment_status_{{$order->item_id}}">
											@if($order->payment_status != 0)
												<button style="background:{{config('constants.payment_status_color.'.$order->payment_status)}} !important;border-color:{{config('constants.payment_status_color.'.$order->payment_status)}} !important" type="button" class="btn btn-xs bg-navy margin payment_status" data="{{$order->payment_status}}" order-id="{{$order->order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#payment_status" title="Set Payment Status">{{config('constants.payment_status.'.$order->payment_status)}}
												</button><br/>
												<?php //pr($order->order)?>
												<b>Payment By :</b> 
												@php
													if($order->order->payment_method == 'paypal')
														echo "Paypal";
													elseif($order->order->payment_method == 'authorized')
														echo "Credit Card";
													else
														echo "Pay By Invoice";
												@endphp<br/>
												@if(!empty($order->order->payment_id))
													<b>Payment Id :</b> {{$order->order->payment_id}}
												@endif
											@else
												<button style="background:{{config('constants.payment_status_color.0')}} !important;border-color:{{config('constants.payment_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin payment_status" data="0" order-id="{{$order->order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#payment_status" title="Set Payment Status">Set Status</button>
											@endif
										</td>
										<td class="nowrap" id="due_date_{{$order->item_id}}">
											@if(!empty($order->due_date))
												@php
												if($order->due_date_type == 'soft_date'){
													$class_name = 'btn-success';
												}else{
													$class_name = 'btn-danger';
												}
												@endphp
												<button type="button" class="btn btn-xs {{$class_name}} margin due_date" data="{{$order->due_date}}" data-type="{{$order->due_date_type}}" order-product-id="{{$order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#due_date" title="Due Date">{{date('M-d-Y',strtotime($order->due_date))}}</button>
											@else
												<button type="button" class="btn btn-xs bg-orange margin due_date" data="0" data-type="" order-product-id="{{$order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#due_date" title="Set Due Date">Set Due Date</button>
											@endif
										</td>
										<td class="nowrap">${{$order->total}}</td>
										<td class="nowrap" id="tracking_id_{{$order->item_id}}">
											@if(!empty($order->tracking_id))
												<b>Tracking Number :<br/></b> {{$order->tracking_id}}
												<br/>
												<button style="background:#868686 !important" type="button" class="btn btn-xs bg-maroon margin tracking_id" data="{{$order->tracking_id}}" order-product-id="{{$order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#tracking_id" title="Tracking Id">Change Tracking</button>
												<br/>
												<a style="background:#868686 !important" href="{{$order->tracking_link}}" target="_blank" class="btn btn-xs bg-olive margin">Track Order</a>
											@else
												<button style="background:#868686 !important" type="button" class="btn btn-xs bg-orange margin tracking_id" data="0" order-product-id="{{$order->id}}" product_item_id="{{$order->item_id}}" data-toggle="modal" data-target="#tracking_id" title="Set Tracking Id">Set Tracking</button>
											@endif
										</td>
										<td>{{(!empty($order->tflow_job_id))?$order->tflow_job_id:'Not set'}}</td>
										<td class="nowrap">	
											@php
												if(count($order->notes) > 0){ 
													$count = '('.count($order->notes).')';
												}else{
													$count = '';
												}
											@endphp	
											{{Form::button('<span class="note_count_'.$order->item_id.'">'.$count.'</span><i class="fa fa-text-width"></i>',['class'=>'btn btn-xs btn-primary notes_btn','data'=>$order->item_id,'data-target'=>'#order_notes','title'=>'Notes'])}}
											
											{{Form::button('<i class="fa fa-calendar"></i>',['class'=>'btn btn-xs btn-danger events_btn','data'=>$order->item_id,'data-target'=>'#order_events','title'=>'Events'])}}
											
											<a href="{{url('/admin/order/edit/'.$order->order->id)}}" class="btn btn-xs btn-info" title="Edit"><i class="fa fa-pencil"></i></a>
											
											@if($order->status == 1)
												<a href="{{url('/admin/order/change-status/'.$order->id.'/2')}}" class="btn btn-xs btn-warning" title="Archived" onclick="return confirm('Are you sure to archived this ?')"><i class="fa fa-file-archive-o"></i></a>
											@else
												<a href="{{url('/admin/order/change-status/'.$order->id.'/1')}}" class="btn btn-xs btn-warning" title="Archived" onclick="return confirm('Are you sure to active this ?')"><i class="fa fa-file-archive-o"></i></a>
											@endif
											<a href="{{url('/admin/order/delete/'.$order->order->id.'/'.$order->product_id)}}" class="btn btn-xs btn-danger" title="Delete order" onclick="return confirm('Are you sure to delete this ?')"><i class="fa fa-times" aria-hidden="true"></i></a>					
										</td>
									</tr>
								@endforeach 
							@else
								<tr>
									<td colspan="14"><center><b>No Data Found here</b></center></td>
								</tr>
							@endif
							</tbody>
						</table>
						
						</div>
						<div class="pull-left">  {{ $orders->links() }} </div>
				</div>
			</div>
		</div>
	</div>
</section>  
<script>
	$(document).ready(function(){
	  $('#container2').doubleScroll();
	});
</script>

@include('partials.order.assign_agent')
@include('partials.order.assign_designer')
@include('partials.order.assign_vendor')
@include('partials.order.order_notes')
@include('partials.order.order_event')
@include('partials.order.order_status')

@endsection		  