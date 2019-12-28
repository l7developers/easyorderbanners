@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Discounts List</h1>
</section>

<section class="content">
	<div class="row">
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
				<div class="box-body table-responsive">
					<table id="example2" class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
						<thead>
							<tr>
								<th>S.No.</th>
								<th>ID#</th>
								<th>
									<a href="{{url('admin/discount/lists/quantity/'.$sort)}}" class="sort_link">Quantity</a>
									@if($field == 'quantity')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/discount/lists/percent/'.$sort)}}" class="sort_link">Percent</a>
									@if($field == 'percent')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>Products</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($discounts)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($discounts as $discount)
								<tr class="tr_{{$discount->id}}">
									<td>{{ ++$i }}</td>
									<th scope="row">{{$discount->id}}</th>
									<td>{{$discount->quantity}}</td>
									<td>{{$discount->percent}}%</td>
									<td>
										@php
											if(empty($discount->products)){
												echo 'All Products';
											}else{
												$ids = explode(',',$discount->products);
												$products = \App\Products::whereIn('id',$ids)->select('name')->get();
												echo '<ul>';
												foreach($products as $product){
													echo '<li>'.$product->name.'</li>';
												}
												echo '</ul>';
											}
										@endphp
									</td>
									<td>
										@if($discount->status==1)
											<div class="badge badge-primary">Activate</div>
										@else
											<div class="badge badge-danger">Deactivate</div>
										@endif
									</td>
									<td>{{$discount->created_at}}</td>
									<td>								
										@if($discount->status==1)
											<a href="{{url('/admin/discount/action/'.$discount->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
										@else
											<a href="{{url('/admin/discount/action/'.$discount->id.'/1')}}" class="btn btn-primary btn-sm">Activate</a>
										@endif
											<a href="{{url('/admin/discount/edit/'.$discount->id)}}" class="btn btn-sm btn-info">Edit</a>
											
											<a href="javascript:void(0)" class="btn btn-sm bg-olive delete" data-id="{{$discount->id}}" data="{{'#'.$discount->id}}">Delete</a>
									</td>
								</tr>
							@endforeach 
						@else
							<tr>
								<td colspan="9"><center><b>No Data Found here</b></center></td>
							</tr>
						@endif
						</tbody>
					</table>
				<div class="pull-left">  {{ $discounts->links() }} </div>
				</div>
			</div>
		</div>
	</div>
</section>  
<script type="text/javascript">
$(document).ready(function(){
	$('.delete').click(function(index,value){
		var id = $(this).attr('data-id');
		var str = $(this).attr('data');
		str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
			return letter.toUpperCase();
		});
		if(confirm("You are about to delete "+str+". Are you sure?")){
			$.ajax({
				url:'{{url("admin/actions/delete")}}',
				type:'post',
				dataType:'json',
				data:{'table':'discounts','id':id},
				beforeSend: function () {
				  $.blockUI();
				},
				complete: function () {
				  $.unblockUI();
				},
				success:function(data){
					if(data.status == 'success'){
						$('.tr_'+id).remove();
					}
				}
			});
		}
	});
});
</script>
@endsection		  