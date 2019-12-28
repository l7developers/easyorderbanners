@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Category Detail</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/category/lists')}}" class="btn btn-warning btn-sm" style="float: right;">Back to list</a>
			</div>
			<div class="top_btns">
				<a href="{{url($category[0]['slug'])}}" target="_blank" class="btn btn-success btn-sm" style="float: right;">View On Site</a>
			</div>
			<div class="top_btns">
				<a href="{{url('admin/category/edit/'.$category[0]['id'])}}" class="btn btn-info btn-sm" style="float: right;">Edit</a>
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
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Name</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">{{$category[0]['name']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Slug</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">{{$category[0]['slug']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Category Image</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">
							<img class="img-responsive image_list" src="{{URL::to('/public/uploads/category/'.$category[0]['image'])}}" alt="Photo">
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Meta Title</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">{{$category[0]['meta_title']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Meta Description</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">{{$category[0]['meta_description']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Meta Keywords</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">	
						@php
							$keywords = explode(',',$category[0]['meta_tag']);
							foreach($keywords as $key){
								echo "<li>$key</li>";
							}
						@endphp
						</div> 
						<div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Status</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">
							@if($category[0]['status']==0)
								<span class="badge">Deactive</span>
							@else
								<span class="badge">Active</span>
							@endif
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Created</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">{{$category[0]['created_at']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Modified</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">{{$category[0]['updated_at']}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Excerpt</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">{!! html_entity_decode($category[0]['excerpt']); !!}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-md-4 col-xs-6"><b>Description</b></div>
						<div class="col-lg-6 col-md-8 col-xs-6">{!! html_entity_decode($category[0]['description']); !!}</div> <div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>	  
@endsection		  