@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Vendors List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/vendors/add')}}" class="btn btn-primary btn-md">Add vendor</a>
				</div>
				<div class="panel-body">
					<form role="form" method="POST">
					{{ csrf_field() }}
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">Company</label>
							<input id="company_name" type="text" class="form-control" name="company_name" value="{{ \session()->get('vendors.company_name')}}" placeholder="Search by Company">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">First Name</label>
							<input id="fname" type="text" class="form-control" name="fname" value="{{ \session()->get('vendors.fname')}}" placeholder="Search by First Name">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">Last Name</label>
							<input id="lname" type="text" class="form-control" name="lname" value="{{ \session()->get('vendors.lname')}}" placeholder="Search by Last Name">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">Email</label>
							<input id="email" type="text" class="form-control" name="email" value="{{ \session()->get('vendors.email')}}" placeholder="Search by email">
						</div>
					</div>
					@php
						$status = '2';
						if(\session()->has('vendors.status')){
							$status = \session()->get('vendors.status');
						}
						//echo $status;
					@endphp
					<div class="col-sm-2">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">Status</label>			{{Form::select('status',[''=>'--All--','1'=>'Active','0'=>'Deactive'],$status,['class'=>'form-control','id'=>'status'])}}
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
								<a href="{{url('/admin/vendors/lists?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
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
								<th class="nowrap">S.No.</th>
								<th class="nowrap">ID#</th>
								<th class="nowrap">Company Name</th>
								<th class="nowrap">
									<a href="{{url('admin/vendors/lists/fname/'.$sort)}}" class="sort_link">First Name</a>
									@if($field == 'fname')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th class="nowrap">
									<a href="{{url('admin/vendors/lists/lname/'.$sort)}}" class="sort_link">Last Name</a>
									@if($field == 'lname')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th class="nowrap">
									<a href="{{url('admin/vendors/lists/email/'.$sort)}}" class="sort_link">Email</a>
									@if($field == 'email')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th class="nowrap">Phone Number</th>
								<th class="nowrap">Status</th>
								<th class="nowrap">Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($vendors)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($vendors as $vendor)
								<tr>
									<td>{{ ++$i }}</td>
									<th scope="row">{{$vendor->id}}</th>
									<th scope="row">{{$vendor->company_name}}</th>
									<td>{{$vendor->fname}}</td>
									<td>{{$vendor->lname}}</td>
									<td>{{$vendor->email}}</td>
									<td>{{$vendor->phone_number}}</td>
									<td>
										@if($vendor->status==1)
											<div class="badge badge-primary">Activate</div>
										@else
											<div class="badge badge-danger">Deactivate</div>
										@endif
									</td>
									<td>								
										@if($vendor->status==1)
											<a href="{{url('/admin/vendors/action/'.$vendor->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
										@else
											<a href="{{url('/admin/vendors/action/'.$vendor->id.'/1')}}" class="btn btn-primary btn-sm">Activate</a>
										@endif									
											<a href="{{url('/admin/vendors/view/'.$vendor->id)}}" class="btn btn-sm btn-warning">View</a>
											<a href="{{url('/admin/vendors/edit/'.$vendor->id)}}" class="btn btn-sm btn-info">Edit</a>
											<button data-value="{{$vendor->fname.' '.$vendor->lname}}" data-url="{{url('/admin/vendors/delete/'.$vendor->id)}}" class="btn bg-olive btn-sm delete">Delete</button>
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
				<div class="pull-left">  {{ $vendors->links() }} </div>
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