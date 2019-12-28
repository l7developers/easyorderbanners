@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Add Agent</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">		
					<form role="form" method="POST" action="{{ url('admin/agents/add') }}">
					{{ csrf_field() }}
						<div class="col-sm-6">
							<div class="form-group row{{ $errors->has('role_id') ? ' has-error' : '' }}">
								<label class="col-sm-4 form-control-label">Role<span class="text-danger">*</span></label>
								<div class="col-sm-8">
									{{Form::select('role_id',$roles,(old('role_id')) ? old('role_id') : '',['class' => 'form-control','id' => 'role_id','placeholder' => 'Select user role'] )}}
									@if ($errors->has('role_id'))
										<span class="help-block">{{ $errors->first('role_id') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('fname') ? ' has-error' : '' }}">
								<label class="col-sm-4 form-control-label">First Name<span class="text-danger">*</span></label>
								<div class="col-sm-8">
									<input id="fname" type="text" class="form-control" name="fname" value="{{ old('fname') }}" placeholder="First Name">
									@if ($errors->has('fname'))
										<span class="help-block">{{ $errors->first('fname') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('lname') ? ' has-error' : '' }}">
								<label class="col-sm-4 form-control-label">Last Name<span class="text-danger">*</span></label>
								<div class="col-sm-8">
								<input id="lname" type="text" class="form-control" name="lname" value="{{ old('lname') }}" placeholder="last Name">
									@if ($errors->has('lname'))
										<span class="help-block">{{ $errors->first('lname') }}</span>
									@endif
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 form-control-label">Intercompany Extension</label>
								<div class="col-sm-8">
								<input id="extension" type="text" class="form-control" name="extension" value="{{ old('extension')}}" placeholder="Intercompany Extension">									
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 form-control-label">Direct</label>
								<div class="col-sm-8">
								<input id="direct" type="text" class="form-control" name="direct" value="{{ old('direct')}}" placeholder="Direct">									
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 form-control-label">Mobile</label>
								<div class="col-sm-8">
								<input id="mobile" type="text" class="form-control" name="mobile" value="{{ old('mobile')}}" placeholder="Mobile">									
								</div>
							</div>

							<div class="form-group row{{ $errors->has('email') ? ' has-error' : '' }}">
								<label class="col-sm-4 form-control-label">Email<span class="text-danger">*</span></label>
								<div class="col-sm-8">
									<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">
									@if ($errors->has('email'))
										<span class="help-block">{{ $errors->first('email') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('password') ? ' has-error' : '' }}">
								<label class="col-sm-4 form-control-label">Password<span class="text-danger">*</span></label>
								<div class="col-sm-8">
									<input id="password" type="password" class="form-control" name="password" placeholder="Password">
									@if ($errors->has('password'))
										<span class="help-block">{{ $errors->first('password') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-4 form-control-label">Confirm Password<span class="text-danger">*</span></label>
								<div class="col-sm-8">
									<input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
								</div>
							</div>
							<div class="line"></div>
							<div class="form-group row">
								<label class="col-sm-4 form-control-label">&nbsp;</label>
										
								<div class="col-sm-8 offset-sm-2">
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
@endsection		  