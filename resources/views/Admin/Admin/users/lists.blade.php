@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Customers List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/users/add')}}" class="btn btn-primary btn-md">Add Customer</a>
				</div>
				<div class="panel-body">
					<form role="form" method="POST" >
					{{ csrf_field() }}
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">Company</label>
							<input id="company_name" type="text" class="form-control" name="company_name" value="{{ \session()->get('users.company_name')}}" placeholder="Search by Company">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">First Name</label>
							<input id="fname" type="text" class="form-control" name="fname" value="{{ \session()->get('users.fname')}}" placeholder="Search by First Name">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">Last Name</label>
							<input id="lname" type="text" class="form-control" name="lname" value="{{ \session()->get('users.lname')}}" placeholder="Search by Last Name">
						</div>
					</div>

					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">Email</label>
							<input id="email" type="text" class="form-control" name="email" value="{{ \session()->get('users.email')}}" placeholder="Search by email">
						</div>
					</div>
					@php
						$status = '';
						if(\session()->has('users.status')){
							$status = \session()->get('users.status');
						}
						//echo $status;
					@endphp
					<div class="col-sm-2">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">Status</label>				{{Form::select('status',['1'=>'Active','0'=>'Deactive'],$status,['class'=>'form-control','id'=>'status','placeholder'=>'--All--'])}}
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
								<a href="{{url('/admin/users/lists?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
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
								<th>ID#</th>
								<th>
									<a href="{{url('admin/users/lists/company_name/'.$sort)}}" class="sort_link">Company Name</a>
									@if($field == 'company_name')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/users/lists/fname/'.$sort)}}" class="sort_link">First Name</a>
									@if($field == 'fname')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/users/lists/lname/'.$sort)}}" class="sort_link">Last Name</a>
									@if($field == 'lname')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/users/lists/email/'.$sort)}}" class="sort_link">Email</a>
									@if($field == 'email')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>Phone Number</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($users)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($users as $user)
								<tr class="tr_{{$user->id}}">
									<td>{{ ++$i }}</td>
									<th scope="row">{{$user->id}}</th>
									<td><b>{{$user->company_name}}<b></td>
									<td>{{$user->fname}}</td>
									<td>{{$user->lname}}</td>
									<td>{{$user->email}}</td>
									<td>{{$user->phone_number}}</td>
									<td>
										@if($user->status==1)
											<div class="badge badge-primary">Activate</div>
										@else
											<div class="badge badge-danger">Deactivate</div>
										@endif
									</td>
									<td>								
										@if($user->status==1)
											<a href="{{url('/admin/users/action/'.$user->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
										@else
											<a href="{{url('/admin/users/action/'.$user->id.'/1')}}"><button type="submit" class="btn btn-primary btn-sm">Activate</button></a>
										@endif	
											
										<a href="{{url('/admin/users/view/'.$user->id)}}" class="btn btn-sm btn-warning">View</a>
										<a href="{{url('/admin/users/edit/'.$user->id)}}" class="btn btn-sm btn-info">Edit</a>
										
										<a href="javascript:void(0)" class="btn btn-sm bg-olive delete" data-id="{{$user->id}}" data="{{$user->fname.' '.$user->lname}}">Delete</a>
										
										{{Form::button('<i class="fa fa-text-width"></i>',['class'=>'btn btn-sm btn-primary notes_btn','data'=>$user->id,'data-target'=>'#customer_notes','title'=>'Notes'])}}
										
										{{Form::button('<i class="fa fa-calendar"></i>',['class'=>'btn btn-sm btn-danger events_btn','data'=>$user->id,'data-target'=>'#customer_events','title'=>'Events'])}}	

										<a href="{{url('/loginbyemail/'.$user->email)}}"><button type="submit" class="btn btn-primary btn-sm">Login</button></a>
										
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
				<div class="pull-left">  {{ $users->links() }} </div>
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
				data:{'table':'users','related_tables':{'name':'user_address','field_name':'user_id'},'id':id},
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

@include('partials.customers.customer_notes')
@include('partials.customers.customer_event')
  
@endsection		  