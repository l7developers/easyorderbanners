@extends('layouts.app')

@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Testimonials</h2>
	</div>
</section>

<section class="innerpages">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 sidebar">
				@foreach($testimonials as $testimonial)
					<div class="sidebar_title">{{$testimonial->name.', '.$testimonial->designation_company}}</div>
					<p>{!!$testimonial->content!!}</p>
				@endforeach
				<div class="space"></div>
				<div class="pull-left">  {{ $testimonials->links() }} </div>
			</div>
			@include('partials.front.side_bar')
		</div>
	</div>
</section>
@endsection
