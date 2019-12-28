@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Testimonials List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/testimonial/add')}}" class="btn btn-primary btn-md">Add Testimonial</a>
				</div>
				<div class="panel-body">
					{{Form::model('filter')}}
						<div class="col-lg-4 col-md-3 col-sm-6">
							<div class="form-group">
								{{Form::label('name','Name',['class'=>'form-control-label'])}}	{{Form::text('name',\session()->get('testimonial.name'),['class'=>'form-control','placeholder'=>'Search by name'])}}
							</div>
						</div>
						<div class="col-lg-4 col-md-3 col-sm-6">
							<div class="form-group">	{{Form::label('designation_company','Designation/Company',['class'=>'form-control-label'])}}	{{Form::text('designation_company',\session()->get('testimonial.designation_company'),['class'=>'form-control','placeholder'=>'Search By Designation/Company'])}}
							</div>
						</div>
						@php
							$status = '2';
							if(\session()->has('testimonial.status')){
								$status = \session()->get('testimonial.status');
							}
							//echo $status;
						@endphp
						<div class="col-lg-2 col-md-3 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="Filter_Search">Status</label>				{{Form::select('status',[''=>'--All--','1'=>'Active','0'=>'Deactive'],$status,['class'=>'form-control','id'=>'status'])}}
							</div>
						</div>
						<div class="col-lg-2 col-md-3 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="Filter_Search">&nbsp;</label>
								<div class="col-sm-12">
									<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
									<a href="{{url('/admin/testimonial/lists?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
								</div>
							</div>
						</div>
					{{Form::close()}}
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
								<th>ID#</th>
								<th>
									<a href="{{url('admin/testimonial/lists/name/'.$sort)}}" class="sort_link">Name</a>
									@if($field == 'name')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>Designation/Company
									<a href="{{url('admin/testimonial/lists/designation/'.$sort)}}" class="sort_link">Designation/Company</a>
									@if($field == 'designation_company')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($testimonials)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($testimonials as $testimonial)
								<tr>
									<td>{{ ++$i }}</td>
									<th scope="row">{{$testimonial->id}}</th>
									<td scope="row">{{$testimonial->name}}</td>
									<td scope="row">{{$testimonial->designation_company}}</td>
									<td>
										@if($testimonial->status==1)
											<div class="badge badge-primary">Activate</div>
										@else
											<div class="badge badge-danger">Deactivate</div>
										@endif
									</td>
									<td>{{$testimonial->created_at}}</td>
									<td>								
										@if($testimonial->status==1)
											<a href="{{url('/admin/testimonial/action/'.$testimonial->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
										@else
											<a href="{{url('/admin/testimonial/action/'.$testimonial->id.'/1')}}" class="btn btn-primary btn-sm">Activate</a>
										@endif									
											<a href="{{url('/admin/testimonial/view/'.$testimonial->id)}}" class="btn btn-sm btn-warning">View</a>
											<a href="{{url('/admin/testimonial/edit/'.$testimonial->id)}}" class="btn btn-sm btn-info">Edit</a>
											
											<button data-value="{{$testimonial->name}}" data-url="{{url('/admin/testimonial/delete/'.$testimonial->id)}}" class="btn bg-olive btn-sm delete">Delete</button>
									</td>
								</tr>
							@endforeach 
						@else
							<tr>
								<td colspan="7"><center><b>No Data Found here</b></center></td>
							</tr>
						@endif
						</tbody>
					</table>
				<div class="pull-left">  {{ $testimonials->links() }} </div>
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