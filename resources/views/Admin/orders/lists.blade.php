@extends('layouts.admin_layout')
@section('content')

<!--    Jquery for top scroll and table scroll drag ------------>
<script src="{{ asset('public/js/admin/dragscroll.js') }}"></script>
<script src="{{ asset('public/js/admin/top_scroll.js') }}"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Orders List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/order/add')}}" class="btn btn-primary btn-md">Create a New Estimate</a>
				</div>
				<div class="panel-body">
					<form role="form" method="POST" action="{{ url('admin/order/lists') }}">
					{{ csrf_field() }}
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('id', 'Order ID#',array('class'=>'form-control-label'))}}	{{Form::text('id',\session()->get('orders.id'),['class'=>'form-control','placeholder'=>'Search by Order ID#'])}}
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('search', 'Customer Name/Email/Company',array('class'=>'form-control-label'))}}	{{Form::text('search',\session()->get('orders.search'),['class'=>'form-control','placeholder'=>'Search by Name/Email/Company'])}}
						</div>
					</div>
					<div class="clearfix"></div>					
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('agent', 'Agent',array('class'=>'form-control-label'))}}	
							{{Form::select('agent', [''=>'Select Agent']+$agents, \session()->get('orders.agent'),array('class'=>'form-control','id'=>'agent'))}}
						</div>
					</div>					
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
					<?php /*<div class="col-md-2 col-sm-6">
						<div class="form-group">
							<?php $types = [1=>'SALES ORDERS',0=>'ESTIMATES'];?>
							{{ Form::label('order_type', 'Order Type',array('class'=>'form-control-label'))}}	
							{{Form::select('order_type', $types, \session()->get('orders.order_type'),array('class'=>'form-control','id'=>'order_type','placeholder'=>'All ORDERS'))}}
						</div>
					</div> */ ?>
					
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
	 
		<div class="col-xs-12">
			<div class="box box-primary">
				<div id="order_list" class="box-body">
					@include('Admin/orders/orders_list_table')
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