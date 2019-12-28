@extends('layouts.app')
@section('meta')
<meta name="title" content="{{(isset($pageTitle))?$pageTitle:$product->name}}">
<meta name="description" content="{{(!empty($product->meta_description))?$product->meta_description:$product->excerpt}}">
<meta name="keywords" content="{{$product->meta_tag}}">

<meta name="og:title" content="{{$product->name}}">
<meta name="og:url" content="{{url($product->slug)}}">
<meta name="og:type" content="Article">
<meta name="og:description " content="{{$product->excerpt}}">
<meta name="og:image" content="{{(@getimagesize(url('public/uploads/product/'.$product->image)))?URL::to('/public/uploads/product/'.$product->image):URL::to('/public/img/front/img.jpg')}}">
@endsection
@section('content')
@php
	$cart_product = array();
	if(!empty(\session()->get('cart')) and $cartkey != null){
		$cart_product = \session()->get('cart')[$cartkey];
		/* foreach(\session()->get('cart') as $cart){
			if($cart['product_id'] == $product->id){
				$cart_product = $cart;
			}
		} */		
	}
	//pr($product->min_sqft);die;
@endphp
<link href="{{ asset('public/css/front/lightslider.css') }}" rel="stylesheet">
<script src="{{ asset('public/js/front/lightslider.js') }}"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>

<section class="heavyduty">
	<div class="container">
		<div class="row">
			<div class="col-sm-6">
				<div class="prdocut_gallery_slider">
					<ul id="imageGallery">
					@foreach($product->images as $image)
						<li data-thumb="{{URL::to('/public/uploads/product/'.$image->name)}}" data-src="{{URL::to('/public/uploads/product/'.$image->name)}}">
							<img src="{{URL::to('/public/uploads/product/'.$image->name)}}" />
						</li>
					@endforeach
					</ul>
					<div class="leftslider_prodct custom_tab hidden-xs hidden-sm" id="accordion">
						<div class="panel">
							<div class="box-header with-border" style="margin-bottom: 10px;">
								<a data-toggle="collapse" data-parent="#accordion" href="#product_detail">
									<div class="productselect" style="padding: 5px 8px;">PRODUCT DETAIL</div>
								</a>
							</div>
							<div id="product_detail" class="panel-collapse collapse in">
								<div class="coustomeflutter">
								@php
									if(!empty($product->description)){
										echo $product->description;
									}else{
										echo '<p>The Product Detail will be comming very soon .</p>';
									}
								@endphp
								</div>
							</div>
						</div>
						@if(strip_tags($product->design_templates) !="")
						<div class="panel">
							<div class="box-header with-border" style="margin-bottom: 10px;">
								<a data-toggle="collapse" data-parent="#accordion" href="#design_templates">
									<div class="productselect" style="padding: 5px 8px;">DESIGN TEMPLATES</div>
								</a>
							</div>
							<div id="design_templates" class="panel-collapse collapse">
								<div class="coustomeflutter">
								{!! $product->design_templates!!}							
								</div>
							</div>
						</div>
						@endif

						@if(strip_tags($product->art_file_preparations) !="")
						<div class="panel">
							<div class="box-header with-border" style="margin-bottom: 10px;">
								<a data-toggle="collapse" data-parent="#accordion" href="#art_file_preperation">
									<div class="productselect" style="padding: 5px 8px;">ART FILE PREPARATION</div>
								</a>
							</div>
							<div id="art_file_preperation" class="panel-collapse collapse">
								<div class="coustomeflutter">
									{!! $product->art_file_preparations !!}													
								</div>
							</div>
						</div>
						@endif

						@if(count($product->custom) > 0)
							@foreach($product->custom as $val)
								<div class="panel">
									<div class="box-header with-border" style="margin-bottom: 10px;">
										<a data-toggle="collapse" data-parent="#accordion" href="#new_tab_{{$val->id}}">
											<div class="productselect" style="padding: 5px 8px;">{{$val->title}}</div>
										</a>
									</div>
									<div id="new_tab_{{$val->id}}" class="panel-collapse collapse">
										<div class="coustomeflutter">
											@php
												if(!empty($val->body)){
													echo $val->body;
												}else{
													echo '<p>The content will be comming very soon .';
												}
											@endphp
										</div>
									</div>
								</div>
							@endforeach
						@endif

						@if(count($reviews) > 0)
						<div class="panel">
							<div class="box-header with-border" style="margin-bottom: 10px;">
								<a data-toggle="collapse" data-parent="#accordion" href="#customer_reviews">
									<div class="productselect" style="padding: 5px 8px;">CUSTOMER REVIEWS</div>
								</a>
							</div>
							<div id="customer_reviews" class="panel-collapse collapse">
								<div class="coustomeflutter">									
									@foreach($reviews as $review)
										<div class="show_reviews">
											<span class="comment_star" style="padding:0px;" data="{{$review->rating}}"></span>
											<p>
												{!! nl2br($review->comment) !!}
												<br/><br/>Posted on : {{date('M-d',strtotime($review->created_at))}} &nbsp;&nbsp;&nbsp;
												@if(isset($review->user))
												By :  {{ucwords($review->user->fname.' '.$review->user->lname)}}
												@endif
											</p>
											<hr/>
										</div>
									@endforeach
									@if(count($reviews) > 1)
									<button type="button" class="btn btn-xs btn-success pull-right view_more">View More</button>
									@endif
								</div>
							</div>
						</div>
						@endif               
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="product_page">
					<h4>{{$product->name}}</h4>
					@php
						if(!empty($product->min_price)){
							$min_price = $product->min_price;
						}else{
							$min_price = 0;
						}
						//$min_price = productMinPrice($product);
					@endphp
					<p class="starting"> {!! display_product_price($product) !!} </p>
					<div class="rating">
						@if($count > 0)
							<div id="rateYo"></div>
							<p>{{$count}} customer reviews</p>
						@else
							<p>Not Reviewed Yet!</p>
						@endif
					</div>
					<!--<p><strong>All Our Large Outdoor Banners Are Outdoor Rated and Guaranteed to Last! Lowest Price on the Web, as Low as $1.50 psf! </strong></p>-->
                    <p>{!! $product->short_description !!}</p>
					<div class="space"></div>
					<h5>Select Your Options:</h5>
					{{Form::model('product_form',['id'=>'product_form'])}}
						{{Form::hidden('product_id',$product->id)}}
						{{Form::hidden('product_slug',$product->slug)}}
						{{Form::hidden('cartkey',$cartkey)}}
						{{Form::hidden('min_width',(!empty($product->min_width))?$product->min_width:0)}}
						{{Form::hidden('max_width',(!empty($product->max_width))?$product->max_width:0)}}
						{{Form::hidden('min_height',(!empty($product->min_height))?$product->min_height:0)}}	{{Form::hidden('max_height',(!empty($product->max_height))?$product->max_height:0)}}
						<div class="row">							
							<div class="col-xs-6 col-sm-12 col-md-12 col-lg-6 fullwid">
								<div class="finishingoptions">
									<div class="title">
										Printing Options
									</div>
									<div class="form-group main_box">{{Form::label('quantity','Quantity',['class'=>'quantity_lab'])}}
										<div class="quantity">{{Form::number('quantity',(!empty($cart_product))?$cart_product['quantity']:'1',['class'=>'mod option_fields','id'=>'quantity','min'=> 1])}}
										</div>			
										<span class="qty_help" style="color: #CCC;font-size: 12px;display: none;clear: both;">
											<i style="color: #8bc242;"><b style="color: red;">*Only One Art File per Job Allowed.</b><br>You are choosing to duplicate this product, is that correct? </i>
										</span>
									</div>

									@php
									$variant_check = $product->variant;
									$price_sqft_area = $product->price_sqft_area;
									@endphp
									@foreach($product->variants as $variant)
										<div class="form-group main_box">{{Form::label("variants[$variant->id]",$variant->name,['class'=>'form-control-label'])}}<div class="clearfix"></div>
											<div class="coustomeselect">
											@php
											$variant_options = array();
											foreach($variant->variantValues as $value){
												$variant_options[$value->id] = $value->value;
											}
											@endphp	
											{{Form::select('variants['.$variant->id.']',$variant_options,(!empty($cart_product) and array_key_exists('variants',$cart_product))?$cart_product['variants'][$variant->id]:'',['class'=>'option_fields variants_option'])}}
											</div>
										</div>
									@endforeach
									@php
									$show_width_height = $product->show_width_height;
									@endphp
									@if($product->show_width_height == 1)
										@php
										$selected_width = 1;	
										$selected_height = 1;
										if(!empty($cart_product)){
											$selected_width = $cart_product['options']['printing'][0]['width'];
											$selected_height = $cart_product['options']['printing'][0]['height'];
										}else{
											if(!empty($product->min_width)){
												$selected_width = $product->min_width;
											}
											if(!empty($product->min_height)){
												$selected_height = $product->min_height;
											}
										}
										@endphp
										<div class="form-group main_box">{{Form::label('options[printing][0][width]','Width(ft)',array('class'=>'quantity_lab'))}}
											<div class="quantity">{{Form::number('options[printing][0][width]',$selected_width,['class'=>'option_fields mod','id'=>'width','placeholder'=>'Enter Width','min'=>1])}}
											</div>
										</div>
										<div class="form-group main_box">{{Form::label('options[printing][0][height]','Height(ft)',array('class'=>'quantity_lab'))}}
											<div class="quantity">{{Form::number('options[printing][0][height]',$selected_height,['class'=>'option_fields mod','id'=>'height','placeholder'=>'Enter Height','min'=>1])}}
											</div>
										</div>
									@endif
									@if(array_key_exists('printing',$options_array))
									
									@foreach($options_array['printing'] as $option)
									@if($option['type'] == 1)
										<div class="form-group main_box">	{{Form::label('options[printing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$option['name'],array('class'=>'form-control-label'))}}
											<div class="coustomeselect">
												<select name="{{'options[printing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']'}}" class="option_fields option_custom" id="option_custom{{$option['id']}}" data-name="{{$option['name']}}" data-id="{{$option['id']}}">
												<option value="" data-price="0" data-price-type="">Select Option</option>
												@php
												foreach($option['values'] as $value){
													
													$price = 0;
													$weight = 0;
													$flat_rate_additional_price = 0;
													if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
													
													if(array_key_exists('weight',$value) and !empty($value['weight'])){ $weight = $value['weight']; }

													if(array_key_exists('flat_rate_additional_price',$value) and !empty($value['flat_rate_additional_price'])){ $flat_rate_additional_price = $value['flat_rate_additional_price']; }
													
													$selected = '';
													
													if(!empty($cart_product) and isset($cart_product['options']['printing'][$option['id']][str_replace(' ','_',strtolower($option['name']))])){
													
														$val_opt = trim(htmlspecialchars_decode ($value['value'])).'__'.$price;
														
														$val_cart = trim($cart_product['options']['printing'][$option['id']][str_replace(' ','_',strtolower($option['name']))]);	
														
														$val_opt = trim($val_opt);
														$val_cart = trim($val_cart);
														
														if($val_cart == $val_opt){
															$selected = 'selected="selected"';	
														}
													}
													else if(array_key_exists('default',$value) && $value['default'] == 1)
													{
														$selected = 'selected="selected"';	
													}
													
													echo '<option value="'.trim(htmlentities($value['value'])).'__'.$price.'" data-price="'.$price.'" data-flat_rate_additional_price="'.$flat_rate_additional_price.'" data-weight="'.$weight.'" data-price-type="'.$option['price_formate'].'"'.$selected.'>'.htmlentities($value['value']).'</option>';
												}			
												@endphp
												
												</select>
											</div>
											@if(!empty($option['description']))
												<span class="information" title="{{ $option['description'] }}"><i class="fas fa-info-circle"></i> </span>
											@endif
										</div>
									@else
										<div class="form-group main_box">	{{Form::label('options[printing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$option['name'],array('class'=>'form-control-label'))}}
											@php
											$val = '';
											if(!empty($cart_product) and isset($cart_product['options']['printing'][$option['id']][str_replace(' ','_',strtolower($option['name']))])){
												
												$val = $cart_product['options']['printing'][$option['id']][str_replace(' ','_',strtolower($option['name']))];
											}
											@endphp
											<div class="coustomeselect">{{Form::text('options[printing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$val,['class'=>'form-control option_fields','placeholder'=>'Enter '.$option['name'],'rel'=>0,'data-formate'=>$option['price_formate']])}}
											</div>
										</div>
									@endif
									@endforeach
									@endif
									<div class="clearfix"></div>
								</div>
							</div>
							
							@if(array_key_exists('finishing',$options_array))
							<div class="col-xs-6 col-sm-12 col-md-12 col-lg-6 fullwid">
								<div class="finishingoptions">
									<div class="title">
										Finishing Options
									</div>
									@foreach($options_array['finishing'] as $option)
									@if($option['type'] == 1)
										<div class="form-group main_box">	{{Form::label('options[finishing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$option['name'],array('class'=>'form-control-label'))}}
											<div class="coustomeselect">
												<select name="{{'options[finishing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']'}}" class="option_fields option_custom" id="option_custom{{$option['id']}}" data-name="{{$option['name']}}" data-id="{{$option['id']}}">
												<option value="" data-price="0" data-price-type="">Select Option</option>
												@php
												foreach($option['values'] as $value){
													$price = 0;
													$weight = 0;
													$flat_rate_additional_price = 0;

													if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
													
													if(array_key_exists('weight',$value) and !empty($value['weight'])){ $weight = $value['weight']; }

													if(array_key_exists('flat_rate_additional_price',$value) and !empty($value['flat_rate_additional_price'])){ $flat_rate_additional_price = $value['flat_rate_additional_price']; }

													
													$selected = '';
													
													if(!empty($cart_product) and isset($cart_product['options']['finishing'][$option['id']][str_replace(' ','_',strtolower($option['name']))])){
													
														$val_opt = trim(htmlspecialchars_decode ($value['value'])).'__'.$price;
														
														$val_cart = trim($cart_product['options']['finishing'][$option['id']][str_replace(' ','_',strtolower($option['name']))]);	
														
														$val_opt = trim($val_opt);
														$val_cart = trim($val_cart);
														
														if($val_cart == $val_opt){
															$selected = 'selected="selected"';	
														}
													}
													else if(array_key_exists('default',$value) && $value['default'] == 1)
													{
														$selected = 'selected="selected"';	
													}
													
													echo '<option value="'.trim(htmlentities($value['value'])).'__'.$price.'" data-price="'.$price.'" data-flat_rate_additional_price="'.$flat_rate_additional_price.'" data-weight="'.$weight.'" data-price-type="'.$option['price_formate'].'"'.$selected.'>'.htmlentities($value['value']).'</option>';
												}		
												@endphp
												</select>
											</div>
											@if(!empty($option['description']))
												<span class="information" title="{{ $option['description'] }}"><i class="fas fa-info-circle"></i> </span>
											@endif
										</div>
									@else
										<div class="form-group main_box">	{{Form::label('options[finishing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$option['name'],array('class'=>'form-control-label'))}}	
											<div class="coustomeselect">{{Form::text('options[finishing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']','',['class'=>'form-control option_fields','placeholder'=>'Enter '.$option['name'],'rel'=>0,'data-formate'=>$option['price_formate']])}}
											</div>
										</div>
									@endif
									@endforeach
									<div class="clearfix"></div>
								</div>
							</div>
							@endif
						</div>
						<div class="row">
							@if(array_key_exists('production',$options_array))
							<div class="col-xs-6 col-sm-12 col-md-12 col-lg-6 fullwid">
								<div class="finishingoptions">
									<div class="title">
										Design Options
									</div>
									@foreach($options_array['production'] as $option)
									@if($option['type'] == 1)
										<div class="form-group main_box">	{{Form::label('options[production]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$option['name'],array('class'=>'form-control-label'))}}
											<div class="coustomeselect">
												<select name="{{'options[production]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']'}}" class="option_fields option_custom" id="option_custom{{$option['id']}}" data-name="{{$option['name']}}" data-id="{{$option['id']}}">
												<option value="" data-price="0" data-price-type="">Select Option</option>
												@php
												foreach($option['values'] as $value){
													$price = 0;
													$weight = 0;
													$flat_rate_additional_price = 0;
													if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
													
													if(array_key_exists('weight',$value) and !empty($value['weight'])){ $weight = $value['weight']; }

													if(array_key_exists('flat_rate_additional_price',$value) and !empty($value['flat_rate_additional_price'])){ $flat_rate_additional_price = $value['flat_rate_additional_price']; }
													
													
													$selected = '';
													
													if(!empty($cart_product) and isset($cart_product['options']['production'][$option['id']][str_replace(' ','_',strtolower($option['name']))])){
													
														$val_opt = trim(htmlspecialchars_decode($value['value'])).'__'.$price;
														
														$val_cart = trim($cart_product['options']['production'][$option['id']][str_replace(' ','_',strtolower($option['name']))]);	
														
														$val_opt = trim($val_opt);
														$val_cart = trim($val_cart);
														
														if($val_cart == $val_opt){
															$selected = 'selected="selected"';	
														}
													}
													else if(array_key_exists('default',$value) && $value['default'] == 1)
													{
														$selected = 'selected="selected"';	
													}

													
													
													echo '<option value="'.trim(htmlentities($value['value'])).'__'.$price.'" data-price="'.$price.'" data-flat_rate_additional_price="'.$flat_rate_additional_price.'" data-weight="'.$weight.'" data-price-type="'.$option['price_formate'].'" '.$selected.'>'.htmlentities($value['value']).'</option>';
												}		
												@endphp
												</select>
											</div>
											@if(!empty($option['description']))
												<span class="information" title="{{ $option['description'] }}"><i class="fas fa-info-circle"></i> </span>
											@endif
										</div>
									@else
										<div class="form-group main_box">	{{Form::label('options[production]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$option['name'],array('class'=>'form-control-label'))}}	
											<div class="coustomeselect">{{Form::text('options[production]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']','',['class'=>'form-control option_fields','placeholder'=>'Enter '.$option['name'],'rel'=>0,'data-formate'=>$option['price_formate']])}}
											</div>
										</div>
									@endif
									@endforeach
									<div class="clearfix"></div>
								</div>
							</div>
							@endif
							<div class="col-xs-6 col-sm-12 col-md-12 col-lg-6 fullwid pull-right">
								<div class="totla_amount">
									<strong id='product_amount'> ${{priceFormat($product->price)}}</strong>
									<span id="product_each_amount">(${{priceFormat($product->price)}} each)</span>
									{{Form::hidden('price_default',$product->price)}}
									{{Form::hidden('price','')}}
									{{Form::hidden('sub_total',$product->price,['class'=>'sub_total','class'=>'sub_total'])}}
									<i class="fa fa-spinner fa-spin hide" style="font-size:24px"></i>
									<a href="javascript:void(0)" class="addcart"> <i class="fas fa-cart-plus"></i> ADD TO CART</a>
									<div class="col-xs-12 no-padding quote_btn_div">
										<a href="javascript:void(0)" class="quote_btn quote_email_btn" data-type="email">Email Quote</a>
										<a href="javascript:void(0)" class="quote_btn quote_shipping_btn">Get Ship Rate</a>
									</div>
									<div class="col-xs-12">
										<script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=5abc94de1fff98001395a821&product=social-ab' async='async'></script>
										<div class="sharethis-inline-share-buttons"></div>
									</div>
								</div>
							</div>
						</div>
						<p class="samllsize">Production Turnaround Time is {{$product->turnaround_time}}.</p>
						<p class="call"> Call us at 800-920-9527 for Rush Services.</p>
					{{Form::close()}}
				</div>
			</div>
			<div class="mobile_custom_tab hidden-md hidden-lg"></div>
		</div>
		<div class="space"></div>
		<?php
		//pr($related_products);die;
		?>
		@if(count($related_products) > 0)
		<div class="row">
			<h3 class="relate_title">Related Products</h3>
			@foreach($related_products as $related_product)
			<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 fullwid">
				<div class="related_product">
					<div class="imgbox">
						<a href="{{url($related_product->slug)}}">	@if(@getimagesize(url('public/uploads/product/'.$related_product->image)))
							<img src="{{URL::to('/public/uploads/product/'.$related_product->image)}}" alt="{{$related_product->image_title}}" title="{{$related_product->image_title}}" />
						@else
							<img src="{{URL::to('/public/img/front/img.jpg')}}" alt="" />
						@endif
						</a>
					</div>
					<a href="{{url($related_product->slug)}}"><h6>{{$related_product->name}}</h6></a>
					<?php /* @php
						$amount = $related_product->price;
						foreach($related_product->product_prices as $prices){
							$amount *= $prices->min_area;
							break;
						}
					@endphp
					<p>Starting at <strong> <small>$</small> {{$amount}} </strong></p> */ ?>
					<p>{!! display_product_price($related_product) !!}</p>
					<a href="{{url($related_product->slug)}}" class="addcart"><i class="fas fa-cart-plus"></i> VIEW OPTIONS</a>
				</div>
			</div>
			@endforeach
		</div>
		@endif
	</div>
</section>
<div class="modal fade" id="product_quote">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-envelope-open"></i> Email Estimate</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						{{ Form::model('email_quote', ['url' => ['admin/product/quote'],'files'=>true,'id'=>'email_quote','onsubmit'=>'this.preventDefault();']) }}
						{{Form::hidden('quote_type','',['id'=>'quote_type']) }}
						<div class="col-xs-6">
							<div class="form-group">
								{{ Form::label('email', 'Email Address',array('class'=>'form-control-label'))}}	
								{{ Form::text('email','',['class'=>'form-control quote_input','id'=>'email','placeholder'=>'Enter Email Address'])}}
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								{{ Form::label('name', 'Name',array('class'=>'form-control-label'))}}	
								{{ Form::text('name','',['class'=>'form-control quote_input','id'=>'name','placeholder'=>'Enter Name'])}}
							</div>
						</div>
						<div class="col-xs-6 shipping_detail hide">
							<div class="form-group">
								{{ Form::label('shipping_option', 'Shipping Option',array('class'=>'form-control-label'))}}	
								{{ Form::select('shipping_option',config('constants.Shipping_option'),'',['class'=>'form-control quote_input','id'=>'shipping_option','placeholder'=>'Enter Shipping Option'])}}
							</div>
						</div>
						<div class="col-xs-6 shipping_detail hide">
							<div class="form-group">
								{{ Form::label('zipcode', 'Zipcode',array('class'=>'form-control-label'))}}	
								{{ Form::text('zipcode','',['class'=>'form-control quote_input','id'=>'zipcode','placeholder'=>'Enter Zipcode'])}}
							</div>
						</div>
						<div class="col-xs-12">
							<div class="form-group">
								{{ Form::label('message', 'Message',array('class'=>'form-control-label'))}}	
								{{ Form::textarea('message','',['class'=>'form-control quote_input','id'=>'message','placeholder'=>'Enter message','rows'=>3])}}
							</div>
						</div>
						<div class="col-xs-12">
							<div class="form-group">
								{{ Form::button('Email Quote',['class'=>'btn btn-success send_quote pull-right'])}}
								<img id="quote_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}" style="display:none;width:50px;height:50px;float:right;">
							</div>
						</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="product_shipping_quote">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-envelope-open"></i> Shipping Estimate</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						{{ Form::model('shipping_quote', ['url' => ['#'],'files'=>true,'id'=>'shipping_quote','onsubmit'=>'this.preventDefault();']) }}
						<div class="col-xs-6">
							<div class="form-group">
								{{ Form::label('shipping_option', 'Shipping Option',array('class'=>'form-control-label'))}}	
								{{ Form::select('shipping_option',config('constants.Shipping_option'),'',['class'=>'form-control quote_input','id'=>'shipping_option','placeholder'=>'Enter Shipping Option'])}}
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								{{ Form::label('zipcode', 'Zipcode',array('class'=>'form-control-label'))}}	
								{{ Form::text('zipcode','',['class'=>'form-control quote_input','id'=>'zipcode','placeholder'=>'Enter Zipcode'])}}
							</div>
						</div>
						<div class="col-xs-12 shipping_detail"></div>
						<div class="col-xs-12">
							<div class="form-group">
								{{ Form::button('Get Ship Rate',['class'=>'btn btn-success get_shipping pull-right'])}}
								<img id="shipping_quote_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}" style="display:none;width:50px;height:50px;float:right;">
							</div>
						</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@include('/product/detail_js')
@endsection