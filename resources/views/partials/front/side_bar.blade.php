<div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
	<div class="sidebar">
		 <div class="sidebar_title">
			Have Questions?
		</div>
		<strong>Call Easy Order Banners Today at (800) 920-9527 </strong>
		<p>
			Or send us an email for any custom sized large vinyl banner or mesh banner quote! We can do any size!
		</p>
		<div class="space"></div>
		<div class="sidebar_title">
			Shop by Category
		</div>
		@php
			$categories = \App\Category::where('parent_id',0)->where('status',1)->with('child')->orderBy('name','ASC')->limit(50)->get();
			$i = 1 ;		
		@endphp
		<div class="sidebarnav">
			<ul>
				@foreach($categories as $cat)
					@if($cat->id != config('constants.CUSTOM_CATEGORY_ID'))
						<li class="{{(count($cat->child) > 0)?'has-sub':''}}"><a href="{{url($cat->slug)}}"><span>{{$cat->name}}</span></a>
							<i rel="{{url($cat->slug)}}" class="fa fa-arrow-right"></i>
						
						@if(count($cat->child) > 0)
							<ul>
								@foreach($cat->child as $child)
									<li class="{{(count($child->child) > 0)?'has-sub':''}}"><a href='{{url($child->slug)}}'><span>{{$child->name}}</span></a>
									<i rel="{{url($child->slug)}}" class="fa fa-arrow-right"></i>

									@if(count($child->child) > 0)
										<ul>
											@foreach($child->child as $child2)
												<li><a href='{{url($child2->slug)}}'><span>{{$child2->name}}</span></a>
													<i rel="{{url($child2->slug)}}" class="fa fa-arrow-right"></i>
												</li>
											@endforeach
										</ul>					
									@endif

									</li>
								@endforeach
							</ul>					
						@endif
						</li>
					@endif
				@endforeach
			</ul>
		</div>
		<div class="space"></div>
		@php
			if(!isset($testimonial)){
				$testimonial = \App\Testimonials::inRandomOrder()->first();
			}
		@endphp
		<div class="sidebar_title">
			{{$testimonial->designation_company}}
		</div>

		<p>{!!$testimonial->content!!}<span class="clientname">- {{$testimonial->name}}</span>
		</p>
	</div>
</div>

<style type="text/css">
.sidebar li svg
{
	position: absolute;
    top: 7px;
    right: 10px;
    color: #66a3e0;
    font-size: 20px;
    cursor: pointer;
    z-index: 999
} 

.sidebar li ul li svg
{
	color: #66a3e0;
} 
.sidebar li:hover svg,
.sidebar li.open svg
{
	color: #FFF;
}  

.sidebar li.open ul li:hover svg,
.sidebar li.open ul li.open svg,
.sidebar li.open ul li svg
{
	color: #66a3e0;
}  
</style>
<script type="text/javascript">
$( document ).ready(function(event) {
   $('.sidebar li').on('click','svg',function(){
   		var rel= $(this).attr('rel');   		
   		window.location = rel;
   		event.preventdefault();
   });
});

</script>