@extends('layouts.admin_layout')
@section('content')

<!--    Jquery for top scroll and table scroll drag ------------>
<script src="{{ asset('public/js/admin/dragscroll.js') }}"></script>
<script src="{{ asset('public/js/admin/top_scroll.js') }}"></script>

<!--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>-->

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
							<?php $types = [1=>'SALES ORDERS',0=>'ESTIMATES'];?>
							{{ Form::label('order_type', 'Order Type',array('class'=>'form-control-label'))}}	
							{{Form::select('order_type', $types, \session()->get('orders.order_type'),array('class'=>'form-control','id'=>'order_type','placeholder'=>'All ORDERS'))}}
						</div>
					</div>
					
					<!--<div class="col-md-3 col-sm-6">
						<div class="form-group">
							<?php $status = [1=>'Active',2=>'Archived'];?>
							{{ Form::label('status', 'Status',array('class'=>'form-control-label'))}}	
							{{Form::select('status', [''=>'--All--']+$status, \session()->get('orders.status'),array('class'=>'form-control','id'=>'status'))}}
						</div>
					</div>-->
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary" title="Search"><i class="fa fa-search"></i></button>
								<a href="{{url('/admin/order/archived')}}" class="btn btn-sm btn-info" title="Reset">Reset</a>
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
					@include('Admin/orders/orders_list_table')
				</div>
			</div>
		</div>
	</div>
</section> 
<style>
.row_open{
	transform: rotate(-90deg);
} 
</style> 
<script>
	
</script>

@include('partials.order.assign_agent')
@include('partials.order.assign_designer')
@include('partials.order.assign_vendor')
@include('partials.order.order_notes')
@include('partials.order.order_event')
@include('partials.order.order_status')

@endsection		  