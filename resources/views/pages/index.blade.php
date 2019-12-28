@extends('layouts.app')

@section('meta')
<meta name="title" content="{{(isset($pageTitle))?$pageTitle:$page[0]['title']}}">
<meta name="description" content="{{(!empty($page[0]['meta_description']))?$page[0]['meta_description']:$page[0]['title']}}">
<meta name="keywords" content="{{$page[0]['meta_tag']}}">
@endsection

@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>{{$page[0]['title']}}</h2>
	</div>
</section>
<section class="innerpages">
	 <div class="container mtb">
	 	<div class="row">
	 		<div class="col-xs-12 col-sm-8 col-md-8 {{($page[0]['page_type']=='sidebar')?'col-lg-9':'col-lg-12'}} static_page">
				{!! $page[0]['body']; !!}
			</div>
			@if($page[0]['page_type']=='sidebar')
				@include('partials.front.side_bar')
			@endif
	 	</div>
	 </div>
</section>
@endsection