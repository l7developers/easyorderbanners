@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Site Setting</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">	{{Form::model('settings',['files'=>true,'role'=>'form','url'=>url('admin/settings')])}}
						<div class="col-sm-12">
							<div id="slider_div" class="form-group row{{ $errors->has('header_script') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Header Script</label>
								<div class="col-sm-6">	{{Form::textarea('header_script',old('header_script',$settings['header_script']),['class'=>'form-control','placeholder'=>'Header Script'])}}
									@if ($errors->has('header_script'))
										<span class="help-block">{{ $errors->first('header_script') }}</span>
									@endif
								</div>
							</div>
							<div id="slider_div" class="form-group row{{ $errors->has('footer_script') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Footer Script</label>
								<div class="col-sm-6">	{{Form::textarea('footer_script',old('footer_script',$settings['footer_script']),['class'=>'form-control','placeholder'=>'Footer Script'])}}
									@if ($errors->has('footer_script'))
										<span class="help-block">{{ $errors->first('footer_script') }}</span>
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
@endsection		  