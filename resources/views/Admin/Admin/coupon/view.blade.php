@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>
	Vendor Detail
  </h1>
  <ol class="breadcrumb">
	<button onclick="window.location.href='{{url('admin/vendors/lists')}}'" style="float: right;" class="btn btn-success btn-sm">Back to list</button>
  </ol>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body admin-view">	
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Email</b></div>
						<div class="col-lg-6">{{$vendor[0]['email']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>First Name</b></div>
						<div class="col-lg-6">{{$vendor[0]['fname']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Last Name</b></div>
						<div class="col-lg-6">{{$vendor[0]['lname']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Phone Number</b></div>
						<div class="col-lg-6">{{$vendor[0]['phone_number']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Status</b></div>
						<div class="col-lg-6">
							@if($vendor[0]['status']==0)
								<span class="badge">Deactive</span>
							@else
								<span class="badge">Active</span>
							@endif
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Created</b></div>
						<div class="col-lg-6">{{$vendor[0]['created_at']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Modified</b></div>
						<div class="col-lg-6">{{$vendor[0]['updated_at']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Dropbox Details</b></div>
						<div class="col-lg-6">{!! $vendor[0]['dropbox']; !!}</div> <div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>	  
@endsection		  