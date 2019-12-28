@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Content View</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/staticpages/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
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
					<div  class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Title</b></div>
						<div class="col-lg-10 col-xs-8">{{$page->title}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Slug</b></div>
						<div class="col-lg-10 col-xs-8">{{$page->slug}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Meta Title</b></div>
						<div class="col-lg-10 col-xs-8">{{$page->mete_title}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Meta Keywords</b></div>
						<div class="col-lg-10 col-xs-8">
							@php
								if(!empty($page->meta_tag)){
									echo '<ul>';
									$keywords = explode(',',$page->meta_tag);
									foreach($keywords as $val){
										echo '<li>$val</li>';
									}
									echo '</ul>';
								}
							@endphp
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Meta Description</b></div>
						<div class="col-lg-10 col-xs-8">{{$page->meta_description}}</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Status</b></div>
						<div class="col-lg-10 col-xs-8">
						@php
							if($page->status == 1){ echo "<span class='badge'>Active</span>";}else{ echo "<span class='badge'>Deactive</span>"; }
						@endphp
						</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Created</b></div>
						<div class="col-lg-10 col-xs-8">{{$page->created_at}}</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Body</b></div>
						<div class="col-lg-10 col-xs-8">{!! $page->body; !!}</div> <div class="clearfix"></div>
					</div>
				</div>
			</div>
        </div>
	</div>
</section>		  
@endsection		  