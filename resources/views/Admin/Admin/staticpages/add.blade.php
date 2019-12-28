@extends('layouts.admin_layout')
@section('content')
<link href="{{ asset('public/css/admin/bootstrap-tokenfield.min.css') }}" rel="stylesheet">
<link href="{{ asset('public/css/admin/tokenfield-typeahead.min.css') }}" rel="stylesheet">
<script src="{{asset('public/js/admin/bootstrap-tokenfield.min.js')}}"> </script>
<section class="content-header">
  <h1>Content Add</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">
                  <form role="form" method="POST" action="{{ url('admin/staticpages/add') }}">
					{{ csrf_field() }}
                    <div class="form-group row{{ $errors->has('title') ? ' has-error' : '' }}">
                      <label class="col-sm-2 form-control-label">Title<span class="text-danger">*</span></label>
                      <div class="col-sm-6">
                       <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" placeholder="Enter Title">
						@if ($errors->has('title'))
							<span class="help-block">
								<strong>{{ $errors->first('title') }}</strong>
							</span>
						@endif
                      </div>
                    </div>
					<div class="form-group row{{ $errors->has('slug') ? ' has-error' : '' }}">
                      <label class="col-sm-2 form-control-label">Slug<span class="text-danger">*</span></label>
                      <div class="col-sm-6">
                        <input id="slug" type="text" class="form-control" name="slug" value="{{ old('slug') }}" placeholder="Enter Slug">
						@if ($errors->has('slug'))
							<span class="help-block">
								<strong>{{ $errors->first('slug') }}</strong>
							</span>
						@endif
                      </div>
                    </div>
					<div class="form-group row{{ $errors->has('page_type') ? ' has-error' : '' }}">
						<label class="col-sm-2 form-control-label">Selecte Template<span class="text-danger">*</span></label>
						<div class="col-sm-6">
							{{Form::select('page_type',[''=>'Select Type','full_width'=>'Page With Full Width','sidebar'=>'Page With Side Bar'],old('page_type'),['class'=>'form-control','id'=>'page_type'])}}
							@if ($errors->has('page_type'))
								<span class="help-block">
									<strong>{{ $errors->first('page_type') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group row{{ $errors->has('body') ? ' has-error' : '' }}">
						<label class="col-sm-2 form-control-label">Body<span class="text-danger">*</span></label>
						<div class="col-sm-10">
							<textarea id="body" class="form-control" name="body" value="{{ old('body') }}"></textarea>
							@if ($errors->has('body'))
								<span class="help-block">
									<strong>{{ $errors->first('body') }}</strong>
								</span>
							@endif
						</div>
                    </div>
					<div class="form-group row{{ $errors->has('testimonials') ? ' has-error' : '' }}">
						<label class="col-sm-2 form-control-label">Testimonials</label>
						<div class="col-sm-6">	{{Form::select('testimonials',$testimonials,old('testimonials'),['class'=>'form-control','placeholder'=>'Select testimonials'])}}
						</div>
					</div>
					<fieldset>
						<legend>Meta Details:</legend>
						<div class="form-group row{{ $errors->has('meta_title') ? ' has-error' : '' }}">
							<label class="col-sm-2 form-control-label">Meta Title</label>
							<div class="col-sm-6">
								<input id="meta_title" type="text" class="form-control" name="meta_title" value="{{ old('meta_title') }}" placeholder="Enter Meta Title" />
								@if ($errors->has('meta_title'))
									<span class="help-block">{{ $errors->first('meta_title') }}</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('meta_description') ? ' has-error' : '' }}">
							<label class="col-sm-2 form-control-label">Meta Description</label>
							<div class="col-sm-6">
								<textarea class="form-control" name="meta_description" id="meta_description" value="{{ old('meta_description') }}" placeholder="Meta Description">{{(old('meta_description'))?old('meta_description'):''}}</textarea>
								@if ($errors->has('meta_description'))
									<span class="help-block">{{ $errors->first('meta_description') }}</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('meta_tag') ? ' has-error' : '' }}">
							<label class="col-sm-2 form-control-label">Meta Keywords</label>
							<div class="col-sm-6">
								<input id="meta_tag" type="text" class="form-control" name="meta_tag" value="{{ old('meta_tag') }}" placeholder="Enter Meta Keywords" />
								@if ($errors->has('meta_tag'))
									<span class="help-block">{{ $errors->first('meta_tag') }}</span>
								@endif
							</div>
						</div>
					</fieldset>
                    <div class="line"></div>
                    <div class="form-group row">
						 <label class="col-sm-2 form-control-label">&nbsp;</label>
						  <div class="col-sm-4 offset-sm-2">
							<button type="submit" class="btn btn-primary">Add</button>
						  </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
      </section>

	  
<script type="text/javascript">
	$("#title").blur(function(){
		var val = $(this).val();		
		val =  val.toLowerCase();
		val =  val.replace(/ /g,"-");
		val =  val.replace(/[^A-Za-z0-9^_\-]/g, "");
		
		$('#slug').val(val);
		
	});
	
    CKEDITOR.replace( 'body', {
		filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
    //toolbar: [[ 'Bold', 'Italic','Underline','Subscript','Superscript'],],
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
	
</script>	  
@endsection		  