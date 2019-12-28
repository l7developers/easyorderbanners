@extends('layouts.admin_layout')
@section('content')
<link href="{{ asset('public/css/admin/bootstrap-tokenfield.min.css') }}" rel="stylesheet">
<link href="{{ asset('public/css/admin/tokenfield-typeahead.min.css') }}" rel="stylesheet">
<script src="{{asset('public/js/admin/bootstrap-tokenfield.min.js')}}"> </script>
<section class="content-header">
	<h1>Add Category</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">		
					{{Form::model('cat_add',['files'=>true])}}
						<div class="col-sm-12">
							<div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Name<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Name">
									@if ($errors->has('name'))
										<span class="help-block">{{ $errors->first('name') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('slug') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Slug<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="slug" type="text" class="form-control" name="slug" value="{{ old('slug') }}" placeholder="Slug">
									@if ($errors->has('slug'))
										<span class="help-block">{{ $errors->first('slug') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row">
								{{Form::label('parent_id','Select Parent Category',['class'=>'col-sm-3 form-control-label'])}}
								<div class="col-sm-6">
									{{Form::select('parent_id',[''=>'Select Category']+$categories,old('parent_id'),['class'=>'form-control','id'=>'parent_id'])}}
								</div>
							</div>
							<div class="form-group row{{ $errors->has('image_title') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Category Image Title</label>
								<div class="col-sm-6">
									<input id="image_title" type="text" class="form-control" name="image_title" value="{{ old('image_title') }}" placeholder="Enter Image Title" />
									@if ($errors->has('image_title'))
										<span class="help-block">{{ $errors->first('image_title') }}</span>
									@endif
								</div>
							</div>
							<div id="cat_image_div" class="form-group row{{ $errors->has('image') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Category Image<br/><span class="size_msg">Image size(385X200)</span></label>
								<div class="col-sm-6">
									<input id="image" type="file" class="" name="image"/>
									@if ($errors->has('image'))
										<span class="help-block">{{ $errors->first('image') }}</span>
									@else
										<span class="help-block"></span>
									@endif
								</div>
							</div>
							<div class="form-group row hide" id="cat_div">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-9" id="cat_images_div"></div>
							</div>
							<div class="form-group row{{ $errors->has('excerpt') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Excerpt<span class="text-danger">*</span></label>
								<div class="col-sm-8">
									<textarea id="excerpt" type="text" class="form-control" name="excerpt" placeholder="Excerpt">{{ old('excerpt') }}</textarea>
									@if ($errors->has('excerpt'))
										<span class="help-block">{{ $errors->first('excerpt') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('description') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Description<span class="text-danger">*</span></label>
								<div class="col-sm-8">
									<textarea id="description" type="text" class="form-control" name="description" placeholder="Description">{{ old('description') }}</textarea>
									@if ($errors->has('description'))
										<span class="help-block">{{ $errors->first('description') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('meta_title') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Meta Title</label>
								<div class="col-sm-6">
									<input id="meta_title" type="text" class="form-control" name="meta_title" value="{{ old('meta_title') }}" placeholder="Enter Meta Title" />
									@if ($errors->has('meta_title'))
										<span class="help-block">{{ $errors->first('meta_title') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('meta_tag') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Meta Keywords</label>
								<div class="col-sm-6">
									<input id="meta_tag" type="text" class="form-control" name="meta_tag" value="{{ old('meta_tag') }}" placeholder="Enter Meta Keywords" />
									@if ($errors->has('meta_tag'))
										<span class="help-block">{{ $errors->first('meta_tag') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('meta_description') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Meta Description</label>
								<div class="col-sm-6">
									<textarea id="meta_description" type="text" class="form-control" name="meta_description" placeholder="Meta Description">{{ old('meta_description') }}</textarea>
									@if ($errors->has('meta_description'))
										<span class="help-block">{{ $errors->first('meta_description') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-8 offset-sm-2">
									<button type="submit" class="btn btn-primary">Save</button>
								</div>
							</div>
						</div>
					{{Form::close()}}
				</div>
			</div>
		</div>
	</div>
</section>	
<script type="text/javascript">
$(document).ready(function(){
	$("#name").blur(function(){
		var val = $(this).val();		
		val =  val.toLowerCase();
		val =  val.replace(/ /g,"-");
		val =  val.replace(/[^A-Za-z0-9^_\-]/g, "");		
		$('#slug').val(val);		
	});
	
	CKEDITOR.replace( 'description', {
		filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
	//toolbar: [[ 'Bold', 'Italic','Underline','Subscript','Superscript'],],
	/* toolbar: [				
			{ name: 'basicstyles', items: [ 'Bold', 'Italic',] },
			{ name: 'styles', items: [ 'Font', 'FontSize' ] }
		], */
	//width: '900',
	height: '300',
	});

	$('#meta_tag').tokenfield({
	  autocomplete: {
		source: [],
		delay: 100
	  },
	  showAutocompleteOnFocus: true,
	  createTokensOnBlur: true,
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
		$('#image').on('change', function() {
			$('div#cat_images_div').html('');
			imagesPreview(this,'cat_image_div','cat_div', 'div#cat_images_div','image');
		});
	});

});	
</script>
@endsection		  