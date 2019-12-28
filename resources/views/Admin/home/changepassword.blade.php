@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Change Password</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">				
				<div class="box-body">
				<form method="post" role="form">
				{{ csrf_field() }}
				<div class="row" id="divDataEntry">
					<div class="col-md-6">
						<div class="form-group row{{ $errors->has('OldPassword') ? ' has-error' : '' }}">
							<label class="col-sm-4 form-control-label">Old Password</label>
							<div class="col-sm-8">
								<input id="OldPassword" type="password" class="form-control" name="OldPassword" value="{{old('OldPassword')}}" placeholder="Old Password">
								@if ($errors->has('OldPassword'))
									<span class="help-block">
										<strong>{{ $errors->first('OldPassword') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('NewPassword') ? ' has-error' : '' }}">
							<label class="col-sm-4 form-control-label">New Password</label>
							<div class="col-sm-8">
								<input id="NewPassword" type="password" class="form-control" name="NewPassword" value="{{old('NewPassword')}}" placeholder="New Password">
								@if ($errors->has('NewPassword'))
									<span class="help-block">
										<strong>{{ $errors->first('NewPassword') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('NewPassword_confirmation') ? ' has-error' : '' }}">
							<label class="col-sm-4 form-control-label">Confirm Password</label>
							<div class="col-sm-8">
								<input id="NewPassword_confirmation" type="password" class="form-control" name="NewPassword_confirmation" value="{{old('NewPassword_confirmation')}}" placeholder="Confirm New Password">
								@if ($errors->has('NewPassword_confirmation'))
									<span class="help-block">
										<strong>{{ $errors->first('NewPassword_confirmation') }}</strong>
									</span>
								@endif
							</div>
						</div>
						
						<div class="line"></div>
						<div class="form-group row">
							<label class="col-sm-4 form-control-label">&nbsp;</label>
							<div class="col-sm-4 offset-sm-2">
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>
					</div>
				</div>
				</form>			
				</div>
			</div>
		</div>
	</div>
</section>
		  
@endsection		  