@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Edit Menu</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">	
					{{ Form::model('menu',['url'=>'admin/menu/edit/'.$id,'files'=>true]) }}
						<div class="col-sm-12">
							<div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Name<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::text('name', array_key_exists('name',old()) ? old('name') : $menu->name ,['class'=>'form-control','placeholder'=>'Enter Name','id'=>'name'])}}
									@if ($errors->has('name'))
										<span class="help-block">{{ $errors->first('name') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('type') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Select Type<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::select('type', [''=>'Select type','page'=>'Page','category'=>'Category','product'=>'Product','static'=>'Static URL'], array_key_exists('type',old()) ? old('type') : $menu->type,array('class'=>'form-control','id'=>'type','required'))}}
									@if ($errors->has('type'))
										<span class="help-block">{{ $errors->first('type') }}</span>
									@endif
								</div>
							</div>
							@php
							$page_class = 'hide';
							$page_value = '';
							$category_class = 'hide';
							$category_value = '';
							$product_class = 'hide';
							$product_value = '';
							$static_class = 'hide';
							$static_value = '';
							if(array_key_exists('type',old())){
								if(old('type') == 'page'){
									$page_class = '';
									$page_value = old('page');
								}
								else if(old('type') == 'category'){
									$category_class = '';
									$category_value = old('category');
								}
								else if(old('type') == 'product'){
									$product_class = '';
									$product_value = old('product');
								}
								else if(old('type') == 'static'){
									$static_class = '';
									$static_value = old('static');
								}
							}
							else{
								if($menu->type == 'page'){
									$page_class = '';
									$page_value = $menu->page_id;
								}
								else if($menu->type == 'category'){
									$category_class = '';
									$category_value = $menu->category_id;
								}
								else if($menu->type == 'product'){
									$product_class = '';
									$product_value = $menu->product_id;
								}
								else if($menu->type == 'static'){
									$static_class = '';
									$static_value = $menu->static;
								}
								
							}
							@endphp
							<div class="form-group row cat_div {{$category_class}} {{ $errors->has('category') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Select Category<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{ Form::select('category', [''=>'Select Category']+$categories , $category_value , array('class'=>'form-control','id'=>'category'))}}
									@if ($errors->has('category'))
										<span class="help-block">{{ $errors->first('category') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row product_div {{$product_class}} {{ $errors->has('product') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Select Product<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::select('product', [''=>'Select Product']+$products, $product_value,array('class'=>'form-control','id'=>'product'))}}
									@if ($errors->has('product'))
										<span class="help-block">{{ $errors->first('product') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row page_div {{$page_class}} {{ $errors->has('page') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Select Page<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::select('page', [''=>'Select Page']+$pages, $page_value,array('class'=>'form-control','id'=>'page'))}}
									@if ($errors->has('page'))
										<span class="help-block">{{ $errors->first('page') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row static_div {{$static_class}} {{ $errors->has('static') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Static URL<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::text('static', $static_value ,['class'=>'form-control','placeholder'=>'Enter Static URL','id'=>'static'])}}
									@if ($errors->has('static'))
										<span class="help-block">{{ $errors->first('static') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('menu_name') ? ' has-error' : '' }}">
								{{ Form::label('menu_parent', 'Select Menu',['class'=>'col-sm-3 form-control-label'])}}
								<div class="col-sm-6">
									{{Form::select('menu_parent',[''=>'Select Parent']+ $menus, array_key_exists('menu_parent',old())?old('menu_parent'):$menu->parent_id,array('class'=>'form-control','id'=>'menu_parent'))}}
									@if ($errors->has('menu_parent'))
										<span class="help-block">{{ $errors->first('menu_parent') }}</span>
									@endif
								</div>
							</div>
							<div id="menu_image_div" class="form-group row{{ $errors->has('new_menu_image') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Menu Image</label>
								<div class="col-sm-6">
									<input id="new_menu_image" type="file" class="" name="new_menu_image"/>
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group row" id="menu_image_view_div">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-9" id="menu_image_view">
									@if(!empty($menu->image_name) and $menu->image_name != '')
									<div class="col-sm-4 image_main_box"><label>
										<img class="img-responsive" src="{{URL::to('/public/uploads/menu/'.$menu->image_name)}}" alt="Photo">
										</label>
									</div>
									@endif
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-8 offset-sm-2">
									{{ Form::submit('Update',['class'=>'btn btn-primary']) }}
									{{ link_to(url('/admin/menu/lists'), 'Back', $attributes = array('class'=>'btn btn-warning'), $secure = null) }}
									
								</div>
							</div>
						</div>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
</section>	
<script>
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
					reader.onload = function(event) {
						$('#'+main_div+' span').html('');
						var clone = '<div class="col-sm-4 image_main_box"><label><img class="img-responsive" src="'+event.target.result+'" alt="Photo"></label></div>';
						//alert(clone);
						$(placeToInsertImagePreview).append(clone);
						$('div#'+remove_div).removeClass('hide');
					}
					reader.readAsDataURL(input.files[i]);
				}
            }
        }

    };

    $('#new_menu_image').on('change', function() {
		$('div#menu_image_view').html('');
        imagesPreview(this,'menu_image_div','menu_image_view_div', 'div#menu_image_view','new_menu_image');
    });
});

	$(document).on('change','#type',function(){
		if($(this).val() == 'page'){
			$('.page_div').removeClass('hide');
			$('#page').prop('required',true);
			$('#static').prop('required',false);
			$('#category').prop('required',false);
			$('#product').prop('required',false);
			$('.static_div').addClass('hide');
			$('.cat_div').addClass('hide');
			$('.product_div').addClass('hide');
		}
		else if($(this).val() == 'category'){
			$('.cat_div').removeClass('hide');
			$('#category').prop('required',true);
			$('#product').prop('required',false);
			$('#page').prop('required',false);
			$('#static').prop('required',false);
			$('.page_div').addClass('hide');
			$('.static_div').addClass('hide');
			$('.product_div').addClass('hide');
		}
		else if($(this).val() == 'product'){
			$('.product_div').removeClass('hide');
			$('#product').prop('required',true);
			$('#category').prop('required',false);
			$('#page').prop('required',false);
			$('#static').prop('required',false);
			$('.page_div').addClass('hide');
			$('.static_div').addClass('hide');
			$('.cat_div').addClass('hide');
		}
		else if($(this).val() == 'static'){
			$('.static_div').removeClass('hide');
			$('#static').prop('required',true);
			$('#page').prop('required',false);
			$('#category').prop('required',false);
			$('#product').prop('required',false);
			$('.page_div').addClass('hide');
			$('.cat_div').addClass('hide');
			$('.product_div').addClass('hide');
		}
		else{
			$('.page_div').addClass('hide');
			$('.cat_div').addClass('hide');
			$('.static_div').addClass('hide');
			$('.product_div').addClass('hide');
			$('#static').prop('required',false);
			$('#category').prop('required',false);
			$('#page').prop('required',false);
			$('#product').prop('required',false);
		}
	});
</script>	  
@endsection		  