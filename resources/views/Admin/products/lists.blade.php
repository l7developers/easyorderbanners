@extends('layouts.admin_layout')
@section('content')

<!--    Jquery for top scroll and table scroll drag ------------>
<script src="{{ asset('public/js/admin/top_scroll.js') }}"></script>

<section class="content-header">
  <h1>Products List</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/products/add')}}" class="btn btn-primary btn-md">Add Product</a>
				</div>
				<div class="panel-body">
					<form role="form" method="POST">
					{{ csrf_field() }}
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							<label class="form-control-label">Search</label>
							<input id="name" type="text" class="form-control" name="name" value="{{ \session()->get('products.search')}}" placeholder="Search">
						</div>
					</div>
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">Category</label>
							{{Form::select('category', [''=>'Select Category'] + $categories, \session()->get('products.category'),array('class'=>'form-control'))}}
							
						</div>
					</div>
					<div class="col-md-4 col-sm-6">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
								<a href="{{url('/admin/products/lists?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
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
				<!-- /.box-header -->
				<div class="box-body table-responsive">
					<div class="dragscroll" id="container_div">
						<table class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
							<thead>
								<tr>
									<th>Sr.No.</th>
									<th>#Id</th>
									<th>
										<a href="{{url('admin/products/lists/name/'.$sort)}}" class="sort_link">Name</a>
										@if($field == 'name')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th>
										<a href="{{url('admin/products/lists/category/'.$sort)}}" class="sort_link">Category</a>
										@if($field == 'category')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th>Price</th>
									<th>Disp. Order</th>
									<th>Status</th>
									<th>Shipping</th>
									<th>
										<a href="{{url('admin/products/lists/created/'.$sort)}}" class="sort_link">Created</a>
										@if($field == 'created_at')
											{!!$arrow!!}
										@else
											<i class="fa fa-arrows-v"></i>
										@endif
									</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							@if(count($products)>=1)
								@php 
									if(isset($_GET['page'])){
										$i=($limit*$_GET['page'])-$limit;
									}
									else{
										$i=0;
									}
								@endphp
								@foreach($products as $product) 
								<tr>
									<th>{{++$i}}</th>
									<th class="nowrap" scope="row">{{$product['id']}}</th>
									<td class="nowrap">{{$product['name']}}</td>
									<td class="nowrap">{{$product->cat_name}}</td>
									<td class="nowrap">{{priceFormat($product['price'])}}</td>
									<td class="nowrap">
										<input type="number" value="{{$product['weight']}}" style="width:50px" class="weight_{{$product['id']}}" min="1"/>&nbsp;
										<button  class="btn bg-olive btn-xs save_weight" data-id="{{$product['id']}}"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
									</td>
									<td class="nowrap">
										@if($product->status==1)
											<div class="badge badge-primary">Activate</div>
										@else
											<div class="badge badge-danger">Deactivate</div>
										@endif
									</td>
									<td>
										@if(count($product->shipping) > 0)
											@php
												if($product->shipping->type == 'free')
													$str = 'Free Shipping';
												else if($product->shipping->type == 'free_value')
													$str = 'Free Shipping Based On Order Value';
												else if($product->shipping->type == 'paid')
													$str = 'Paid Shipping';
												else if($product->shipping->type == 'flat')
													$str = 'Flat Shipping';
											@endphp
											<a href="{{url('/admin/products/shipping/'.$product->id)}}" class="btn btn-sm bg-olive">{{$str}}</a>
										@else
											<a href="{{url('/admin/products/shipping/'.$product->id)}}" class="btn btn-sm bg-purple">Set Shipping</a>
										@endif
									</td>
									<td class="nowrap">{{$product->created_at}}</td>
									<td class="nowrap">								
										@if($product->status==1)
											<a href="{{url('/admin/products/action/'.$product->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
										@else
											<a href="{{url('/admin/products/action/'.$product->id.'/1')}}" class="btn btn-primary btn-sm">Activate</a>
										@endif
										
										<a href="{{url('/admin/products/view/'.$product->id)}}" class="btn btn-sm btn-warning">View</a>
										<a href="{{url('/admin/products/edit/'.$product->id)}}" class="btn btn-sm btn-info">Edit</a>
										<button data-value="{{$product->name}}" data-url="{{url('/admin/products/delete/'.$product->id)}}" class="btn bg-olive btn-sm delete">Delete</button>
									</td>
								</tr>
								@endforeach 
							@else
								<tr>
									<td colspan="8"><center><b>No Data Found here</b></center></td>
								</tr>
							@endif
							</tbody>
						</table>
					</div>
					<div class="pull-left">  {{ $products->links() }} </div>
				</div>
			</div>
		</div>
	</div>
</section>	
<script> 
$(document).ready(function(){
	$('.delete').click(function(){
		if(confirm('Are you sure to delete #'+$(this).attr('data-value'))){
			window.location.href = $(this).attr('data-url');
		}
	});
	
	$('.save_weight').click(function(){
		var id = $(this).attr('data-id');
		$.ajax({
			type: "POST",
			url: '{{url("admin/actions/update")}}',
			cache: false,
			async: false, //blocks window close
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			data: {'table':'products','where_field':'id','where_value':id,'update_fields':{'0':{'update_field':'weight','update_value':$('.weight_'+id).val()}}},
			success: function (data){
				if(data.status == 'success'){
					alert('Product display weight set successfully.');
				}
			}
		});
	});
});
 
	$(document).ready(function(){
	  $('#container_div').doubleScroll();
	});
</script>  
@endsection		  