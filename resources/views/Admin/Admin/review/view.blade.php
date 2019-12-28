@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Testimonial Detail</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/testimonial/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
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
						<div class="col-lg-2 col-xs-4"><b>Name</b></div>
						<div class="col-lg-10 col-xs-8">
							{!! $testimonial[0]['name'] !!}
						</div> 
						<div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Designation/Company</b></div>
						<div class="col-lg-10 col-xs-8">
							{!! $testimonial[0]['designation_company'] !!}
						</div> 
						<div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Content</b></div>
						<div class="col-lg-10 col-xs-8">
							{!! $testimonial[0]['content'] !!}
						</div> 
						<div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Status</b></div>
						<div class="col-lg-10 col-xs-8">
							@if($testimonial[0]['status']==0)
								<span class="badge">Deactive</span>
							@else
								<span class="badge">Active</span>
							@endif
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Created</b></div>
						<div class="col-lg-10 col-xs-8">{{$testimonial[0]['created_at']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Modified</b></div>
						<div class="col-lg-10 col-xs-8">{{$testimonial[0]['updated_at']}}</div> <div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>	  
@endsection		  