@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Designer Edit</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">		
					<form role="form" method="POST" action="{{ url('admin/designers/edit/'.$id) }}">
					{{ csrf_field() }}
						<div class="col-sm-12">
							<div class="form-group row{{ $errors->has('fname') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">First Name<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="fname" type="text" class="form-control" name="fname" value="{{ (old('fname')) ? old('fname') : $designer->fname }}" placeholder="First Name">
									@if ($errors->has('fname'))
										<span class="help-block">{{ $errors->first('fname') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('lname') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Last Name<span class="text-danger">*</span></label>
								<div class="col-sm-6">
								<input id="lname" type="text" class="form-control" name="lname" value="{{ (old('lname')) ? old('lname') : $designer->lname }}" placeholder="last Name">
									@if ($errors->has('lname'))
										<span class="help-block">{{ $errors->first('lname') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">Intercompany Extension</label>
								<div class="col-sm-6">
								<input id="extension" type="text" class="form-control" name="extension" value="{{ (old('extension')) ? old('extension') : $designer->extension }}" placeholder="Intercompany Extension">									
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-3 form-control-label">Direct</label>
								<div class="col-sm-6">
								<input id="direct" type="text" class="form-control" name="direct" value="{{ (old('direct')) ? old('direct') : $designer->direct }}" placeholder="Direct">									
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-3 form-control-label">Mobile</label>
								<div class="col-sm-6">
								<input id="mobile" type="text" class="form-control" name="mobile" value="{{ (old('mobile')) ? old('mobile') : $designer->mobile }}" placeholder="Mobile">									
								</div>
							</div>

							<div class="form-group row{{ $errors->has('email') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Email<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="email" type="email" class="form-control" name="email" value="{{ (old('email')) ? old('email') : $designer->email }}" placeholder="Email">
									@if ($errors->has('email'))
										<span class="help-block">{{ $errors->first('email') }}</span>
									@endif
								</div>
							</div>
							<?php /* <div class="form-group row{{ $errors->has('phone_number') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Phone Number<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="phone_number" type="text" class="form-control" name="phone_number" value="{{ (old('phone_number')) ? old('phone_number') : $designer->phone_number }}" placeholder="Phone Number">
									@if ($errors->has('phone_number'))
										<span class="help-block">{{ $errors->first('phone_number') }}</span>
									@endif
								</div>
							</div> */ ?>
							<div class="form-group row{{ $errors->has('tFlow') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">tFlow Details<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="tFlow" type="text" class="form-control" name="tFlow" value="{{ (old('tFlow')) ? old('tFlow') : $designer->tFlow }}" placeholder="tFlow Details">
									@if ($errors->has('tFlow'))
										<span class="help-block">{{ $errors->first('tFlow') }}</span>
									@endif
								</div>
							</div>
							<div class="line"></div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-9 offset-sm-2">
									<button type="submit" class="btn btn-primary">Update</button>
									<a href="{{url('/admin/designers/lists')}}" class="btn btn-warning">Back</a>
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