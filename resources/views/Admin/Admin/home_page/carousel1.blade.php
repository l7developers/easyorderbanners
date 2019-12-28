@extends('layouts.admin_layout')
@section('content')

<section class="content-header">
  <h1>Product Carousel-1 Detail</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">
					<form role="form" method="POST" enctype="multipart/form-data">
						{{ csrf_field() }}
						<div class="form-group row{{ $errors->has('title') ? ' has-error' : '' }}">
							<label class="col-sm-2 form-control-label">Title<span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<input id="title" type="text" class="form-control" name="title" value="{{ (array_key_exists('title',old()))?old('title'):$obj->title }}" placeholder="Enter Title">
								@if ($errors->has('title'))
									<span class="help-block">
										<strong>{{ $errors->first('title') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('images') ? ' has-error' : '' }}" id="carousel_image_div">
							<label class="col-sm-2 form-control-label">Images</label>
							<div class="col-sm-6">
								<input id="images" type="file" class="" name="images[]" multiple="true">
								@if ($errors->has('images'))
									<span class="help-block">
										<strong>{{ $errors->first('images') }}</strong>
									</span>
								@else
									<span class="help-block"></span>
								@endif
							</div>
						</div>
						<div class="form-group row hide" id="images_div">
							<label class="col-sm-2 form-control-label">&nbsp;</label>
							<div class="col-sm-10">
								<div class="panel panel-info product_panel">
									<div class="panel-heading">
										<h5>Carousel-1 Images</h5>
									</div>
									<div class="panel-body custom_option_panel" id="">
										<div class="col-sm-12">
											<div class="row" id="images_container"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						@if(count($obj->images) > 0)
						<div class="form-group row{{ $errors->has('product_image') ? ' has-error' : '' }}">
							<div class="col-md-12">
								<div class="col-md-2">&nbsp;</div>
								<div class="box-body col-md-10">
									<div class="panel panel-info product_panel">
										<div class="panel-heading">
											<h5>Carousel-1 Images</h5>
										</div>
										<div class="panel-body" id="">
											<div class="col-sm-12">
												<div class="row">
													
												@foreach($obj->images as $image)
												<?php
												//pr($image);
												?>
												<div class="col-xs-6 col-sm-4 col-md-3" id="image_div_{{$image->id}}">
													<div class="image_main_box">
														<label for="">
															<div class="square">
																<img class="img-responsive" src="{{URL::to('/public/uploads/home/carousel1/'.$image->name)}}" alt="Photo">
															</div>
														</label>
														<label class="col-sm-8 col-xs-12 pull-left no-padding">Disp. Order : </label> &nbsp;<input type="number" name="carousel1_images[{{$image->id}}][weight]" value="{{$image->weight}}" class="col-sm-3 col-xs-12 no-padding" max="{{count($obj->images)}}" min="1"/>
														<div class="clearfix"></div>
														<label class="col-sm-5 col-xs-12 pull-left no-padding">Title : </label> &nbsp;<input type="text" name="carousel1_images[{{$image->id}}][title]" value="{{$image->title}}" class="col-sm-6 col-xs-12 no-padding"/>
														<a class="remove_media delete" href="javascript:void(0)" data-id="{{$image->id}}" data="{{$obj->title.' image'}}" data-image="public/uploads/home/carousel1/{{$image->name}}">Delete</a>
													</div>
												</div>
												@endforeach
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						@endif
						<div class="form-group row{{ $errors->has('description') ? ' has-error' : '' }}">
							<label class="col-sm-2 form-control-label">Description<span class="text-danger">*</span></label>
							<div class="col-sm-10">
								<textarea id="description" class="form-control" name="description">{{(array_key_exists('description',old()))?old('description'):$obj->description}}</textarea>
								@if ($errors->has('description'))
									<span class="help-block">
										<strong>{{ $errors->first('description') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<fieldset>
							<legend>Sub-Section:</legend>
							<div class="form-group row{{ $errors->has('sub_title') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Title<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="sub_title" type="text" class="form-control" name="sub_title" value="{{ (array_key_exists('sub_title',old()))?old('sub_title'):$sub_obj->title }}" placeholder="Enter Title">
									@if ($errors->has('sub_title'))
										<span class="help-block">
											<strong>{{ $errors->first('sub_title') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('sub_image') ? ' has-error' : '' }}" id="carousel_sub_image_div">
								<label class="col-sm-2 form-control-label">Image<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="sub_image" type="file" class="" name="sub_image" multiple="true">
									@if ($errors->has('sub_image'))
										<span class="help-block">
											<strong>{{ $errors->first('sub_image') }}</strong>
										</span>
									@else
										<span class="help-block"></span>
									@endif
								</div>
							</div>
							<div class="form-group row" id="carousel_div">
								<label class="col-sm-2 form-control-label">&nbsp;</label>
								<div class="col-sm-6" id="logo_image_div">
								@if(!empty($sub_obj->img))
									<div class="col-sm-4 image_main_box">
										<label>
											<img class="img-responsive" src="{{URL::to('public/uploads/home/carousel1/'.$sub_obj->img)}}" alt="Photo">
										</label>
									</div>
								@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('sub_description') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Description<span class="text-danger">*</span></label>
								<div class="col-sm-10">
									<textarea id="sub_description" class="form-control" name="sub_description">{{(array_key_exists('sub_description',old()))?old('sub_description'):$sub_obj->description}}</textarea>
									@if ($errors->has('sub_description'))
										<span class="help-block">
											<strong>{{ $errors->first('sub_description') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</fieldset>
						<div class="line"></div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label">&nbsp;</label>
							<div class="col-sm-4 offset-sm-2">
								<button type="submit" class="btn btn-primary">Update</button>
							</div>
						</div>
					</form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
	CKEDITOR.replace( 'description', {
		filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
		height: '300',
	});
	
	CKEDITOR.replace( 'sub_description', {
		filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
		height: '300',
	});
			
	$('.delete').click(function(index,value){
		var id = $(this).attr('data-id');
		var image = $(this).attr('data-image');
		var str = $(this).attr('data');
		str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
			return letter.toUpperCase();
		});
		if(confirm("You are about to delete "+str+". Are you sure?")){
			$.ajax({
				url:'{{url("admin/actions/delete")}}',
				type:'post',
				dataType:'json',
				data:{'table':'homeimages','id':id,'image_unlink':'true','image':image},
				beforeSend: function () {
				  $.blockUI();
				},
				complete: function () {
				  $.unblockUI();
				},
				success:function(data){
					if(data.status == 'success'){
						$('#image_div_'+id).remove();
					}
				}
			});
		}
	});
	
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
						var j = Number('{{count($obj->images)}}')+1;
						reader.onload = function(event) {
							$('#'+main_div+' span').html('');
							if(placeToInsertImagePreview == 'div#images_container'){
								//alert(j);
								var clone = '<div class="col-xs-6 col-sm-4 col-md-3"><div class="image_main_box"><label for=""><div class="square"><img class="img-responsive" src="'+event.target.result+'" alt="Photo"></div></label><label class="col-sm-6 col-xs-12 pull-left no-padding">Disp.Order : </label> &nbsp;<input type="number" name="carousel_images_weight[]" value="'+j+'" class="col-sm-3 col-xs-12 no-padding"/><div class="clearfix"></div><label class="col-sm-5 col-xs-12 pull-left no-padding">Title : </label> &nbsp;<input type="text" name="carousel_images_title[]" value="" placeholder="Enter Title" class="col-sm-5 col-xs-12 no-padding"/></div></div>';
								j++;
							}else{
								var clone = '<div class="col-sm-4 image_main_box"><label><img class="img-responsive" src="'+event.target.result+'" alt="Photo"></label></div>';
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
		
		$('#images').on('change', function() {
			$('div#images_container').html('');
			imagesPreview(this,'carousel_image_div','images_div', 'div#images_container','images');
		});
		
		$('#sub_image').on('change', function() {
			$('div#logo_image_div').html('');
			imagesPreview(this,'carousel_sub_image_div','carousel_div', 'div#logo_image_div','sub_image');
		});
	});
</script>
@endsection		  