@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Product Detail</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/products/lists')}}" class="btn btn-warning btn-sm" style="float: right;">Back to list</a>
			</div>
			<div class="top_btns">
				<a href="{{url($products->slug)}}" target="_blank" class="btn btn-success btn-sm" style="float: right;">View On Site</a>
			</div>
			<div class="top_btns">
				<a href="{{url('admin/products/edit/'.$products->id)}}" class="btn btn-info btn-sm" style="float: right;">Edit</a>
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
						<div class="col-lg-3 col-xs-4"><b>Name</b></div>
						<div class="col-lg-9 col-xs-8">{{$products->name}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Slug</b></div>
						<div class="col-lg-9 col-xs-8">{{$products->slug}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Category Name</b></div>
						<div class="col-lg-9 col-xs-8">{{$products->Catgory->name}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Category Page Image</b></div>
						<div class="col-lg-9 col-xs-8">
							<div class="row margin-bottom">
								<div class="col-sm-12">
									<img class="img-responsive image_list" src="{{URL::to('/public/uploads/product/'.$products->cat_image)}}" />
								</div>
							</div>
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Category Page Image Title/Alt</b></div>
						<div class="col-lg-9 col-xs-8">{{$products->cat_image_title}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Product Image</b></div>
						<div class="col-lg-9 col-xs-8">
							<div class="row margin-bottom">
								<div class="col-sm-12">
									<img class="img-responsive image_list" src="{{URL::to('/public/uploads/product/'.$products->image)}}" />
								</div>
							</div>
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Product Image Title/Alt</b></div>
						<div class="col-lg-9 col-xs-8">{{$products->image_title}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Other Images</b></div>
						<div class="col-lg-9 col-xs-8">
							<div class="row margin-bottom">
								<div class="col-sm-12">
								@php
								foreach($products->Images as $image){
									if($image->type == 2){
								@endphp
									<img class="img-responsive image_list" src="{{URL::to('/public/uploads/product/'.$image->name)}}">
								@php
									}
								}
								@endphp
								
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Price</b></div>
						<div class="col-lg-9 col-xs-8"><b>$</b>{{priceFormat($products->price)}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Show Width Height</b></div>
						<div class="col-lg-9 col-xs-8">
							@if($products->show_width_height == 0)
								<span class="badge">No</span>
							@else
								<span class="badge">Yes</span>
							@endif
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Excerpt</b></div>
						<div class="col-lg-9 col-xs-8">{!! $products->excerpt !!} </div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Short Description</b></div>
						<div class="col-lg-9 col-xs-8">{!! $products->short_description; !!} </div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Product Detail</b></div>
						<div class="col-lg-9 col-xs-8">{!! $products->description; !!} </div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Art Files Preparations</b></div>
						<div class="col-lg-9 col-xs-8">
							@if(!empty($products->art_file_preparations))
								{!! $products->art_file_preparations !!} 
							@else
								Empty
							@endif
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Design Template</b></div>
						<div class="col-lg-9 col-xs-8">
							@if(!empty($products->design_templates))
								{!! $products->design_templates !!} 
							@else
								Empty
							@endif
						</div> <div class="clearfix"></div>
					</div>
					@if(count($products->custom) > 0)
						@foreach($products->custom as $val)
							<div class="col-lg-12">    
								<div class="col-lg-3 col-xs-4"><b>{{$val->title}}</b></div>
								<div class="col-lg-9 col-xs-8">{!! $val->body !!} </div> <div class="clearfix"></div>
							</div>
						@endforeach
					@endif
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Meta Title</b></div>
						<div class="col-lg-9 col-xs-8">{{$products->meta_title}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Meta Keywords</b></div>
						<div class="col-lg-9 col-xs-8">
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>Sr.No</th>
											<th>Name</th>
										</tr>
									</thead>
									<tbody>
									@php
										$i = 1;
										$data = explode(',',$products->meta_tag);
										foreach($data as $val){
									@endphp
										<tr>
											<td>{{$i}}</td>
											<td>{{$val}}</td>
										</tr>
									@php $i++; } @endphp
									</tbody>
								</table>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Meta Description</b></div>
						<div class="col-lg-9 col-xs-8">{{$products->meta_description}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Custom Option</b></div>
						<div class="col-lg-9 col-xs-8">
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>Sr.No</th>
											<th>Option Name</th>
										</tr>
									</thead>
									<tbody>
									@php 
									$i = 1;
									foreach($products->Options as $option){
									@endphp
										<tr>
											<td>{{$i}}</td>
											<td>{{$option->CustomOption->name}}</td>
										</tr>
									@php
										$i++;
										}
									@endphp
									</tbody>
								</table>
							</div>
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Status</b></div>
						<div class="col-lg-9 col-xs-8">
							@if($products->status == 0)
								<span class="badge">Deactive</span>
							@else
								<span class="badge">Active</span>
							@endif
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Created</b></div>
						<div class="col-lg-9 col-xs-8">{{$products->created_at}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Modified</b></div>
						<div class="col-lg-9 col-xs-8">{{$products->updated_at}}</div> <div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>	  
<style>

</style>
@endsection		  