@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Edit Slider</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">
					{{Form::model('slider_add',['files'=>true,'role'=>'form','url'=>url('admin/slider/edit/'.$id)])}}
						<div class="col-sm-12">
							<div id="slider_div" class="form-group row{{ $errors->has('image') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Image<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::file('image')}}
									@if ($errors->has('image'))
										<span class="help-block">{{ $errors->first('image') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row" id="slider_view_div">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-9" id="slider_view">	@if(@getimagesize(url('/public/uploads/slider/'.$slider->image)))
									<div class="col-sm-4 image_main_box">
										<label>	
											<img class="img-responsive" src="{{URL::to('/public/uploads/slider/'.$slider->image)}}" alt="Photo">
										</label>
									</div>
								@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('content_direction') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Content Direction<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::select('content_direction',[''=>'Select Content Direction','1'=>'Left','2'=>'Right'],(array_key_exists('content_direction',old()))?old('content_direction'):$slider->content_direction,['class'=>'form-control'])}}
									@if ($errors->has('content_direction'))
										<span class="help-block">{{ $errors->first('content_direction') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('content') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Content<span class="text-danger">*</span></label>
								<div class="col-sm-9">	{{Form::textarea('content',(array_key_exists('content',old()))?old('content'):$slider->content,['class'=>'form-control'])}}
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