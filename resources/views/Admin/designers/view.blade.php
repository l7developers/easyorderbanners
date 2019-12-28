@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Designer Detail</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/designers/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
			</div>
		</div>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body admin-view">	
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>Email</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{{$designer[0]['email']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>First Name</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{{$designer[0]['fname']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>Last Name</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{{$designer[0]['lname']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>Intercompany Extension </b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{{$designer[0]['extension']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>Direct</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{{$designer[0]['direct']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>Mobile</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{{$designer[0]['mobile']}}</div> <div class="clearfix"></div>
					</div>
					<?php /*<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>Phone Number</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{{$designer[0]['phone_number']}}</div> <div class="clearfix"></div>
					</div> */?>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>Status</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">
							@if($designer[0]['status']==0)
								<span class="badge">Deactive</span>
							@else
								<span class="badge">Active</span>
							@endif
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>Created</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{{$designer[0]['created_at']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>Modified</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{{$designer[0]['updated_at']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-4"><b>tFlow Details</b></div>
						<div class="col-lg-6 col-md-8 col-xs-8">{!! $designer[0]['tFlow']; !!}</div> <div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>	  
@endsection		  