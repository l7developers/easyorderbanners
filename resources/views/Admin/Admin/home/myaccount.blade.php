@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>Welcome</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-info">
				<div class="box-body admin-view">
					<div class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Email</b></div>
						<div class="col-lg-9 col-xs-8">{{$data[0]['email']}}</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>First Name</b></div>
						<div class="col-lg-9 col-xs-8">{{$data[0]['fname']}}</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Last Name</b></div>
						<div class="col-lg-9 col-xs-8">{{$data[0]['lname']}}</div> <div class="clearfix"></div>
					</div>
					<div  class="col-lg-12">    
						<div class="col-lg-3 col-xs-4"><b>Phone Number</b></div>
						<div class="col-lg-9 col-xs-8">{{$data[0]['phone_number']}}</div> <div class="clearfix"></div>
					</div>
				</div>
			</div>
        </div>
		<div class="col-xs-12">
			<div class="box box-info">
				<div class="box-header">
					<h2>Edit Profile</h2>
				</div>
				<div class="box-body">
					<form method="post" role="form">
					{{ csrf_field() }}
						<div class="form-group row{{ $errors->has('fname') ? ' has-error' : '' }}">
							<label class="col-md-2 col-sm-3 form-control-label">First Name</label>
							<div class="col-sm-6">
								<input id="fname" type="text" class="form-control" name="fname" value="@php if (old('fname')) echo old('fname'); else echo $data[0]['fname'] @endphp" required>
								@if ($errors->has('fname'))
									<span class="help-block">
										<strong>{{ $errors->first('fname') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('lname') ? ' has-error' : '' }}">
							<label class="col-md-2 col-sm-3 form-control-label">Last Name</label>
							<div class="col-sm-6">
								<input id="lname" type="text" class="form-control" name="lname" value="@php if (old('lname')) echo old('lname'); else echo $data[0]['lname'] @endphp" required>
								@if ($errors->has('lname'))
									<span class="help-block">
										<strong>{{ $errors->first('lname') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('email') ? ' has-error' : '' }}">
							<label class="col-md-2 col-sm-3 form-control-label">Email</label>
							<div class="col-sm-6">
								<input id="email" type="email" class="form-control" name="email" value="@php if (old('email')) echo old('email'); else echo $data[0]['email'] @endphp" required>
								@if ($errors->has('email'))
									<span class="help-block">
										<strong>{{ $errors->first('email') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('phone_number') ? ' has-error' : '' }}">
							<label class="col-md-2 col-sm-3 form-control-label">Phone Number</label>
							<div class="col-sm-6">
								<input id="phone_number" type="text" class="form-control" name="phone_number" value="@php if (old('phone_number')) echo old('phone_number'); else echo $data[0]['phone_number'] @endphp" required>
								@if ($errors->has('phone_number'))
									<span class="help-block">
										<strong>{{ $errors->first('phone_number') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="line"></div>
						<div class="form-group row">
							<label class="col-md-2 col-sm-3 form-control-label">&nbsp;</label>
							<div class="col-sm-4 offset-sm-2">
								<button type="submit" class="btn btn-primary">Save</button>
							</div>
						</div>
					</form>	
				</div>
			</div>
		</div>
	</div>
</section>	

<!--<section class="content">
	<div class="row">
		<div class="detail_pending col-lg-6 col-xs-12 col-sm-5 col-md-4 my_account_left">
			<h2 class="tool_kit_heading">Edit Profile</h2>
			<div id="edit_profile" class="sidebar-submenu">
						
			</div>
		</div>
	</div>
</section>-->
		  
@endsection		  