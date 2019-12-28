@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>Emails List</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body table-responsive">			
					<table class="table table-bordered table-hover table-striped addedfeature table-striped" border="1" style="width:100%;border-collapse:collapse;">
						<thead>
							<tr>
								<th>S.No.</th>
								<th>#id</th>
								<th>Slug</th>
								<th>Subject</th>
								<!--<th>Status</th>-->
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($emails)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($emails as $email) 
							  <tr>
								<th>{{++$i}}</th>
								<th scope="row">{{$email->id}}</th>
								<td>{{$email['slug']}}</td>
								<td>{{$email->subject}}</td>
								<!--<td>
									@if($email->status==1)
										<div class="badge badge-primary">Activate</div>
									@else
										<div class="badge badge-danger">Deactivate</div>
									@endif
								</td>-->
								<td>								
									<!--@if($email->status==1)
										<a href="{{url('/admin/emails/action/'.$email->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
									@else
										<a href="{{url('/admin/emails/action/'.$email->id.'/1')}}" class="btn btn-sm btn-primary">Activate</a>
									@endif -->
									<a href="{{url('/admin/emails/view/'.$email->id)}}" class="btn btn-sm btn-warning">View</a>
									<a href="{{url('/admin/emails/edit/'.$email->id)}}" class="btn btn-sm btn-info">Edit</a>
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
					<div class="pull-left">  {{ $emails->links() }} </div>
				</div>			
			</div>			
		</div>
	</div>
</section>		  
@endsection		  