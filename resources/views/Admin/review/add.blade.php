@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Add Testimonial</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">	{{Form::model('testimonial_add',['files'=>true,'role'=>'form','url'=>url('admin/testimonial/add')])}}
						<div class="col-sm-12">
							<div id="slider_div" class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Name<span class="text-danger">*</span></label>
								<div class="col-sm-6">	{{Form::text('name',old('name'),['class'=>'form-control','placeholder'=>'Enter Name'])}}
									@if ($errors->has('name'))
										<span class="help-block">{{ $errors->first('name') }}</span>
									@endif
								</div>
							</div>
							<div id="slider_div" class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Designation/Company<span class="text-danger">*</span></label>
								<div class="col-sm-6">	{{Form::text('designation_company',old('designation_company'),['class'=>'form-control','placeholder'=>'Enter Designation/Company'])}}
									@if ($errors->has('designation_company'))
										<span class="help-block">{{ $errors->first('designation_company') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('content') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Content<span class="text-danger">*</span></label>
								<div class="col-sm-9">	{{Form::textarea('content',old('content'),['class'=>'form-control'])}}
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
});
</script>		  
@endsection		  