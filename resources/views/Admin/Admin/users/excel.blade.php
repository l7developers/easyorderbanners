@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>Upload Product Excel</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				@if (count($errors) > 0)
					@foreach ($errors->all() as $error)
						<div class="box-header">
						<div class="col-lg-12 bg-danger">
							<h4 class="errorMsg">{{$error}}</h4>
						</div>
					</div>
					@endforeach
				@endif
				<div class="box-body">
					{!! Form::open(array('method'=>'POST','files'=>'true')) !!}
						<div class="col-sm-12" style="margin-top:20px;">
							<div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
								{{Form::label('upload_excel', 'Upload Excel',['class'=>'col-sm-2 form-control-label requiredAsterisk'])}}
								<div class="col-sm-4">{{Form::file('upload_excel',null,['class'=>'form-control','data-validation'=>'required'])}}
									@if ($errors->has('upload_excel'))
										<span class="help-block">{{ $errors->first('upload_excel') }}</span>
									@endif
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-2 form-control-label">&nbsp;</label>
								<div class="col-sm-8 offset-sm-2">
									{{Form::submit('Upload',['class'=>'btn btn-primary khine_btn'])}}
									{{link_to(url('admin/users/lists'), 'Back to list', ['class'=>'btn btn-warning'])}}
								</div>
							</div>
						</div>
					{!!Form::close()!!}
				</div>
			</div>
		</div>
	</div>
</section>  
@endsection		  