@extends('layouts.admin_layout')
@section('content')

<section class="content-header">
  <h1>Customers Logo Edit</h1>
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
						<div class="form-group row{{ $errors->has('link') ? ' has-error' : '' }}">
							<label class="col-sm-2 form-control-label">Link<span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<input id="link" type="text" class="form-control" name="link" value="{{ (array_key_exists('link',old()))?old('link'):$obj->link }}" placeholder="Enter Link">
								@if ($errors->has('link'))
									<span class="help-block">
										<strong>{{ $errors->first('link') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('image') ? ' has-error' : '' }}" id="customer_image_div">
							<label class="col-sm-2 form-control-label">Image<span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<input id="image" type="file" class="" name="image">
								@if ($errors->has('image'))
									<span class="help-block">
										<strong>{{ $errors->first('image') }}</strong>
									</span>
								@else
									<span class="help-block"></span>
								@endif
							</div>
						</div>
						<div class="form-group row" id="customer_div">
							<label class="col-sm-2 form-control-label">&nbsp;</label>
							<div class="col-sm-6" id="logo_image_div">
								@if(!empty($obj->image))
									<div class="col-sm-4 image_main_box">
										<label>
											<img class="img-responsive" src="{{URL::to('public/uploads/home/customers/'.$obj->image)}}" alt="Photo">
										</label>
									</div>
								@endif
							</div>
						</div>
						<div class="line"></div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label">&nbsp;</label>
							<div class="col-sm-4 offset-sm-2">
								<button type="submit" class="btn btn-primary">Update</button>
								<a href="{{url('admin/home/customers-logo-list')}}" class="btn btn-warning">Back</a>
							</div>
						</div>
					</form>
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
						$('#'+main_div+' .col-sm-6 span').html('Please Select Only(gif, png, jpg, jpeg)');
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
			$('div#logo_image_div').html('');
			imagesPreview(this,'customer_image_div','customer_div', 'div#logo_image_div','image');
		});
	});
</script>
@endsection		  