@extends('layouts.admin_layout')
@section('content')

<section class="content-header">
  <h1>Top Blue Section Content</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">
					<form role="form" method="POST">
						{{ csrf_field() }}
						<div class="form-group row{{ $errors->has('title') ? ' has-error' : '' }}">
							<label class="col-sm-2 form-control-label">Title<span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<input id="title" type="text" class="form-control" name="title" value="{{ (array_key_exists('title',old()))?old('title'):$obj->title }}" placeholder="Enter Title">
								@if ($errors->has('title'))
									<span class="help-block">
										<strong>{{ $errors->first('title') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group row{{ $errors->has('description') ? ' has-error' : '' }}">
							<label class="col-sm-2 form-control-label">Description<span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<textarea id="description" class="form-control" name="description" placeholder="Enter Description">{{(array_key_exists('description',old()))?old('description'):$obj->description}}</textarea>
								@if ($errors->has('description'))
									<span class="help-block">
										<strong>{{ $errors->first('description') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="line"></div>
						<div class="form-group row">
							<label class="col-sm-2 form-control-label">&nbsp;</label>
							<div class="col-sm-4 offset-sm-2">
								<button type="submit" class="btn btn-primary">Update</button>
							</div>
						</div>
					</form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection		  