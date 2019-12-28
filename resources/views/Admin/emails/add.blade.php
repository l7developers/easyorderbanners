@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>Email Add</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">
                  <form role="form" method="POST" action="{{ url('admin/emails/add') }}">
					{{ csrf_field() }}
                    <div class="form-group row{{ $errors->has('slug_name') ? ' has-error' : '' }}">
                      <label class="col-sm-2 form-control-label">Slug Name</label>
                      <div class="col-sm-10">
                        <input id="slug_name" type="text" class="form-control" name="slug_name" value="{{ old('slug_name') }}">
						@if ($errors->has('slug_name'))
							<span class="help-block">{{ $errors->first('slug_name') }}</span>
						@endif
                      </div>
                    </div>
					<div class="form-group row{{ $errors->has('subject') ? ' has-error' : '' }}">
                      <label class="col-sm-2 form-control-label">Subject</label>
                      <div class="col-sm-10">
                       <input id="subject" type="text" class="form-control" name="subject" value="{{ old('subject') }}">
						@if ($errors->has('subject'))
							<span class="help-block">{{ $errors->first('subject') }}</span>
						@endif
                      </div>
                    </div>
					<div class="form-group row{{ $errors->has('message') ? ' has-error' : '' }}">
                      <label class="col-sm-2 form-control-label">Message</label>
                      <div class="col-sm-10">
                        <textarea id="message" class="form-control" name="message" value="{{ old('message') }}" ></textarea>
						@if ($errors->has('message'))
							<span class="help-block">{{ $errors->first('message') }}</span>
						@endif
                      </div>
                    </div>
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
    CKEDITOR.replace( 'message', {
		filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
    //toolbar: [[ 'Bold', 'Italic','Underline','Subscript','Superscript'],],
    //width: '900',
    height: '300',
    });
</script>	  
@endsection		  