@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Categories List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/category/add')}}" class="btn btn-primary btn-md">Add Category</a>
				</div>
				<div class="panel-body">
					<form role="form" method="POST" action="{{ url('admin/category/lists') }}">
					{{ csrf_field() }}
					<div class="col-sm-4 col-xs-6">
						<div class="form-group">
							<label class="form-control-label">Name</label>
							<input id="name" type="text" class="form-control" name="name" value="{{ \session()->get('category.name')}}" placeholder="Search by name">
						</div>
					</div>
					<div class="col-sm-6 col-xs-6">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
								<a href="{{url('/admin/category/lists?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
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
				<div class="box-body table-responsive">
					<table id="example2" class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
						<thead>
							<tr>
								<th>S.No.</th>
								<th>Id</th>
								<th>
									<a href="{{url('admin/category/lists/name/'.$sort)}}" class="sort_link">Name</a>
									@if($field == 'name')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>Disp. Order</th>
								<th>Status</th>
								<th>
									<a href="{{url('admin/category/lists/created/'.$sort)}}" class="sort_link">Created</a>
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
						@if(count($categories)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($categories as $category)
								<tr class="tr_{{$category->id}}">
									<td>{{ ++$i }}</td>
									<td>{{$category->id}}</td>
									<td>{{$category->name}}</td>
									<td class="nowrap">
										<input type="number" value="{{$category->weight}}" style="width:50px" class="weight_{{$category->id}}" min="1"/>&nbsp;
										<button  class="btn bg-olive btn-xs save_weight" data-id="{{$category->id}}"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
									</td>
									<td>
										@if($category->status==1)
											<div class="badge badge-primary">Activate</div>
										@else
											<div class="badge badge-danger">Deactivate</div>
										@endif
									</td>
									<td>{{$category->created_at}}</td>
									<td>								
										@if($category->status==1)
											<a href="{{url('/admin/category/action/'.$category->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
										@else
											<a href="{{url('/admin/category/action/'.$category->id.'/1')}}"><button type="submit" class="btn btn-primary btn-sm">Activate</button></a>
										@endif									
											<a href="{{url('/admin/category/view/'.$category->id)}}" class="btn btn-sm btn-warning">View</a>
											<a href="{{url('/admin/category/edit/'.$category->id)}}" class="btn btn-sm btn-info">Edit</a>
											
											<a href="javascript:void(0)" class="btn btn-sm bg-olive delete" data-id="{{$category->id}}" data="{{$category->name}}">Delete</a>
									
									</td>
								</tr>
								@if(count($category->child_list) > 0)
									@foreach($category->child_list as $categoryChild)
										<tr class="tr_{{$categoryChild->id}}">
											<td>{{ ++$i }}</td>
											<td>{{$categoryChild->id}}</td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--{{$categoryChild->name}}</td>
											<td class="nowrap">
												<input type="number" value="{{$categoryChild->weight}}" style="width:50px" class="weight_{{$categoryChild->id}}" min="1"/>&nbsp;
												<button  class="btn bg-olive btn-xs save_weight" data-id="{{$categoryChild->id}}"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
											</td>
											<td>
												@if($categoryChild->status==1)
													<div class="badge badge-primary">Activate</div>
												@else
													<div class="badge badge-danger">Deactivate</div>
												@endif
											</td>
											<td>{{$categoryChild->created_at}}</td>
											<td>								
												@if($categoryChild->status==1)
													<a href="{{url('/admin/category/action/'.$categoryChild->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
												@else
													<a href="{{url('/admin/category/action/'.$categoryChild->id.'/1')}}"><button type="submit" class="btn btn-primary btn-sm">Activate</button></a>
												@endif									
													<a href="{{url('/admin/category/view/'.$categoryChild->id)}}" class="btn btn-sm btn-warning">View</a>
													<a href="{{url('/admin/category/edit/'.$categoryChild->id)}}" class="btn btn-sm btn-info">Edit</a>
													<a href="javascript:void(0)" class="btn btn-sm bg-olive delete" data-id="{{$categoryChild->id}}" data="{{$categoryChild->name}}">Delete</a>
											</td>
										</tr>

										@if(count($categoryChild->child_list) > 0)
											@foreach($categoryChild->child_list as $categoryChild2)
												<tr class="tr_{{$categoryChild2->id}}">
													<td>{{ ++$i }}</td>
													<td>{{$categoryChild2->id}}</td>
													<td><div style="width: 40px;float: left">&nbsp;</div> --{{$categoryChild2->name}}</td>
													<td class="nowrap">
														<input type="number" value="{{$categoryChild2->weight}}" style="width:50px" class="weight_{{$categoryChild2->id}}" min="1"/>&nbsp;
														<button  class="btn bg-olive btn-xs save_weight" data-id="{{$categoryChild2->id}}"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
													</td>
													<td>
														@if($categoryChild2->status==1)
															<div class="badge badge-primary">Activate</div>
														@else
															<div class="badge badge-danger">Deactivate</div>
														@endif
													</td>
													<td>{{$categoryChild2->created_at}}</td>
													<td>								
														@if($categoryChild2->status==1)
															<a href="{{url('/admin/category/action/'.$categoryChild2->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
														@else
															<a href="{{url('/admin/category/action/'.$categoryChild2->id.'/1')}}"><button type="submit" class="btn btn-primary btn-sm">Activate</button></a>
														@endif									
															<a href="{{url('/admin/category/view/'.$categoryChild2->id)}}" class="btn btn-sm btn-warning">View</a>
															<a href="{{url('/admin/category/edit/'.$categoryChild2->id)}}" class="btn btn-sm btn-info">Edit</a>
															<a href="javascript:void(0)" class="btn btn-sm bg-olive delete" data-id="{{$categoryChild2->id}}" data="{{$categoryChild2->name}}">Delete</a>
													</td>
												</tr>
											@endforeach 
										@endif
									@endforeach 
								@endif
							@endforeach 
						@else
							<tr>
								<td colspan="6"><center><b>No Data Found here</b></center></td>
							</tr>
						@endif
						</tbody>
					</table>
				<div class="pull-left">  {{ $categories->links() }} </div>
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
				data:{'table':'categories','id':id},
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
			data: {'table':'categories','where_field':'id','where_value':id,'update_fields':{'0':{'update_field':'weight','update_value':$('.weight_'+id).val()}}},
			success: function (data){
				if(data.status == 'success'){
					alert('Category weight set successfully.');
				}
			}
		});
	});
});
</script>	  
@endsection		  