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
					</div>
	
	
	</div>
		<div class="col-sm-6"></div>
	
	</div>
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
					<div class="leftslider_prodct custom_tab" id="accordion">
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
						<div class="panel">
							<div class="box-header with-border" style="margin-bottom: 10px;">
								<a data-toggle="collapse" data-parent="#accordion" href="#design_templates">
									<div class="productselect" style="padding: 5px 8px;">DESIGN TEMPLATES</div>
								</a>
							</div>
							<div id="design_templates" class="panel-collapse collapse">
								<div class="coustomeflutter">
								@php
									if(!empty($product->design_templates)){
										echo $product->design_templates;
									}else{
										echo '<p>The design templates will be comming very soon .';
									}
								@endphp
								</div>
							</div>
						</div>
						<div class="panel">
							<div class="box-header with-border" style="margin-bottom: 10px;">
								<a data-toggle="collapse" data-parent="#accordion" href="#art_file_preperation">
									<div class="productselect" style="padding: 5px 8px;">ART FILE PREPERATION</div>
								</a>
							</div>
							<div id="art_file_preperation" class="panel-collapse collapse">
								<div class="coustomeflutter">
									@php
									if(!empty($product->art_file_preparations)){
										echo $product->art_file_preparations;
									}else{
										echo '<p>The art files will be comming very soon .';
									}
								@endphp
								</div>
							</div>
						</div>
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
						<div class="panel">
							<div class="box-header with-border" style="margin-bottom: 10px;">
								<a data-toggle="collapse" data-parent="#accordion" href="#customer_reviews">
									<div class="productselect" style="padding: 5px 8px;">CUSTOMER REVIEWS</div>
								</a>
							</div>
							<div id="customer_reviews" class="panel-collapse collapse">
								<div class="coustomeflutter">
									@if(count($reviews) > 0)
										@foreach($reviews as $review)
											<div class="show_reviews">
												<span class="comment_star" style="padding:0px;" data="{{$review->rating}}"></span>
												<p>
													{{ucfirst($review->comment)}}
													<br/>Posted on : {{date('d-M',strtotime($review->created_at))}} -By  {{ucwords($review->user->fname.' '.$review->user->lname)}}
												</p>
												<hr/>
											</div>
										@endforeach
										<button type="button" class="btn btn-xs btn-success pull-right view_more">View More</button>
									@else
										<p>Reviews will be comming very soon .</p>
									@endif
								</div>
							</div>
						</div>               
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="product_page">
					<h4>{{$product->name}}</h4>
					<p class="starting">Starting at <strong>${{number_format((float)$product->price, 2, '.', '')}} </strong></p>
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
						{{Form::hidden('min_width',(!empty($product->min_width))?$product->min_width:0)}}
						{{Form::hidden('max_width',(!empty($product->max_width))?$product->max_width:0)}}
						{{Form::hidden('min_height',(!empty($product->min_height))?$product->min_height:0)}}	{{Form::hidden('max_height',(!empty($product->max_height))?$product->max_height:0)}}
						<div class="row">							
							<div class="col-xs-6 col-sm-12 col-md-12 col-lg-5 fullwid">
								<div class="finishingoptions">
									<div class="title">
										Printing Options
									</div>
									<div class="form-group main_box">{{Form::label('quantity','Quantity',['class'=>'quantity_lab'])}}
										<div class="quantity">{{Form::number('quantity','1',['class'=>'mod option_fields','id'=>'quantity','min'=> 1])}}
										</div>
									</div>
									@php
									$variant_check = $product->variant;
									$price_sqft_area = $product->price_sqft_area;
									@endphp
									@foreach($product->variants as $variant)
										<div class="form-group main_box">{{Form::label("variants[$variant->id]",$variant->name,['class'=>'form-control-label'])}}<div class="clearfix"></div>
											<div class="coustomeselect">
											<select name="{{'variants['.$variant->id.']'}}" class="option_fields variants_option">
											@php
											$variant_options = array();
											foreach($variant->variantValues as $value){
											@endphp
												<option value="{{$value->id}}">{{$value->value}}</option>
											@php
											}
											@endphp	
											</select>
											</div>
										</div>
									@endforeach
									@php
									$show_width_height = $product->show_width_height;
									@endphp
									@if($product->show_width_height == 1)
										<div class="form-group main_box">{{Form::label('options[printing][0][width]','Width(ft)',array('class'=>'quantity_lab'))}}
											<div class="quantity">{{Form::number('options[printing][0][width]',(!empty($product->min_width))?$product->min_width:1,['class'=>'option_fields mod','id'=>'width','placeholder'=>'Enter Width','min'=>1])}}
											</div>
										</div>
										<div class="form-group main_box">{{Form::label('options[printing][0][height]','Height(ft)',array('class'=>'quantity_lab'))}}
											<div class="quantity">{{Form::number('options[printing][0][height]',(!empty($product->min_height))?$product->min_height:1,['class'=>'option_fields mod','id'=>'height','placeholder'=>'Enter Height','min'=>1])}}
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
													if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
													echo '<option value=\''.trim(htmlentities($value['value'])).'__'.$price.'\' data-price="'.$price.'" data-price-type="'.$option['price_formate'].'">'.htmlentities($value['value']).'</option>';
												}												
												@endphp
												
												</select>
											</div>
										</div>
									@else
										<div class="form-group main_box">	{{Form::label('options[printing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$option['name'],array('class'=>'form-control-label'))}}	
											<div class="coustomeselect">{{Form::text('options[printing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']','',['class'=>'form-control option_fields option_custom','placeholder'=>'Enter '.$option['name'],'rel'=>0,'data-formate'=>$option['price_formate']])}}
											</div>
										</div>
									@endif
									@endforeach
									@endif
									<div class="clearfix"></div>
								</div>
							</div>
							
							@if(array_key_exists('finishing',$options_array))
							<div class="col-xs-6 col-sm-12 col-md-12 col-lg-7 fullwid">
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
													if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
													echo '<option value=\''.trim(htmlentities($value['value'])).'__'.$price.'\' data-price="'.$price.'" data-price-type="'.$option['price_formate'].'">'.htmlentities($value['value']).'</option>';
												}												
												@endphp
												</select>
											</div>
										</div>
									@else
										<div class="form-group main_box">	{{Form::label('options[finishing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$option['name'],array('class'=>'form-control-label'))}}	
											<div class="coustomeselect">{{Form::text('options[finishing]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']','',['class'=>'form-control option_fields option_custom','placeholder'=>'Enter '.$option['name'],'rel'=>0,'data-formate'=>$option['price_formate']])}}
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
							<div class="col-xs-6 col-sm-12 col-md-12 col-lg-7 fullwid">
								<div class="finishingoptions">
									<div class="title">
										Production Options
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
													if(array_key_exists('price',$value) and !empty($value['price'])){ $price = $value['price']; }
													echo '<option value=\''.trim(htmlentities($value['value'])).'__'.$price.'\' data-price="'.$price.'" data-price-type="'.$option['price_formate'].'">'.htmlentities($value['value']).'</option>';
												}												
												@endphp
												</select>
											</div>
										</div>
									@else
										<div class="form-group main_box">	{{Form::label('options[production]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']',$option['name'],array('class'=>'form-control-label'))}}	
											<div class="coustomeselect">{{Form::text('options[production]['.$option['id'].']['.str_replace(' ','_',strtolower($option['name'])).']','',['class'=>'form-control option_fields option_custom','placeholder'=>'Enter '.$option['name'],'rel'=>0,'data-formate'=>$option['price_formate']])}}
											</div>
										</div>
									@endif
									@endforeach
									<div class="clearfix"></div>
								</div>
							</div>
							@endif
							<div class="col-xs-6 col-sm-12 col-md-12 col-lg-5 fullwid pull-right">
								<div class="totla_amount">
									<strong id='product_amount'> ${{$product->price}}</strong>
									<span id="product_each_amount">(${{$product->price}} each)</span>
									{{Form::hidden('price_default',$product->price)}}
									{{Form::hidden('price','')}}
									{{Form::hidden('sub_total',$product->price,['class'=>'sub_total','class'=>'sub_total'])}}
									<i class="fa fa-spinner fa-spin hide" style="font-size:24px"></i>
									<a href="javascript:void(0)" class="addcart"> <i class="fas fa-cart-plus"></i> ADD TO CART</a>
									<div>
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
		</div>
		<div class="space"></div>
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
					<p>Starting at <strong> <small>$</small> {{$related_product->price}} </strong></p>
					<a href="{{url($related_product->slug)}}" class="addcart"><i class="fas fa-cart-plus"></i> VIEW OPTIONS</a>
				</div>
			</div>
			@endforeach
		</div>
		@endif
	</div>
</section>

<script type="text/javascript">
$(document).ready(function() {
	
var combinations = <?php echo json_encode($sets,JSON_PRETTY_PRINT);?>;
var discount_arr = <?php echo json_encode($discount,JSON_PRETTY_PRINT);?>;
//console.log(combinations);
//console.log(discount_arr);

var orderObj={};

orderObj.price_default=Number($('input[name="price_default"]').val());
orderObj.quantity = 1;

orderObj.price_sqft_area = Number('{{$price_sqft_area}}');
orderObj.variant = Number('{{$variant_check}}');
orderObj.show_width_height = Number('{{$show_width_height}}');
if(orderObj.show_width_height == 1){
	orderObj.width = Number($('input#width').val());
	orderObj.height = Number($('input#height').val());
}else{
	orderObj.width = 1;
	orderObj.height = 1;
}

if($("input#width").length > 0 && $("input#height").length > 0){
	orderObj.width = Number($('input#width').val());
	orderObj.height = Number($('input#height').val());
}
orderObj.variant_price=Number($('input[name="price_default"]').val());
orderObj.variant_option_price=null;
orderObj.variant_option_price_300=null;
orderObj.variant_option_price_500=null;
orderObj.variant_option_price_1000=null;

var comb_str = '';
$('.variants_option').each(function(index,val){
	comb_str += $('option:selected', this).val()+'_';
});
comb_str = comb_str.substring(0, comb_str.length - 1);

$.each(combinations, function (index, value) {
    if(index.replace('-','_') == comb_str){
		orderObj.variant_price = Number(value.price);
		orderObj.variant_option_price = Number(value.price);
		if(orderObj.price_sqft_area == 1){
			orderObj.variant_option_price_300 = Number(value.price_300);
			orderObj.variant_option_price_500 = Number(value.price_500);
			orderObj.variant_option_price_1000 = Number(value.price_1000);
		}
	}
});

orderObj.custom_option= new Object();
orderObj.gross_price=0;
orderObj.product_total_price=0;
orderObj.discount_per=0;
orderObj.discount=0;
orderObj.total_price=0;	
console.log(orderObj);
function calculation(){	
	var total_price = orderObj.variant_price * (orderObj.width * orderObj.height) * orderObj.quantity;	

	$.each(orderObj.custom_option, function (cid, coption) {
		//console.log(coption);		
		if(coption.price_type == "gross")
		{
			total_price = total_price + ( coption.price * orderObj.quantity ) ;
		}
		else if(coption.price_type == "area")
		{
			total_price = total_price + ( (orderObj.width * orderObj.height) * coption.price *orderObj.quantity );
		}
		else if(coption.price_type == "parimeter")
		{
			total_price = total_price + ( ((orderObj.width + orderObj.height) * 2 ) * coption.price * orderObj.quantity);
		}
		else if(coption.price_type == "item")
		{
			total_price = (total_price + coption.price );
		}
	});
	
	orderObj.gross_price = total_price;
	orderObj.product_total_price = total_price/orderObj.quantity;
	orderObj.discount = (orderObj.gross_price *  orderObj.discount_per)/100;
	orderObj.total_price =  orderObj.gross_price - orderObj.discount;
	orderObj.total_price =  orderObj.total_price .toFixed(2);

	$("#product_amount").html("$"+orderObj.total_price);
	$(".sub_total").val(orderObj.total_price);
	$("#product_each_amount").html("$"+(orderObj.gross_price/orderObj.quantity)+" each");
	

	
	console.log(orderObj);
	return true;		
}

var min_width = Number($('input[name="min_width"]').val());
var max_width = Number($('input[name="max_width"]').val());

var min_height = Number($('input[name="min_height"]').val());
var max_height = Number($('input[name="max_height"]').val());

$('input[name="quantity"],#width,#height').on('keyup input change', function(e) {
    if($(this).val() < 1)
		$(this).val('1');
	
	$(this).closest('.main_box').removeClass('has-error');
	$($(this)).closest('div').nextAll().remove();
	
	var eid = $(this).attr("id");
	var on = 1;
	if(eid=='width'){	
		var width = Number($(this).val());
		if(width < min_width){
			on = 0;
			$(this).closest('.main_box').addClass('has-error');
			$("<div class='clearfix'></div><span class='help-block'>Minimum width "+min_width+" is required.</span>").insertAfter($(this).closest('div'));
		}
		if(max_width != 0){
			if(width > max_width){
				on = 0;
				$(this).closest('.main_box').addClass('has-error');
				$("<div class='clearfix'></div><span class='help-block'>Maximum width "+max_width+" is required.</span>").insertAfter($(this).closest('div'));
			}
		}
		
		if(on == 1){
			orderObj.width = width;
			check_price();
		}
	}
	else if(eid=='height'){
		var height = Number($(this).val());
		
		if(height < min_height){
			on = 0;
			$(this).closest('.main_box').addClass('has-error');
			$("<div class='clearfix'></div><span class='help-block'>Minimum height "+min_height+" is required.</span>").insertAfter($(this).closest('div'));
		}
		if(max_height != 0){
			if(height > max_height){
				on = 0;
				$(this).closest('.main_box').addClass('has-error');
				$("<div class='clearfix'></div><span class='help-block'>Maximum height "+max_height+" is required.</span>").insertAfter($(this).closest('div'));
			}
		}
		
		if(on == 1){
			orderObj.height = height;
			check_price();
		}
	}
	else if(eid=='quantity'){
		orderObj.quantity = Number($(this).val());
		orderObj.discount_per = 0;
		$.each(discount_arr,function(index,val){
			if(index <= orderObj.quantity)
				orderObj.discount_per=Number(val);
		});
	}

	calculation();		
});

$('.option_custom').change(function(){

	var sid=$(this).attr('id');

	custom_option = new Object();

	custom_option.id = $(this).attr('data-id');
	custom_option.name = $(this).attr('data-name');		
	custom_option.value = $('#'+sid+' :selected').attr('value');
	custom_option.price = parseFloat($('#'+sid+' :selected').attr('data-price'));
	custom_option.price_type = $('#'+sid+' :selected').attr('data-price-type');

	orderObj.custom_option[custom_option.id]= custom_option;

	calculation();
		
});

$('.variants_option').change(function(){

	var comb_str = '';
	$('.variants_option').each(function(index,val){
		comb_str += $('option:selected', this).val()+'_';
	});
	comb_str = comb_str.substring(0, comb_str.length - 1);
	$.each(combinations, function (index, value) {
        if(index.replace('-','_') == comb_str.replace('-','_')){
			orderObj.variant_price = Number(value.price);
			orderObj.variant_option_price = Number(value.price);
			if(orderObj.price_sqft_area == 1){
				orderObj.variant_option_price_300 = Number(value.price_300);
				orderObj.variant_option_price_500 = Number(value.price_500);
				orderObj.variant_option_price_1000 = Number(value.price_1000);
			}
		}
		check_price();
	});
	//console.log(orderObj);
	calculation();
		
});

function check_price()
{
	if(orderObj.price_sqft_area == 0){
		orderObj.variant_price = orderObj.variant_option_price;
	}else{
		var sqft_area = orderObj.width * orderObj.height;
		if(sqft_area > 0 && sqft_area <= 300){
			orderObj.variant_price = orderObj.variant_option_price;
		}else if(sqft_area > 300 && sqft_area <= 500){
			orderObj.variant_price = orderObj.variant_option_price_300;
		}else if(sqft_area > 500 && sqft_area <= 1000){
			orderObj.variant_price = orderObj.variant_option_price_500;
		}else if(sqft_area > 1000){
			orderObj.variant_price = orderObj.variant_option_price_1000;
		}
	}
	//alert("check "+orderObj.variant_price)
}

check_price();
calculation();


$('.addcart').click(function(){
	var on = 1;
	$('.option_fields').each( function (index, data) {
		$(this).closest('.main_box').removeClass('has-error');
		$($(this)).closest('div').nextAll().remove();
		if($(this).val() == ''){
			on = 0;
			$(this).closest('.main_box').addClass('has-error');
			$("<div class='clearfix'></div><span class='help-block'>This field is required</span>").insertAfter($(this).closest('div'));
		}
	});
	
	if(on){
		var form = $('#product_form');
		$.ajax({
			url:'{{url("cart/add")}}',
			type:'post',
			dataType:'json',
			data:{'object':orderObj,'form':form.serialize()},
			beforeSend: function() {
				$('#fade').fadeIn();
				$('.fa-spinner').removeClass('hide');
			},
			success:function(data){
				$('#fade').fadeOut();
				$('.fa-spinner').addClass('hide');
				if(data.status == 'success'){
					window.location.href = "{{url('cart')}}";
					$('.cart_menu').html(data.res);
					if(data.session_count > 0){
						$('#cart_count').html(data.session_count).removeClass('hide');
					}else{
						$('#cart_count').html('').removeClass('hide');
					}
				}
			}
		});
	}
});


<!-------flexslider---->	
$('#imageGallery').lightSlider({
	gallery: true,
	item: 1,
	loop: true,
	thumbItem: 4,
	slideMargin: 0,
	enableDrag: false,
	currentPagerPosition: 'left',
	onSliderLoad: function(el) {
		/*el.lightGallery({
			selector: '#imageGallery .lslide'
		}); */
	}
});

});

$(function () {
	function put_star(){
		$('.comment_star').each(function(){
			var star = $(this).attr('data');
			$(this).rateYo({
				normalFill: "#A0A0A0",
				rating: Number(star),
				halfStar: true,
				readOnly: true,
				starWidth: "20px",
			  });
		});
	}
	
  $("#rateYo").rateYo({
    normalFill: "#A0A0A0",
	rating: Number('{{$avg}}'),
    halfStar: true,
	readOnly: true,
	starWidth: "20px",
  });
  
  var current_page = 1;
  var dataGet = 1;
  $('.view_more').click(function(){
	  if(dataGet == 1){
		$.ajax({
			url:'{{url("reviews/get?page=")}}'+(++current_page),
			type:'get',
			data:{'product_id':'{{$product->id}}'},
			dataType:'json',
			success:function(data){
				if(data.status == 'success'){
					if(data.data == 1){
						$('.show_reviews:last').append(data.str);
						put_star();
					}else{
						dataGet = 0;
						$('.view_more').css({'display':'none'});
					}
				}
			}
		});
	  }
  });

  put_star();
});

</script>
@endsection