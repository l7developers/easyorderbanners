@extends('layouts.admin_layout')
@section('content')
<link href="{{ asset('public/css/front/5star.css') }}" rel="stylesheet">

<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Edit Review</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/reviews/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
			</div>
		</div>
	</div>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">
					{{Form::model('review',['role'=>'form','class'=>'contactpage','id'=>'review'])}}				

					{{Form::label('rating','Rating',array('class'=>''))}} 
					<div class="form-group{{ $errors->has('OldPassword') ? ' has-error' : '' }}">
						<ul class="rate-area" style="padding: 0px">
							<input type="radio" id="5-star" name="rating" value="5" {{($reviews->rating==5)?'checked':''}}/><label for="5-star" title="Amazing">5 stars</label>
							<input type="radio" id="4-star" name="rating" value="4" {{($reviews->rating==4)?'checked':''}}/><label for="4-star" title="Good">4 stars</label>
							<input type="radio" id="3-star" name="rating" value="3" {{($reviews->rating==3)?'checked':''}}/><label for="3-star" title="Average">3 stars</label>
							<input type="radio" id="2-star" name="rating" value="2" {{($reviews->rating==2)?'checked':''}}/><label for="2-star" title="Not Good">2 stars</label>
							<input type="radio" id="1-star" name="rating" value="1" {{($reviews->rating==1)?'checked':''}}/><label for="1-star" title="Bad">1 star</label>
						</ul>
						@if ($errors->has('rating'))
							<div class="clearfix"></div>
							<span class="help-block">{{ $errors->first('rating') }}</span>
						@endif
					</div>
					<div class="clearfix"></div>
					
					<div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
						{{Form::label('comment','Comment',array('class'=>''))}} {{Form::textarea('comment',$reviews->comment,['class'=>'form-control','placeholder'=>'Comment'])}}
						@if ($errors->has('comment'))
							<span class="help-block">{{ $errors->first('comment') }}</span>
						@endif
					</div>

					<button type="submit" class="btn btn-primary">Save</button>
					{{Form::close()}}					
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