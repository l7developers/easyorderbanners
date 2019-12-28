@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Give Product Review</h2>
	</div>
</section>
<link href="{{ asset('public/css/front/5star.css') }}" rel="stylesheet">
<?php //pr(old());die; ?>
<?php //pr($errors);die; ?>
<section class="innerpages">
	 <div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
				<nav class="my_account_nav">
					<ul>
						<li><a href="{{url('orders')}}">Back</a></li>
					</ul>
				</nav>
			</div>
		</div>
		<!--<div class="space"></div>-->
	 	<div class="row">
		 	<div class="col-sm-6 col-xs-12">	{{Form::model('review',['role'=>'form','class'=>'contactpage','id'=>'review'])}}
				{{Form::hidden('order_id',$orderId)}}
				{{Form::hidden('product_id',$productId)}}

				{{Form::label('rating','Rating',array('class'=>''))}} 
				<div class="form-group{{ $errors->has('OldPassword') ? ' has-error' : '' }}">
					<ul class="rate-area">
						<input type="radio" id="5-star" name="rating" value="5" /><label for="5-star" title="Amazing">5 stars</label>
						<input type="radio" id="4-star" name="rating" value="4" /><label for="4-star" title="Good">4 stars</label>
						<input type="radio" id="3-star" name="rating" value="3" /><label for="3-star" title="Average">3 stars</label>
						<input type="radio" id="2-star" name="rating" value="2" /><label for="2-star" title="Not Good">2 stars</label>
						<input type="radio" id="1-star" name="rating" value="1" /><label for="1-star" title="Bad">1 star</label>
					</ul>
					@if ($errors->has('rating'))
						<div class="clearfix"></div>
						<span class="help-block">{{ $errors->first('rating') }}</span>
					@endif
				</div>
				<div class="clearfix"></div>
				<div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
					{{Form::label('comment','Comment',array('class'=>''))}} {{Form::textarea('comment','',['class'=>'form-control','placeholder'=>'Comment'])}}
					@if ($errors->has('comment'))
						<span class="help-block">{{ $errors->first('comment') }}</span>
					@endif
				</div>
				<button type="submit" class="btn btn-default">Save</button>
			{{Form::close()}}
			</div>
	 	</div>
	 </div>
</section>
<script>
/* $('#review').submit(function (evt) {
	evt.preventDefault();
	var on = 1;
	if(!$('input[name="rating"]').is(':checked')){
		on = 0;
		$('input[name="rating"]').closest('div').addClass('has-error');
	}
	if($('textarea[name="comment"]').val() == ''){
		on = 0;
		$('textarea[name="comment"]').closest('div').addClass('has-error');
	}
}); */
</script>
@endsection