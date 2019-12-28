@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Sliders List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel-heading no-padding">
				<a href="{{url('admin/slider/add')}}" class="btn btn-primary btn-md">Add Slider</a>
			</div>			
		</div>
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body table-responsive">
					<table id="example2" class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
						<thead>
							<tr>
								<th>S.No.</th>
								<th>ID#</th>
								<th>Image</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($sliders)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($sliders as $slider)
								<tr>
									<td>{{ ++$i }}</td>
									<th scope="row">{{$slider->id}}</th>
									<td>
										<div class="col-sm-4 image_main_box">
											<label>
												<img class="img-responsive" src="{{URL::to('/public/uploads/slider/'.$slider->image)}}" alt="Photo"/>
											</label>
										</div>
									</td>
									<td>
										@if($slider->status==1)
											<div class="badge badge-primary">Activate</div>
										@else
											<div class="badge badge-danger">Deactivate</div>
										@endif
									</td>
									<td>{{$slider->created_at}}</td>
									<td>								
										@if($slider->status==1)
											<a href="{{url('/admin/slider/action/'.$slider->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
										@else
											<a href="{{url('/admin/slider/action/'.$slider->id.'/1')}}" class="btn btn-primary btn-sm">Activate</a>
										@endif									
											<a href="{{url('/admin/slider/view/'.$slider->id)}}" class="btn btn-sm btn-warning">View</a>
											<a href="{{url('/admin/slider/edit/'.$slider->id)}}" class="btn btn-sm btn-info">Edit</a>
											<button data-value="{{$slider->name}}" data-url="{{url('/admin/slider/delete/'.$slider->id)}}" class="btn bg-olive btn-sm delete">Delete</button>
									</td>
								</tr>
							@endforeach 
						@else
							<tr>
								<td colspan="6"><center><b>No Data Found here</b></center></td>
							</tr>
						@endif
						</tbody>
					</table>
				<div class="pull-left">  {{ $sliders->links() }} </div>
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
	})
});
</script>
@endsection		  