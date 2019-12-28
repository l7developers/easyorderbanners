@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Email View</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/emails/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
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
						<div class="col-xs-4"><b>Slug</b></div>
						<div class="col-xs-8">{{$email->slug}}</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-xs-4"><b>Subject</b></div>
						<div class="col-xs-8">{{$email->subject}}</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-xs-4"><b>Status</b></div>
						<div class="col-xs-8">
						@php
							if($email->status == 1){ echo "<span class='badge'>Active</span>";}else{ echo "<span class='badge'>Deactive</span>"; }
						@endphp
						</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-xs-4"><b>Created</b></div>
						<div class="col-xs-8">{{$email->created_at}}</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-xs-4"><b>Message</b></div>
						<div class="col-xs-8">{!! $email->message; !!}</div> <div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection		  