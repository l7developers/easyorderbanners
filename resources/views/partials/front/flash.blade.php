<!-- Session Flash -->
@if(Session::has('success'))
	<div class="row">
		<div class="col-lg-12">
			<div class="alert alert-info alert-dismissable" data-dismiss="alert" aria-hidden="true">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<i class="fa fa-info-circle"></i>  <strong>{!! session('success') !!}</strong> 
			</div>
		</div>
	</div>
@endif

@if(Session::has('error'))
	<div class="row">
		<div class="col-lg-12">
			<div class="alert alert-danger alert-dismissable" data-dismiss="alert" aria-hidden="true">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<i class="fa fa-info-circle"></i>  <strong>{!! session('error') !!}</strong> 
			</div>
		</div>
	</div>
@endif