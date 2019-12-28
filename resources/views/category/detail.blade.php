@extends('layouts.app')
@section('meta')
<meta name="description" content="{{$category->meta_description}}">
<meta name="keywords" content="{{$category->meta_tag}}">
@endsection
@section('content')

<style type="text/css">
a.cate_bax img
{
	width: 385px;
	height: 200px;
}	
</style>

<section class="pagestitles">
	<div class="container">
		<h2>{{$category->name}}</h2>
	</div>
</section>
<section class="innerpages">
	<div class="container">
		<div class="row">
		@if(count($category->products) > 0 || count($category->child) > 0)
			<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 detail detail_page">
				{!! htmlspecialchars_decode($category->description); !!}
			</div>
			
			<div class="col-xs-12 col-sm-6 col-md-8 col-lg-8">
				<div class="row"> 					
				@if(count($category->products) > 0)					
					@foreach($category->products as $key=>$val)
					
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<div class="standardvinyl">		@if(@getimagesize(url('public/uploads/product/'.$val->cat_image)))
								<a class="cate_bax" href="{{url($val->slug)}}"><img src="{{URL::to('/public/uploads/product/'.$val->cat_image)}}" alt="{{$val->cat_image_title}}" title="{{$val->cat_image_title}}" /></a>
							@else
								<a class="cate_bax" href="{{url($val->slug)}}"><img src="{{URL::to('/public/img/front/img.jpg')}}" alt="" style="max-height: 260px"/></a>
							@endif
								<a class="cate_bax" href="{{url($val->slug)}}" style="height: 33px;overflow: hidden;"><span>{{$val->name}}</span></a>
								<p>	{{substr(strip_tags(html_entity_decode($val->excerpt)),0,200) }}
								</p>
								<div class="text-right">
									<a href="{{url($val->slug)}}">Get Started</a>
								</div>
							</div>
						</div>
					@endforeach
					
				@endif
				@if(count($category->child) > 0)
					
					@foreach($category->child as $key=>$val)
					
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="height: 490px">
							<div class="standardvinyl">	@if(@getimagesize(url('public/uploads/category/'.$val->image)))
								<a class="cate_bax" href="{{url($val->slug)}}"><img src="{{URL::to('/public/uploads/category/'.$val->image)}}" alt="{{$val->image_title}}" title="{{$val->image_title}}" /></a>
							@else
								<a class="cate_bax" href="{{url($val->slug)}}"><img src="{{URL::to('/public/img/front/img.jpg')}}" alt="" style="max-height: 260px"/></a>
							@endif
								<a class="cate_bax" href="{{url($val->slug)}}" style="height: 33px;overflow: hidden;"><span>{{$val->name}}</span></a>
								<p>{{substr(strip_tags(html_entity_decode($val->excerpt)),0,200) }}
								</p>
								<div class="text-right">
									<a href="{{url($val->slug)}}">Get Started</a>
								</div>
							</div>
						</div>
					@endforeach
					
				@endif
				</div>
			</div>
		@else
			<div class="col-xs-12 detail detail_page">
				{!! htmlspecialchars_decode($category->description); !!}
			</div>
		@endif
		</div>
	</div>
</section>
@endsection