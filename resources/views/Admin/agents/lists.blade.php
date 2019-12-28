@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Agents List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/agents/add')}}" class="btn btn-primary btn-md">Add Agent</a>
				</div>
				<div class="panel-body">
					<form role="form" method="POST">
					{{ csrf_field() }}
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">First Name</label>
							<input id="fname" type="text" class="form-control" name="fname" value="{{ \session()->get('agnets.fname')}}" placeholder="Search by First Name">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">Last Name</label>
							<input id="lname" type="text" class="form-control" name="lname" value="{{ \session()->get('agnets.lname')}}" placeholder="Search by Last Name">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="form-control-label">Email</label>
							<input id="email" type="text" class="form-control" name="email" value="{{ \session()->get('agnets.email')}}" placeholder="Search by email">
						</div>
					</div>
					@php
						$status = '2';
						if(\session()->has('agnets.status')){
							$status = \session()->get('agnets.status');
						}
						//echo $status;
					@endphp
					<div class="col-sm-2">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">Status</label>	{{Form::select('status',[''=>'--All--','1'=>'Active','0'=>'Deactive'],$status,['class'=>'form-control','id'=>'status'])}}
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
								<a href="{{url('/admin/agents/lists?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
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
								<th>Role</th>
								<th>
									<a href="{{url('admin/agents/lists/fname/'.$sort)}}" class="sort_link">First Name</a>
									@if($field == 'fname')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/agents/lists/lname/'.$sort)}}" class="sort_link">Last Name</a>
									@if($field == 'lname')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th><a href="{{url('admin/agents/lists/extension/'.$sort)}}" class="sort_link">Extension</a>
									@if($field == 'extension')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th><a href="{{url('admin/agents/lists/direct/'.$sort)}}" class="sort_link">Direct</a>
									@if($field == 'direct')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th><a href="{{url('admin/agents/lists/mobile/'.$sort)}}" class="sort_link">Mobile</a>
									@if($field == 'mobile')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/agents/lists/email/'.$sort)}}" class="sort_link">Email</a>
									@if($field == 'email')
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
						@if(count($agents)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($agents as $agent)
								<tr>
									<td>{{ ++$i }}</td>
									<th scope="row">{{$agent->id}}</th>
									<td>{{$roles[$agent->role_id]}}</td>
									<td>{{$agent->fname}}</td>
									<td>{{$agent->lname}}</td>
									<td>{{$agent->extension}}</td>
									<td>{{$agent->direct}}</td>
									<td>{{$agent->mobile}}</td>
									<td>{{$agent->email}}</td>
									<td>
										@if($agent->status==1)
											<div class="badge badge-primary">Activate</div>
										@else
											<div class="badge badge-danger">Deactivate</div>
										@endif
									</td>
									<td>{{$agent->created_at}}</td>
									<td>								
										@if($agent->status==1)
											<a href="{{url('/admin/agents/action/'.$agent->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
										@else
											<a href="{{url('/admin/agents/action/'.$agent->id.'/1')}}" class="btn btn-primary btn-sm">Activate</a>
										@endif									
											<a href="{{url('/admin/agents/view/'.$agent->id)}}" class="btn btn-sm btn-warning">View</a>
											<a href="{{url('/admin/agents/edit/'.$agent->id)}}" class="btn btn-sm btn-info">Edit</a>
											<button data-value="{{$agent->fname.' '.$agent->lname}}" data-url="{{url('/admin/agents/delete/'.$agent->id)}}" class="btn bg-olive btn-sm delete">Delete</button>
											
											{{Form::button('<i class="fa fa-comments-o"></i>',['class'=>'btn btn-sm btn-success messages_btn','data'=>$agent->id,'data-target'=>'#agent_messages','title'=>'Messages'])}}	
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
				<div class="pull-left">  {{ $agents->links() }} </div>
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
@include('partials.admin.agent_messages')
@endsection		  