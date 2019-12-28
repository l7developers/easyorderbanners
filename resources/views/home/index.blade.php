@extends('layouts.app')
@section('meta')
<meta name="description" content="Easy Order Banners, a leading vinyl & mesh design and printing firm, provides quality custom banners & signs to a variety of industries. Order online today.">
@endsection

@section('content')
<link href="{{ asset('public/css/front/rollover.css') }}" rel="stylesheet">
<div class="banner">
	<div class="container">
		<div id="myCarousel" class=" roe carousel slide" data-ride="carousel">
			@if(count($sliders) > 1)
			<ol class="carousel-indicators">
			@php 
				($k = 0) 
			@endphp
			@foreach($sliders as $slider)
				  <li data-target="#myCarousel" data-slide-to="{{$k}}" class="{{($k == 0)?'active':''}}"></li>
				@php 
					($k++) 
				@endphp
			@endforeach
			</ol>
			@endif
			@php 
				($k = 0)
			@endphp
			<div class="carousel-inner">
			@foreach($sliders as $slider)
				 <div class="item {{($k == 0)?'active':''}}">
					@if($slider->content_direction == 1)
						<div class="col-xs-7 col-sm-7">
							<div class="banner_cap">
							{!! $slider->content !!}
							</div>
						</div>
						<div class="col-xs-5 col-sm-5 text-right"> <img src="{{URL::to('/public/uploads/slider/'.$slider->image)}}" alt="" /> </div>
					@else
						<div class="col-xs-5 col-sm-5 text-right"> <img src="{{URL::to('/public/uploads/slider/'.$slider->image)}}" alt="" /> </div>
						<div class="col-xs-7 col-sm-7">
							<div class="banner_cap">
							{!! $slider->content !!}
							</div>
						</div>
					@endif
				</div>
				@php 
					($k++) 
				@endphp
			@endforeach
			</div>
			@if(count($sliders) > 1)
			<!-- Left and right controls -->
			<a class="left carousel-control" href="#myCarousel" data-slide="prev">
			  <span class="glyphicon glyphicon-chevron-left"></span>
			  <span class="sr-only">Previous</span>
			</a>
			<a class="right carousel-control" href="#myCarousel" data-slide="next">
			  <span class="glyphicon glyphicon-chevron-right"></span>
			  <span class="sr-only">Next</span>
			</a>
			@endif
		</div>
	</div>
</div>
<section class="ordering_online">
	<div class="container">
		<div class="row">
			<div class="col-sm-8">
				<h5>{{$home_array['topblue']->title}}</h5>
				<p>{!! $home_array['topblue']->description !!}</p>
			</div>

			<div class="col-sm-4">
				<div class="contact_info">
					<a href="tel:18009209527"><i class="fa fa-phone"></i></a>
					<a href="javascript:$zopim.livechat.window.show()">    <i class="fa fa-comments"></i></a>
					<a href="{{url('contactus')}}">  <i class="fa fa-envelope"></i></a>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="custom_banner hidden-xs">
	<div class="container">
		<div class="row">
			<h1>{{$home_array['images']->title}}</h1>
			<div class="col-sm-12 ">
			@php
				$images = unserialize($home_array['images']->info);
			@endphp
				<div class="row">
					<div class="col-sm-12">
						<div class="homepagebanner">
							<ul>
								<li>
									<div class="content">
										<a href="#"> 
											<div class="content-overlay"></div>
											<img class="content-image" src="{{URL::to('public/uploads/home/images/'.$images[1]['image'])}}" alt="" />
										</a>
										<div class="content-details fadeIn-top">
											<div class="heading">{{$images[1]['rollover_text']}}</div>
											<a class="rollover_btn" href="{{(!empty($images[1]['rollover_button_link']))?$images[1]['rollover_button_link']:'javascript:void(0)'}}">{{$images[1]['rollover_button_text']}}</a>
										</div>
									</div>
								</li>
								<li>
									<div class="content">
										<a href="#">
											<div class="content-overlay"></div>
											<img class="content-image" src="{{URL::to('public/uploads/home/images/'.$images[2]['image'])}}" alt="" />
										</a>
										<div class="content-details fadeIn-top">
											<div class="heading">{{$images[2]['rollover_text']}}</div>
											<a class="rollover_btn" href="{{(!empty($images[2]['rollover_button_link']))?$images[2]['rollover_button_link']:'javascript:void(0)'}}">{{$images[2]['rollover_button_text']}}</a>
										</div>
									</div>
								</li>
								<li>
									<div>
										<div class="content fl_l imgboxseven">
											<a href="#">
												<div class="content-overlay"></div>
												<img class="content-image" src="{{URL::to('public/uploads/home/images/'.$images[3]['image'])}}" alt="" />
											</a>
												<div class="content-details fadeIn-top">
													<div class="heading">{{$images[3]['rollover_text']}}</div>
													<a class="rollover_btn" href="{{(!empty($images[3]['rollover_button_link']))?$images[3]['rollover_button_link']:'javascript:void(0)'}}">{{$images[3]['rollover_button_text']}}</a>
												</div>
											
										</div>
										<div class="content fl_l imgboxe">
											<a href="#"> 
												<div class="content-overlay"></div>
												<img class="content-image" src="{{URL::to('public/uploads/home/images/'.$images[4]['image'])}}" alt="" />
											</a>
											<div class="content-details fadeIn-top">
												<div class="heading">{{$images[4]['rollover_text']}}</div>
												<a class="rollover_btn" href="{{(!empty($images[4]['rollover_button_link']))?$images[4]['rollover_button_link']:'javascript:void(0)'}}">{{$images[4]['rollover_button_text']}}</a>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
									<div>
										<div class="content fl_l imgboxn">
											<a href="#"> 
												<div class="content-overlay"></div>
												<img class="content-image" src="{{URL::to('public/uploads/home/images/'.$images[6]['image'])}}" alt="" />
											</a>
											<div class="content-details fadeIn-top">
												<div class="heading">{{$images[6]['rollover_text']}}</div>
												<a class="rollover_btn" href="{{(!empty($images[6]['rollover_button_link']))?$images[6]['rollover_button_link']:'javascript:void(0)'}}">{{$images[6]['rollover_button_text']}}</a>
											</div>
										</div>
										<div class="content fl_l imgboxth">
											<a href="#"> 
												<div class="content-overlay"></div>
												<img class="content-image" src="{{URL::to('public/uploads/home/images/'.$images[7]['image'])}}" alt="" />
											</a>
											<div class="content-details fadeIn-top">
												<div class="heading">{{$images[7]['rollover_text']}}</div>
												<a class="rollover_btn" href="{{(!empty($images[7]['rollover_button_link']))?$images[7]['rollover_button_link']:'javascript:void(0)'}}">{{$images[7]['rollover_button_text']}}</a>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
								</li>
								<li>
									<div class="content">
										<a href="#"> 
											<div class="content-overlay"></div>
											<img class="content-image" src="{{URL::to('public/uploads/home/images/'.$images[5]['image'])}}" alt="" />
										</a>
										<div class="content-details fadeIn-top">
											<div class="heading">{{$images[5]['rollover_text']}}</div>
											<a class="rollover_btn" href="{{(!empty($images[5]['rollover_button_link']))?$images[5]['rollover_button_link']:'javascript:void(0)'}}">{{$images[5]['rollover_button_text']}}</a>
										</div>
									</div>
									<div class="viewall">
										{!! $home_array['images']->description !!}
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--<section class="custom_banner">
	<div class="container">
		<div class="row">
			<h3>{{$pages[0]['title']}}</h3>
			{!! $pages[0]['body'] !!}
		</div>
	</div>
</section>-->

<section class="ordering_made hidden-xs">
	<div class="container">
		<ul>
			<li>
				<div class="ordering_title">
					Ordering <br>
					<span>made</span> <strong>Easy!</strong>
				</div>
			</li>
			<li>
				<div class="blue">
					<div class="boxinner">
						<h3>CHOOSE YOUR PRODUCTS</h3>
						<img src="public/img/front/shop.png" alt="" />
						<p>Select from any of our hundreds of banners, flags, table covers and more...</p>
					</div>
				</div>
			</li>
			<li>
				<div class="oreng">
					<div class="boxinner">
						<h3>PAY ONLINE</h3>
						<img src="public/img/front/upload.png" alt="" />
						<p>Select from any of our hundreds of banners, flags, table covers and more...</p>
					</div>
				</div>
			</li>
			<li>
				<div class="green">
					<div class="boxinner">
						<h3>UPLOAD YOUR FILES</h3>
						<img src="public/img/front/pay.png" alt="" />
						<p>Select from any of our hundreds of banners, flags, table covers and more...</p>
					</div>
				</div>
			</li>
		</ul>
	</div>
	<div class="clearfix"></div>
</section>

<section class="welcome_banner" role="WelcomeBanner" style="background-image: url(public/img/front/welcome_banner.jpg);">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 welcome_left">
				<div class="welcome_title">{{$pages[1]['title']}}</div>
				{!! $pages[1]['body'] !!}
				<div class="client_review_block">
					<div class="owl-carousel client_review">
						@foreach($testimonials as $testimonial)
							<div class="item">
								<div class="clt_review">
									<em>{!! $testimonial->content !!}</em>
								</div>
								<div class="clearfix"></div>
								<div class="clt_review_name">
									{{$testimonial->name}}
									<small>{{$testimonial->designation_company}}</small>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 welcome_left">
				<div class="welcome_subtitle">{{$pages[2]['title']}}</div>
				{!! $pages[2]['body'] !!}
			</div>
		</div>
	</div>
</section>

<section class="canopy_tents_block" role="CanopyTents">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 canopy_left">
				<div class="canopy_feather_slider_block">
					<div class="owl-carousel canopy_feather">
						@foreach($home_array['carousel1']->images as $image)
							<div class="item">
								<a href="javascript:void(0)"><img src="{{URL::to('public/uploads/home/carousel1/'.$image->name)}}" alt="{{$home_array['carousel1']->title}}" title="{{$image->title}}"></a>
							</div>
						@endforeach
					</div>
				</div>
			</div>
			<div class="col-xs-12  col-sm-12 col-md-6 col-lg-6 canopy_right">
				<div class="welcome_title">{{$home_array['carousel1']->title}}</div>
				{!! $home_array['carousel1']->description !!}
				
				<div class="clearfix"></div>
				<div class="accessories_block">
					<div class="accessories_img">
						<img src="{{URL::to('public/uploads/home/carousel1/'.$home_array['sub_carousel1']->img)}}" alt="accessories">
					</div>
					<div class="accessories_text">
						<div class="accessories_title">
							{{$home_array['sub_carousel1']->title}} 
							<small>{!! $home_array['sub_carousel1']->description !!}</small>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="banner_table_throws_block" role="WelcomeBanner" style="background-image: url(public/img/front/welcome_banner.jpg);">
	<div class="container">
		<div class="banner_table_title"><span>{!! $home_array['carousel2']->title !!}</span><small>{!! $home_array['carousel2']->description !!}</small></div>
		<div class="row">
		<div class="col-sm-12">
			<div class="owl-carousel custom_banner_products">
			@foreach($home_array['carousel2']->product_detail as $product)			
				<div class="banner_table_throws_sec item">
					<a href="{{url($product->slug)}}" class="banner_table_throws_img">
						@if(@getimagesize(url('public/uploads/product/'.$product->cat_image)))
							<img src="{{URL::to('public/uploads/product/'.$product->cat_image)}}" alt="{{$product->cat_image_title}}">
						@else
							<img src="{{URL::to('/public/img/front/img.jpg')}}" alt="" style="width:385px;height:200px;"/>
						@endif
					</a>
					<div class="banner_table_throws_title">{{$product->name}} <span>${{$product->price}} {{($product->show_width_height==1)?'/sq.ft.':''}}</span></div>
					<div class="text-center"><a href="{{url($product->slug)}}" class="order_btn">Get Started</a></div>
				</div>
			@endforeach
			</div>
		</div>
		</div>
	</div>
</section>

<section class="canopy_tents_block" role="CanopyTents">
	<div class="container">
		<div class="row">
			<div class="col-xs-12  col-sm-12 col-md-6 col-lg-6 canopy_left">
				<div class="canopy_feather_slider_block">
					<div class="owl-carousel canopy_feather">
						@foreach($home_array['carousel3']->images as $image)
							<div class="item">
								<a href="javascript:void(0)"><img src="{{URL::to('public/uploads/home/carousel3/'.$image->name)}}" alt="{{$home_array['carousel3']->title}}" title="{{$image->title}}"></a>
							</div>
						@endforeach
					</div>
				</div>
			</div>
			<div class="col-xs-12  col-sm-12 col-md-6 col-lg-6 canopy_right">
				<div class="welcome_title">{{$home_array['carousel3']->title}}</div>
				{!! $home_array['carousel3']->description !!}
				<div class="clearfix"></div>
				<div class="accessories_block">
					<div class="accessories_img">
						<img src="{{URL::to('public/uploads/home/carousel3/'.$home_array['sub_carousel3']->img)}}" alt="accessories">
					</div>
					<div class="accessories_text">
						<div class="accessories_title">
							{{$home_array['sub_carousel3']->title}} 
							<small>{!! $home_array['sub_carousel3']->description !!}</small>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="banner_table_throws_block" role="WelcomeBanner" style="background-image: url(public/img/front/welcome_banner.jpg);">
	<div class="container">
		<div class="banner_table_title"><span>{!! $home_array['carousel4']->title !!}</span><small>{!! $home_array['carousel4']->description !!}</small></div>
		<div class="row">
		<div class="col-sm-12">
			<div class="owl-carousel custom_banner_products">
			@foreach($home_array['carousel4']->product_detail as $product)
				<div class="banner_table_throws_sec item">
					<a href="{{url($product->slug)}}" class="banner_table_throws_img">
						@if(@getimagesize(url('public/uploads/product/'.$product->cat_image)))
							<img src="{{URL::to('public/uploads/product/'.$product->cat_image)}}" alt="{{$product->cat_image_title}}">
						@else
							<img src="{{URL::to('/public/img/front/img.jpg')}}" alt="" style="width:385px;height:200px;"/>
						@endif
					</a>
					<div class="banner_table_throws_title">{{$product->name}} <span>${{$product->price}}{{($product->show_width_height==1)?'/sq.ft.':''}}</span></div>
					<div class="text-center"><a href="{{url($product->slug)}}" class="order_btn">Get Started</a></div>
				</div>
			@endforeach
			</div>
		</div>
		</div>
	</div>
</section>
<section class="client_partner_block" role="ClientPartner">
	<div class="container">
		<div class="clt_partner_title"><span>Easy Order Banners is Proud to have Worked with the Following Loyal Customers</span></div>
		<div class="clearfix"></div>
		<div class="client_partner_sec_block">
			<div class="owl-carousel client_partners">
			@foreach($customer_logos as $logo)
				<div class="item"><a href="{{($logo->link != null)?$logo->link:'javascript:void(0)'}}"><img src="{{URL::to('public/uploads/home/customers/'.$logo->image)}}" title="{{$logo->title}}"></a></div>
			@endforeach
			</div>
		</div>
	</div>
</section>
@endsection