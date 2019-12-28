@extends('layouts.app')
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
		<h2>Shop</h2>
	</div>
</section>
<section class="innerpages">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="row"> 
					{{Form::model('searchForm',['role'=>'form','method'=>'get','class'=>''])}}
					<div class="form-group">
						<div class="col-xs-6 no-padding pull-rigth searchBar">
							<div class="row no-gutter">
								<div class="col-sm-6">
									{{Form::text('search','',['class'=> 'form-control','id'=>'search','placeholder'=>'Enter Product Name'])}}
								</div>
								<div class="col-sm-5">
									{{Form::select('category',[0 => 'All Categories']+$categories,'',['class'=>'form-control'])}}
								</div>
								<div class="col-sm-1">
									<button class="btn btn-success" id="" type="submit"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
					{{Form::close()}}
					
					@if(count($categoryList) > 0)
						@foreach($categoryList as $cat)
							<!-- Below Code for main category products show-->
							<?php /* @if(count($cat->products) > 0) */ ?>

								@php
									if($cat->id == config('constants.CUSTOM_CATEGORY_ID'))
										continue;
								@endphp	

								<div class="col-xs-12">
									<h4 class="customFont" style="border-bottom:1px solid ">{{$cat->name}}</h4>
									@foreach($cat->products as $key=>$val)
										<div class="col-xs-12 col-sm-6 col-md-4">
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
								</div>
								
								<!-- Below Code for main category child products show-->
								@if(count($cat->childProducts) > 0)
									@foreach($cat->childProducts as $child1)
									@if(count($child1->products) > 0)
									<div class="col-xs-11 col-xs-offset-1">
										<h4 class="customFont">{{$child1->name}}</h4>
										@foreach($child1->products as $key1=>$val1)
											<div class="col-xs-12 col-sm-6 col-md-4">
												<div class="standardvinyl">		@if(@getimagesize(url('public/uploads/product/'.$val1->cat_image)))
													<a class="cate_bax" href="{{url($val1->slug)}}"><img src="{{URL::to('/public/uploads/product/'.$val1->cat_image)}}" alt="{{$val1->cat_image_title}}" title="{{$val1->cat_image_title}}" /></a>
												@else
													<a class="cate_bax" href="{{url($val1->slug)}}"><img src="{{URL::to('/public/img/front/img.jpg')}}" alt="" style="max-height: 260px"/></a>
												@endif
													<a class="cate_bax" href="{{url($val1->slug)}}" style="height: 33px;overflow: hidden;"><span>{{$val1->name}}</span></a>
													<p>	{{substr(strip_tags(html_entity_decode($val1->excerpt)),0,200) }}
													</p>
													<div class="text-right">
														<a href="{{url($val1->slug)}}">Get Started</a>
													</div>
												</div>
											</div>
										@endforeach
									</div>
									@endif
										
										<!-- Below Code for main category child of child products show-->
										@if(count($child1->childProducts) > 0)
										@foreach($child1->childProducts as $child2)
											@if(count($child2->products) > 0)
											<div class="col-xs-11 col-xs-offset-1">
												<h4 class="customFont">{{$child2->name}}</h4>
												@foreach($child2->products as $key2=>$val2)
													<div class="col-xs-12 col-sm-6 col-md-4">
														<div class="standardvinyl">		@if(@getimagesize(url('public/uploads/product/'.$val2->cat_image)))
															<a class="cate_bax" href="{{url($val2->slug)}}"><img src="{{URL::to('/public/uploads/product/'.$val2->cat_image)}}" alt="{{$val2->cat_image_title}}" title="{{$val2->cat_image_title}}" /></a>
														@else
															<a class="cate_bax" href="{{url($val2->slug)}}"><img src="{{URL::to('/public/img/front/img.jpg')}}" alt="" style="max-height: 260px"/></a>
														@endif
															<a class="cate_bax" href="{{url($val2->slug)}}" style="height: 33px;overflow: hidden;"><span>{{$val2->name}}</span></a>
															<p>	{{substr(strip_tags(html_entity_decode($val2->excerpt)),0,200) }}
															</p>
															<div class="text-right">
																<a href="{{url($val2->slug)}}">Get Started</a>
															</div>
														</div>
													</div>
												@endforeach
											</div>
											@endif
										@endforeach
										@endif
										
										<!-- End Code for main category child of child products show-->
										
									@endforeach
								
								<?php /* @endif */ ?>
								<!-- End Code for main category child products show-->
							@endif
							<!-- End Code for main category products show-->
						@endforeach
						<div class="col-xs-12">
							<div class="pull-rigth">{!! $categoryList->appends(\Request::except('page'))->render() !!}</div>
						</div>
					@else
						<br/><h4 style="clear: both;"><center>No Product Found...</center></h4>
					@endif				
				</div>
			</div>
		</div>
	</div>
</section>

<script>
$(function () {
    'use strict';
	
	var products = <?php echo json_encode($productList,JSON_PRETTY_PRINT);?>;
	var countriesArray = $.map(products, function (value,key) { 
								return { 
										data : value.id, 
										value : value.name,
										image :value.image,
										image_title :value.image_title 
										}; 
						});
	$("#search").on('keyup',function(){
		$('select[name="category"]').val('0');
	});
	
	$("#search").autocomplete({
		source: countriesArray,
		minLength: 1,
		select: function(event, ui) {
			var url = ui.item.id;
			if(url != '') {
			//location.href = '...' + url;
			}
		},
		html: true, 
		open: function(event, ui) {
			$(".ui-autocomplete").css("z-index", 1000);
		}
	}).autocomplete( "instance" )._renderItem = function( ul, item ) {
		var img = '{{url("/")}}'+'/public/img/front/img.jpg';
		if(item.image != '' && item.image != null)
			img = '{{url("/")}}'+'/public/uploads/product/'+item.image;
		var str = "<li><div><img src='"+img+"'><span style='margin-left:5px;'>"+item.value+"</span></div></li>";
		return $(str).appendTo( ul );
	};
});

</script>
@endsection