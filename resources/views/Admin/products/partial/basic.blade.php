<!-- product basic info form -->
<form role="form" class="productForm" id="productBasicInfo" method="post" action="{{ url('admin/products/edit_basic/'.$id) }}" enctype="multipart/form-data">
{{ csrf_field() }}					
	<h3 class="formheading">Product Basic Info:</h3>
	<div class="col-md-12">
		<div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Name<span class="text-danger">*</span></label>
			<div class="col-sm-6">
				<input id="name" type="text" class="form-control" name="name" value="{{ array_key_exists('name',old())?old('name'):$product->name }}" placeholder="Product Name" />
				@if ($errors->has('name'))
					<span class="help-block">{{ $errors->first('name') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('slug') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Slug<span class="text-danger">*</span></label>
			<div class="col-sm-6">
				<input id="slug" type="text" class="form-control" name="slug" value="{{ array_key_exists('slug',old())?old('slug'):$product->slug }}" placeholder="Slug">
				@if ($errors->has('slug'))
					<span class="help-block">{{ $errors->first('slug') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('category_id') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Select Parent Category<span class="text-danger">*</span></label>
			<div class="col-sm-6">		{{Form::select('category_id',$categories,array_key_exists('category_id',old())?old('category_id'):$product->category_id,['class'=>'form-control'])}}
				@if ($errors->has('category_id'))
					<span class="help-block">{{ $errors->first('category_id') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('cat_image_title') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Category Image Title/Alt</label>
			<div class="col-sm-6">
				<input id="cat_image_title" type="text" class="form-control" name="cat_image_title" value="{{array_key_exists('cat_image_title',old())?old('cat_image_title'):$product->cat_image_title}}" placeholder="Category Image Title">
				@if ($errors->has('cat_image_title'))
					<span class="help-block">{{ $errors->first('cat_image_title') }}</span>
				@endif
			</div>
		</div>
		<div id="cat_page_image_div" class="form-group row{{ $errors->has('cat_image') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Category Image
			<br/><span class="size_msg">Image size(385X200)</span></label>
			<div class="col-sm-6">
				<input id="cat_image" type="file" class="" name="cat_image"/>
				@if ($errors->has('cat_image'))
					<span class="help-block">{{ $errors->first('cat_image') }}</span>
				@else
					<span class="help-block"></span>
				@endif
			</div>
		</div>
		<div class="form-group row" id="cat_image_div">
			<label class="col-sm-3 form-control-label">&nbsp;</label>
			<div class="col-sm-9" id="cat_images_div">	@if(@getimagesize(url('/public/uploads/product/'.$product->cat_image)))
				<img class="img-responsive image_list" src="{{URL::to('/public/uploads/product/'.$product->cat_image)}}" alt="Photo">
			@endif
			</div>
		</div>
		<div id="image_main_div" class="form-group row{{ $errors->has('image') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Product Image<span class="text-danger">*</span>
			<br/><span class="size_msg">Image size(345X260)</span></label>
			<div class="col-sm-6">
				<input id="image" type="file" class="" name="image"/>
				@if ($errors->has('image'))
					<span class="help-block">{{ $errors->first('image') }}</span>
				@else	
					<span class="help-block"></span>
				@endif
			</div>
		</div>
		<div class="form-group row" id="image_div">
			<label class="col-sm-3 form-control-label">&nbsp;</label>
			<div class="col-sm-9" id="product_images_div">	@if(@getimagesize(url('/public/uploads/product/'.$product->image)))
				<img class="img-responsive image_list" src="{{URL::to('/public/uploads/product/'.$product->image)}}" alt="Photo">
			@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('image_title') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Product Image Title/Alt</label>
			<div class="col-sm-6">
				<input id="image_title" type="text" class="form-control" name="image_title" value="{{array_key_exists('image_title',old())?old('image_title'):$product->image_title}}" placeholder="Product Image Title">
				@if ($errors->has('image_title'))
					<span class="help-block">{{ $errors->first('image_title') }}</span>
				@endif
			</div>
		</div>
		
		<div id="product_image_div" class="form-group row{{ $errors->has('product_image') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Others Images<span class="text-danger">*</span>
			<br/><span class="size_msg">Image size(545x545)</span></label>
			<div class="col-sm-9">
				<input id="product_image" type="file" class="" name="product_image[]" multiple />
			</div>
			<span id="image_error" class="help-block"></span>
		</div>
		<div class="form-group row hide" id="images_div">
			<label class="col-sm-3 form-control-label">&nbsp;</label>
			<div class="col-sm-9">
				<div class="panel panel-info product_panel">
					<div class="panel-heading">
						<h5>New Product Images</h5>
					</div>
					<div class="panel-body custom_option_panel" id="">
						<div class="col-sm-12">
							<div class="row" id="images"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@if(count($product->Images) >= 1)
		<div class="form-group row{{ $errors->has('product_image') ? ' has-error' : '' }}">
			<div class="col-md-12">
				<div class="col-md-3">&nbsp;</div>
				<div class="box-body col-md-9">
					<div class="panel panel-info product_panel">
						<div class="panel-heading">
							<h5>Product Images</h5>
						</div>
						<div class="panel-body custom_option_panel" id="">
							<div class="col-sm-12">
								<div class="row">
									
								@foreach($product->Images as $image)
									@if($image->type == 2)
									<div class="col-xs-6 col-sm-4 col-md-3" id="image_div_{{$image->id}}">
										<div class="image_main_box">
											<label for="">
												<div class="square">
													<img class="img-responsive" src="{{URL::to('/public/uploads/product/'.$image->name)}}" alt="Photo">
												</div>
											</label>
											<label class="col-sm-8 col-xs-12 pull-left no-padding">Disp. Order : </label> &nbsp;<input type="number" name="product_images_weight[{{$image->id}}]" value="{{$image->weight}}" class="col-sm-3 col-xs-12 no-padding" max="{{count($product->Images)}}" min="1"/>
											<a class="remove_media" href="javascript:void(0)" onclick="delete_image('{{ $image->id }}')">Delete</a>
										</div>
									</div>	
									@endif
								@endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif
		
		<div class="form-group row{{ $errors->has('excerpt') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Excerpt<span class="text-danger">*</span></label>
			<div class="col-sm-9">
				<textarea class="form-control" name="excerpt" id="excerpt" value="{{ old('excerpt') }}" placeholder="Excerpt">{{array_key_exists('excerpt',old())?old('excerpt'):$product->excerpt}}</textarea>
				@if ($errors->has('excerpt'))
					<span class="help-block">{{ $errors->first('excerpt') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('short_description') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Short Description<span class="text-danger">*</span></label>
			<div class="col-sm-9">
				<textarea class="form-control editors" name="short_description" id="short_description" value="{{(old('short_description'))?old('short_description'):$product->short_description }}" placeholder="Short Description">{{array_key_exists('short_description',old())?old('short_description'):$product->short_description}}</textarea>
				@if ($errors->has('short_description'))
					<span class="help-block">{{ $errors->first('short_description') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ ($errors->has('product_detail') or $errors->has('art_file') or $errors->has('design_template')) ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Descriptions<span class="text-danger">*</span></label>
			<div class="col-sm-9">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#product_detail_div" data-toggle="tab">Product Detail</a></li>
						<li class=""><a href="#art_file_div" data-toggle="tab">Art File Preparations</a></li>
						<li class=""><a href="#design_template_div" data-toggle="tab">Design Templates</a></li>
						@php
							$k = 1;
						@endphp
						@if(count($product->custom) > 0)
							@foreach($product->custom as $key=>$val)
								<li class="extra_li_{{$val->id}}"><a class="pull-left" href="#extra_tab_{{$val->id}}" data-toggle="tab">{{$val->title}}</a><i class="fa fa-window-close delete_tab pull-left" aria-hidden="true" data="extra" data-id="{{$val->id}}"></i></li>
							@endforeach
						@endif
						@if(old('custom'))
							@foreach(old('custom') as $val)
								<li class="new_li_{{$k}}"><a class="pull-left" href="#new_tab{{$k}}" data-toggle="tab">{{$val['title']}}</a><i class="fa fa-window-close delete_tab pull-left" aria-hidden="true" data="new" count="{{$k}}"></i></li>
								@php
									$k++;
								@endphp
							@endforeach
						@endif
						<li class="add_tab"><a href="javascript:void(0)">New Tab</a></li>
					</ul>
					<div class="tab-content">
						<div class="active tab-pane" id="product_detail_div">
							<textarea class="form-control editors" name="product_detail" id="product_detail">{{(old('product_detail'))?old('product_detail'):$product->description}}</textarea>
						</div>
						<div class="tab-pane" id="art_file_div">
							<textarea class="form-control editors" name="art_file" id="art_file">{{(old('art_file'))?old('art_file'):$product->art_file_preparations}}</textarea>
						</div>
						<div class="tab-pane" id="design_template_div">
							<textarea class="form-control editors" name="design_template" id="design_template">{{(old('design_template'))?old('design_template'):$product->design_templates}}</textarea>
						</div>
						@php
							$k = 1;
						@endphp
						@if(count($product->custom) > 0)
							@foreach($product->custom as $key=>$val)
								<div class="tab-pane" id="extra_tab_{{$val->id}}">
									<textarea class="form-control editors" name="extra_tab[{{$val->id}}][body]" id="extra_tab[{{$val->id}}][body]">{{$val->body}}</textarea>
								</div>
							@endforeach
						@endif
						@if(old('custom'))
							@foreach(old('custom') as $key=>$val)
								<div class="tab-pane" id="new_tab{{$k}}">
									<label for="custom[{{$k}}][title]">Title</label>
									<input class="form-control" placeholder="Enter Title" name="custom[{{$k}}][title]" type="text" value="{{$val['title']}}" id="custom[{{$k}}][title]"/>
									
									<label for="custom[{{$k}}][body]">Body</label>
									<textarea class="form-control editors" name="custom[{{$k}}][body]" id="custom[{{$k}}][body]">{{$val['body']}}</textarea>
								</div>
								@php
									$k++;
								@endphp
							@endforeach
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="form-group row{{ $errors->has('turnaround_time') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Turnaround Time<span class="text-danger">*</span><br/><span class="size_msg">Production Turnaround Time is</span></label>
			<div class="col-sm-6">
				<input id="turnaround_time" type="text" class="form-control" name="turnaround_time" value="{{(array_key_exists('turnaround_time',old()))?old('turnaround_time'):$product->turnaround_time }}" placeholder="Enter Turnaround Time" />
				@if ($errors->has('turnaround_time'))
					<span class="help-block">{{ $errors->first('turnaround_time') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('no_artwork_required') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">No Artwork Required</label>
			<div class="col-sm-4" id="">
				@php 
				$checked = '';
				if(array_key_exists('no_artwork_required',old())){
					if(old('no_artwork_required') == 1){	$checked = 'checked'; }
				}else{	if($product->no_artwork_required == 1){ $checked = 'checked';} }
				@endphp
				<input type="checkbox" name="no_artwork_required" id="no_artwork_required" value="1" class="flat-red" {{$checked}} />
			</div>
		</div>
		<div class="form-group row{{ $errors->has('meta_title') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Meta Title</label>
			<div class="col-sm-6">
				<input id="meta_title" type="text" class="form-control" name="meta_title" value="{{(old('meta_title'))?old('meta_title'):$product->meta_title}}" placeholder="Enter Meta Title" />
				@if ($errors->has('meta_title'))
					<span class="help-block">{{ $errors->first('meta_title') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('meta_tag') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Meta Keywords</label>
			<div class="col-sm-6">
				<input id="meta_tag" type="text" class="form-control" name="meta_tag" value="{{(old('meta_tag'))?old('meta_tag'):$product->meta_tag}}" placeholder="Enter Meta Keywords" />
				@if ($errors->has('meta_tag'))
					<span class="help-block">{{ $errors->first('meta_tag') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('meta_description') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Meta Description</label>
			<div class="col-sm-6">
				<textarea class="form-control" name="meta_description" id="meta_description" value="{{ old('meta_description') }}" placeholder="Meta Description">{{(old('meta_description'))?old('meta_description'):$product->meta_description}}</textarea>
				@if ($errors->has('meta_description'))
					<span class="help-block">{{ $errors->first('meta_description') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('show_width_height') ? ' has-error' : '' }} width_height_div">
			<label class="col-sm-3 form-control-label">Show Width/Height</label>
			<div class="col-sm-4" id="paid_free_div">
				@php 
				$checked = '';
				if(array_key_exists('show_width_height',old())){
					if(old('show_width_height') == 1){	$checked = 'checked'; }
				}else{	if($product->show_width_height == 1){ $checked = 'checked';} }
				@endphp
				<input type="checkbox" name="show_width_height" id="show_width_height" value="1" class="flat-red" {{$checked}} />
			</div>
		</div>
		<div class="form-group row min_width_height {{($checked == 'checked')?'':'hide'}} {{($errors->has('min_width') or $errors->has('min_height'))?'has-error':''}}">
			<label class="col-sm-3 form-control-label label_price_1000">Min Width/Height</label>
			<div class="col-sm-9">
				<div class="col-sm-6 no-padding">
					<label class="col-sm-4 control-label no-padding">Min Width</label>
					<div class="col-sm-8">
						@php 
						$value = '';
						if(array_key_exists('min_width',old())){
							$value = old('min_width');
						}else{	if($product->min_width != ''){ $value = $product->min_width;}else{ $value = 1 ;} }
						@endphp
						<input id="min_width" type="number" min="0" step="any" class="form-control price" name="min_width" value="{{$value}}" placeholder="Enter Width" />
						@if ($errors->has('min_width'))
							<span class="help-block">{{ $errors->first('min_width') }}</span>
						@endif
					</div>
				</div>
				<div class="col-sm-6">
					<label class="col-sm-4 control-label">Min Height</label>
					<div class="col-sm-8">
						@php 
						$value = '';
						if(array_key_exists('min_height',old())){
							$value = old('min_height');
						}else{	if($product->min_height != ''){ $value = $product->min_height;}else{ $value = 1 ;} }
						@endphp
						<input id="min_height" type="number" min="0" step="any" class="form-control price" name="min_height" value="{{$value}}" placeholder="Enter Height" />
						@if ($errors->has('min_height'))
							<span class="help-block">{{ $errors->first('min_height') }}</span>
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="form-group row max_width_height {{($checked == 'checked')?'':'hide'}} {{($errors->has('max_width') or $errors->has('max_height'))?'has-error':''}}">
			<label class="col-sm-3 form-control-label label_price_1000">Max Width/Height</label>
			<div class="col-sm-9">
				<div class="col-sm-6 no-padding">
					<label class="col-sm-4 control-label no-padding">Max Width</label>
					<div class="col-sm-8">
						<input id="max_width" type="number" min="0" step="any" class="form-control price" name="max_width" value="{{ (array_key_exists('max_width',old()))?old('max_width'):$product->max_width }}" placeholder="Enter Width" />
						@if ($errors->has('max_width'))
							<span class="help-block">{{ $errors->first('max_width') }}</span>
						@endif
					</div>
				</div>
				<div class="col-sm-6">
					<label class="col-sm-4 control-label">Max Height</label>
					<div class="col-sm-8">
						<input id="max_height" type="number" min="0" step="any" class="form-control price" name="max_height" value="{{ (array_key_exists('max_height',old()))?old('max_height'):$product->max_height }}" placeholder="Enter Height" />
						@if ($errors->has('max_height'))
							<span class="help-block">{{ $errors->first('max_height') }}</span>
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="form-group row{{ $errors->has('double_side_print') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Double Side Print</label>
			<div class="col-sm-4">
				@php 
				$checked = '';
				if(array_key_exists('double_side_print',old())){
					if(old('double_side_print') == 1){	$checked = 'checked'; }
				}else{	if($product->double_side_print == 1){ $checked = 'checked';} }
				@endphp
				<input type="checkbox" name="double_side_print" id="double_side_print" value="1" class="flat-red" {{$checked}} />
			</div>
		</div>
		@php 
		$checked = '';
		if(array_key_exists('show_width_height',old())){
			if(old('show_width_height') == 1){	$checked = 'checked'; }
		}else{	if($product->show_width_height == 1){ $checked = 'checked';} }
		@endphp
		<div class="form-group row max_width_height {{($checked == 'checked')?'':'hide'}} {{($errors->has('min_sqft') or $errors->has('min_sqft'))?'has-error':''}}">
			<label class="col-sm-3 form-control-label label_price_1000">Min SQFT</label>
			<div class="col-sm-3">
				<input id="min_sqft" type="number" min="0" step="any" class="form-control price" name="min_sqft" value="{{ (array_key_exists('min_sqft',old()))?old('min_sqft'):$product->min_sqft }}" placeholder="Enter Min SQFT" />
				@if ($errors->has('min_sqft'))
					<span class="help-block">{{ $errors->first('min_sqft') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row{{ $errors->has('shipping_weight') ? ' has-error' : '' }}">
			<label class="col-sm-3 form-control-label">Product Weight<br/><span class="size_msg">Per Item/Per SQFT(In LBS)</span></label>
			<div class="col-sm-6">
				<input id="shipping_weight" type="text" class="form-control" name="shipping_weight" value="{{ (array_key_exists('shipping_weight',old()))?old('shipping_weight'):$product->shipping_weight }}" placeholder="Enter Shipping Weight" />
				@if ($errors->has('shipping_weight'))
					<span class="help-block">{{ $errors->first('shipping_weight') }}</span>
				@endif
			</div>
		</div>
		<div class="line"></div>
		<div class="form-group row">
			<label class="col-sm-3 form-control-label">&nbsp;</label>
			<div class="col-sm-4 offset-sm-2">
				<button type="submit" class="btn btn-primary">Update</button>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	//alert('{{count($product->Images)}}')
	$("#name").blur(function(){
		var val = $(this).val();		
		val =  val.toLowerCase();
		val =  val.replace(/ /g,"-");
		val =  val.replace(/[^A-Za-z0-9^_\-]/g, "");
		
		$('#slug').val(val);
		
	});
	
	$('.width_height_div ins').on('click',function(){
		var value = $(this).siblings('input');
		if (value.is(':checked')) {
			$('.min_width_height').removeClass('hide');
			$('.min_width_height').slideDown();
			$('.max_width_height').removeClass('hide');
			$('.max_width_height').slideDown();
		} else {
			$('.min_width_height').slideUp();
			$('.max_width_height').slideUp();
		}
	});
	
	$('.editors').each(function(){
		init_ckeditor($(this).attr('id'));
	});

	function init_ckeditor(element){
		CKEDITOR.replace( element, {
			filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
			height: '300',
		});
	}
	$(function() {
		// Multiple images preview in browser
		 var imagesPreview = function(input,main_div, remove_div, placeToInsertImagePreview,input_name) {

			if (input.files) {
				var filesAmount = input.files.length;
				for (i = 0; i < filesAmount; i++) {
					var reader = new FileReader();
					var tarr = input.files[i].name.split('/');
					var file = tarr[tarr.length-1];
					var data = file.split('.');
					var data = data[data.length-1];
					//alert(data);
					var noError= true;
					if ($.inArray(data, ['jpg', 'jpeg','png','gif']) == -1) {
						noError = false;
						$('#'+main_div).addClass('has-error');
						$('#'+main_div+' span').html('Please Select Only(gif, png, jpg, jpeg)');
						$("#"+input_name).val(null);
						$('div#'+remove_div).addClass('hide');
						return false;
					}
					
					if(noError){
						var j = Number('{{count($product->Images)}}')+1;
						reader.onload = function(event) {
							$('#'+main_div+' span').html('');
							if(placeToInsertImagePreview == 'div#images'){
								//alert(j);
								var clone = '<div class="col-xs-6 col-sm-4 col-md-3"><div class="image_main_box"><label for=""><div class="square"><img class="img-responsive" src="'+event.target.result+'" alt="Photo"></div></label><label class="col-sm-6 col-xs-12 pull-left no-padding">Weight : </label> &nbsp;<input type="number" name="new_product_images_weight[]" value="'+j+'" class="col-sm-5 col-xs-12 no-padding"/></div></div>';
								j++;
							}else{
								var clone = '<div class="col-sm-9 image_main_box"><label><img class="img-responsive" src="'+event.target.result+'" alt="Photo"></label></div>';
							}
							//alert(clone);
							$(placeToInsertImagePreview).append(clone);
							$('div#'+remove_div).removeClass('hide');
						}
						reader.readAsDataURL(input.files[i]);
					}
				}
			}

		};
		
		$('#cat_image').on('change', function() {
			$('div#cat_images_div').html('');
			imagesPreview(this,'cat_page_image_div','cat_image_div', 'div#cat_images_div','cat_image');
		});
		
		$('#image').on('change', function() {
			$('div#product_images_div').html('');
			imagesPreview(this,'image_main_div','image_div', 'div#product_images_div','cat_image');
		});
		
		$('#product_image').on('change', function() {
			$('div#images').html('');
			imagesPreview(this,'product_image_div','images_div', 'div#images','product_image');
		});
		
	});

	$('#meta_tag').tokenfield({
	  autocomplete: {
		source: [],
		delay: 100
	  },
	  showAutocompleteOnFocus: true,
	  createTokensOnBlur: true,
	});
	
	var tab_count = Number('{{$k}}');
	
	$('.add_tab').click(function(){
		var str = '<li class="new_li_'+tab_count+'"><a class="pull-left" href="#new_tab'+tab_count+'" data-toggle="tab">New Tab '+tab_count+'</a><i class="fa fa-window-close delete_tab pull-left" aria-hidden="true" data="new" count="'+tab_count+'"></i></li>';
		
		var str1 = '<div class="tab-pane" id="new_tab'+tab_count+'">';
		str1 += '<label for="custom['+tab_count+'][title]">Title</label><input class="form-control" placeholder="Enter Title" name="custom['+tab_count+'][title]" type="text" value="" id="custom['+tab_count+'][title]"/>';
		str1 += '<label for="custom['+tab_count+'][body]">Body</label><textarea class="form-control editors" name="custom['+tab_count+'][body]" id="custom_tab_'+tab_count+'"></textarea>';
		str1 += '</div>';
		
		$(str).insertBefore($(this));
		$(str1).insertAfter('#design_template_div');
		
		CKEDITOR.replace( 'custom_tab_'+tab_count, {
			filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
			height: '300',
		});
		
		$('.nav-tabs a[href="#new_tab'+tab_count+'"]').tab('show');
		
		tab_count += 1;
	});
	
	$(document).on('click','.delete_tab',function(){
		if($(this).attr('data') == 'extra'){
			var id = $(this).attr('data-id');
			if(confirm('Are you sure for delete this tab.')){
				$.ajax({
					url:'{{url("admin/products/deleteTab")}}',
					type:'post',
					dataType:'json',
					data:{'id':id},
					success:function(data){
						if(data.status == 'success'){
							$('li.extra_li_'+id).remove();
							$('#extra_tab_'+id).remove();							
							$('.nav-tabs a[href="#product_detail_div"]').tab('show');
						}
					}
				});
			}
		}else{
			var count = $(this).attr('count');
			if(confirm('Are you sure for delete this tab.')){
				//alert(count);
				$('li.new_li_'+count).remove();
				$('#new_tab'+count).remove();							
				$('.nav-tabs a[href="#product_detail_div"]').tab('show');
			}
		}
	});
});

function delete_image(id){
	//alert(id+" and "+name);
	if(confirm('Are you sure to delete this image.')){
		$.ajax({
			url:'<?php echo url('admin/products/delete_image/'); ?>',
			type:'post',
			data:'id='+id,
			success:function(data){
				if(data == 1){
					$('#image_div_'+id).remove();
				}
			}
		});
	}
}
</script>