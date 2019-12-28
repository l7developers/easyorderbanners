@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Edit testimonial</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">
					{{Form::model('slider_add',['files'=>true,'role'=>'form'])}}
						<div class="col-sm-12">
							<div id="slider_div" class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Name<span class="text-danger">*</span></label>
								<div class="col-sm-6">	{{Form::text('name',(array_key_exists('name',old()))?old('name'):$testimonial->name,['class'=>'form-control','placeholder'=>'Enter Name'])}}
									@if ($errors->has('name'))
										<span class="help-block">{{ $errors->first('name') }}</span>
									@endif
								</div>
							</div>
							<div id="slider_div" class="form-group row{{ $errors->has('designation_company') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Designation/Company</label>
								<div class="col-sm-6">	{{Form::text('designation_company',(array_key_exists('designation_company',old()))?old('designation_company'):$testimonial->designation_company,['class'=>'form-control','placeholder'=>'Enter Designation/Company'])}}
									@if ($errors->has('designation_company'))
										<span class="help-block">{{ $errors->first('designation_company') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('content') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Content<span class="text-danger">*</span></label>
								<div class="col-sm-9">	{{Form::textarea('content',(array_key_exists('content',old()))?old('content'):$testimonial->content,['class'=>'form-control'])}}
									@if ($errors->has('content'))
										<span class="help-block">{{ $errors->first('content') }}</span>
									@endif
								</div>
							</div>
							<div class="line"></div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-6 offset-sm-2">
								<button type="submit" class="btn btn-primary">Save</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
$(document).ready(function(){
	CKEDITOR.replace( 'content', {
			filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
			height: '300',
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
			$('div#slider_view').html('');
			imagesPreview(this,'slider_div','slider_view_div', 'div#slider_view','image');
		});
	});
});
</script>		  
@endsection		  