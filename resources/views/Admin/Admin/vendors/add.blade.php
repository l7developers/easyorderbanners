@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Add Vendor</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">		
					<form role="form" method="POST" action="{{ url('admin/vendors/add') }}">
					{{ csrf_field() }}
						<div class="col-sm-12">
							<div class="form-group row{{ $errors->has('company_name') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Company Name</label>
								<div class="col-sm-6">
									<input id="company_name" type="text" class="form-control" name="company_name" value="{{ old('company_name') }}" placeholder="Company Name">
									@if ($errors->has('company_name'))
										<span class="help-block">{{ $errors->first('company_name') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('company_address') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Company Address</label>
								<div class="col-sm-6">
									<textarea id="company_address" class="form-control" name="company_address">{{ old('company_address') }}</textarea>
									@if ($errors->has('company_address'))
										<span class="help-block">{{ $errors->first('company_address') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('fname') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">First Name</label>
								<div class="col-sm-6">
									<input id="fname" type="text" class="form-control" name="fname" value="{{ old('fname') }}" placeholder="First Name">
									@if ($errors->has('fname'))
										<span class="help-block">{{ $errors->first('fname') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('lname') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Last Name</label>
								<div class="col-sm-6">
								<input id="lname" type="text" class="form-control" name="lname" value="{{ old('lname') }}" placeholder="last Name">
									@if ($errors->has('lname'))
										<span class="help-block">{{ $errors->first('lname') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('email') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Email</label>
								<div class="col-sm-6">
									<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">
									@if ($errors->has('email'))
										<span class="help-block">{{ $errors->first('email') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('phone_number') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Phone Number</label>
								<div class="col-sm-6">
									<input id="phone_number" type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}" placeholder="Phone Number">
									@if ($errors->has('phone_number'))
										<span class="help-block">{{ $errors->first('phone_number') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('dropbox') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Phone Extension</label>
								<div class="col-sm-6">
									<input id="phone_extension" type="text" class="form-control" name="phone_extension" value="{{ old('phone_extension') }}" placeholder="Phone Extension">
									@if ($errors->has('phone_extension'))
										<span class="help-block">{{ $errors->first('phone_extension') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('terms') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Terms</label>
								<div class="col-sm-6">
									{{Form::select('terms',config('constants.terms'),old('terms'),['class'=>'form-control','id'=>'terms'])}}
									@if ($errors->has('terms'))
										<span class="help-block">{{ $errors->first('terms') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('new_terms') ? ' has-error' : ' hide' }}">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-6">	{{Form::text('new_terms',old('new_terms'),['class'=>'form-control','placeholder'=>'Enter New Terms','id'=>'new_terms'])}}
								@if ($errors->has('new_terms'))
									<span class="help-block">{{ $errors->first('new_terms') }}</span>
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
$(document).on('change','#terms',function(){
	/* if($(this).val() == 3){
		$('#new_terms').closest('.form-group').removeClass('hide');
	}else{
		$('#new_terms').closest('.form-group').addClass('hide');
	}*/
});

</script>		  
@endsection		  