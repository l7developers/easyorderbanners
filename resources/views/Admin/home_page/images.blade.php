@extends('layouts.admin_layout')
@section('content')
<?php
//pr($errors);

?>
<section class="content-header">
  <h1>Top Images</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">
					<form role="form" method="POST" enctype="multipart/form-data">
					{{ csrf_field() }}
						<div class="form-group row{{ $errors->has('heading') ? ' has-error' : '' }}">
							<label class="col-sm-2 form-control-label">Heading<span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<input id="heading" type="text" class="form-control" name="heading" value="{{array_key_exists('heading',old())?old('heading'):$obj->title}}" placeholder="Enter Heading">
								@if ($errors->has('heading'))
									<span class="help-block">
										<strong>{{ $errors->first('heading') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<fieldset>
							<legend>Image 1:</legend>	
							<div class="form-group row{{ $errors->has('images.1.image') ? ' has-error' : '' }}" id="image_main_div1">
								<label class="col-sm-2 form-control-label">Image</label>
								<div class="col-sm-6">
									<input id="image1" type="file" class="image" name="images[1][image]" data-1="image_main_div1" data-2="image_contain_div1" data-3="image_div1">
									@if ($errors->has('images.1.image'))
										<span class="help-block">
											<strong>{{ $errors->first('images.1.image') }}</strong>
										</span>
									@else
										<span class="help-block"></span>
									@endif
								</div>
							</div>
							<div class="form-group row" id="image_div1">
								<label class="col-sm-2 form-control-label">&nbsp;</label>
								<div class="col-sm-6" id="image_contain_div1">
								@if(!empty($images[1]['image']))
									<div class="col-sm-4 image_main_box">
										<label>
											<img class="img-responsive" src="{{URL::to('public/uploads/home/images/'.$images[1]['image'])}}" alt="Photo">
										</label>
									</div>
								@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.1.rollover_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[1][rollover_text]" value="{{(old('images.1.rollover_text')!= null)?old('images.1.rollover_text'):$images[1]['rollover_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.1.rollover_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.1.rollover_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.1.rollover_button_link') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Link<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[1][rollover_button_link]" value="{{(old('images.1.rollover_button_link')!= null)?old('images.1.rollover_button_link'):$images[1]['rollover_button_link']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.1.rollover_button_link'))
										<span class="help-block">
											<strong>{{ $errors->first('images.1.rollover_button_link') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.1.rollover_button_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[1][rollover_button_text]" value="{{(old('images.1.rollover_button_text')!= null)?old('images.1.rollover_button_text'):$images[1]['rollover_button_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.1.rollover_button_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.1.rollover_button_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>Image 2:</legend>	
							<div class="form-group row{{ $errors->has('images.2.image') ? ' has-error' : '' }}" id="image_main_div2">
								<label class="col-sm-2 form-control-label">Image</label>
								<div class="col-sm-6">
									<input id="image2" type="file" class="image" name="images[2][image]" data-1="image_main_div2" data-2="image_contain_div2" data-3="image_div2">
									@if ($errors->has('images.2.image'))
										<span class="help-block">
											<strong>{{ $errors->first('images.2.image') }}</strong>
										</span>
									@else
										<span class="help-block"></span>
									@endif
								</div>
							</div>
							<div class="form-group row" id="image_div2">
								<label class="col-sm-2 form-control-label">&nbsp;</label>
								<div class="col-sm-6" id="image_contain_div2">
								@if(!empty($images[2]['image']))
									<div class="col-sm-4 image_main_box">
										<label>
											<img class="img-responsive" src="{{URL::to('public/uploads/home/images/'.$images[2]['image'])}}" alt="Photo">
										</label>
									</div>
								@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.2.rollover_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[2][rollover_text]" value="{{(old('images.2.rollover_text')!= null)?old('images.2.rollover_text'):$images[2]['rollover_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.2.rollover_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.2.rollover_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.2.rollover_button_link') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Link<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[2][rollover_button_link]" value="{{(old('images.2.rollover_button_link')!= null)?old('images.2.rollover_button_link'):$images[2]['rollover_button_link']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.2.rollover_button_link'))
										<span class="help-block">
											<strong>{{ $errors->first('images.2.rollover_button_link') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.2.rollover_button_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[2][rollover_button_text]" value="{{(old('images.2.rollover_button_text')!= null)?old('images.2.rollover_button_text'):$images[2]['rollover_button_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.2.rollover_button_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.2.rollover_button_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>Image 3:</legend>	
							<div class="form-group row{{ $errors->has('images.3.image') ? ' has-error' : '' }}" id="image_main_div3">
								<label class="col-sm-2 form-control-label">Image</label>
								<div class="col-sm-6">
									<input id="image3" type="file" class="image" name="images[3][image]" data-1="image_main_div3" data-2="image_contain_div3" data-3="image_div3">
									@if ($errors->has('images.3.image'))
										<span class="help-block">
											<strong>{{ $errors->first('images.3.image') }}</strong>
										</span>
									@else
										<span class="help-block"></span>
									@endif
								</div>
							</div>
							<div class="form-group row" id="image_div3">
								<label class="col-sm-2 form-control-label">&nbsp;</label>
								<div class="col-sm-6" id="image_contain_div3">
								@if(!empty($images[3]['image']))
									<div class="col-sm-4 image_main_box">
										<label>
											<img class="img-responsive" src="{{URL::to('public/uploads/home/images/'.$images[3]['image'])}}" alt="Photo">
										</label>
									</div>
								@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.3.rollover_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[3][rollover_text]" value="{{(old('images.3.rollover_text')!= null)?old('images.3.rollover_text'):$images[3]['rollover_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.3.rollover_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.3.rollover_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.3.rollover_button_link') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Link<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[3][rollover_button_link]" value="{{(old('images.3.rollover_button_link')!= null)?old('images.3.rollover_button_link'):$images[3]['rollover_button_link']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.3.rollover_button_link'))
										<span class="help-block">
											<strong>{{ $errors->first('images.3.rollover_button_link') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.3.rollover_button_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[3][rollover_button_text]" value="{{(old('images.3.rollover_button_text')!= null)?old('images.3.rollover_button_text'):$images[3]['rollover_button_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.3.rollover_button_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.3.rollover_button_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>Image 4:</legend>	
							<div class="form-group row{{ $errors->has('images.4.image') ? ' has-error' : '' }}" id="image_main_div4">
								<label class="col-sm-2 form-control-label">Image</label>
								<div class="col-sm-6">
									<input id="image4" type="file" class="image" name="images[4][image]" data-1="image_main_div4" data-2="image_contain_div4" data-3="image_div4">
									@if ($errors->has('images.4.image'))
										<span class="help-block">
											<strong>{{ $errors->first('images.4.image') }}</strong>
										</span>
									@else
										<span class="help-block"></span>
									@endif
								</div>
							</div>
							<div class="form-group row" id="image_div4">
								<label class="col-sm-2 form-control-label">&nbsp;</label>
								<div class="col-sm-6" id="image_contain_div4">
								@if(!empty($images[4]['image']))
									<div class="col-sm-4 image_main_box">
										<label>
											<img class="img-responsive" src="{{URL::to('public/uploads/home/images/'.$images[4]['image'])}}" alt="Photo">
										</label>
									</div>
								@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.4.rollover_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[4][rollover_text]" value="{{(old('images.4.rollover_text')!= null)?old('images.4.rollover_text'):$images[4]['rollover_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.4.rollover_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.4.rollover_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.4.rollover_button_link') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Link<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[4][rollover_button_link]" value="{{(old('images.4.rollover_button_link')!= null)?old('images.4.rollover_button_link'):$images[4]['rollover_button_link']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.4.rollover_button_link'))
										<span class="help-block">
											<strong>{{ $errors->first('images.4.rollover_button_link') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.4.rollover_button_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[4][rollover_button_text]" value="{{(old('images.4.rollover_button_text')!= null)?old('images.4.rollover_button_text'):$images[4]['rollover_button_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.4.rollover_button_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.4.rollover_button_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>Image 5:</legend>	
							<div class="form-group row{{ $errors->has('images.5.image') ? ' has-error' : '' }}" id="image_main_div5">
								<label class="col-sm-2 form-control-label">Image</label>
								<div class="col-sm-6">
									<input id="image5" type="file" class="image" name="images[5][image]" data-1="image_main_div5" data-2="image_contain_div5" data-3="image_div5">
									@if ($errors->has('images.5.image'))
										<span class="help-block">
											<strong>{{ $errors->first('images.5.image') }}</strong>
										</span>
									@else
										<span class="help-block"></span>
									@endif
								</div>
							</div>
							<div class="form-group row" id="image_div5">
								<label class="col-sm-2 form-control-label">&nbsp;</label>
								<div class="col-sm-6" id="image_contain_div5">
								@if(!empty($images[5]['image']))
									<div class="col-sm-4 image_main_box">
										<label>
											<img class="img-responsive" src="{{URL::to('public/uploads/home/images/'.$images[5]['image'])}}" alt="Photo">
										</label>
									</div>
								@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.5.rollover_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[5][rollover_text]" value="{{(old('images.5.rollover_text')!= null)?old('images.5.rollover_text'):$images[5]['rollover_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.5.rollover_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.5.rollover_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.5.rollover_button_link') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Link<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[5][rollover_button_link]" value="{{(old('images.5.rollover_button_link')!= null)?old('images.5.rollover_button_link'):$images[5]['rollover_button_link']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.5.rollover_button_link'))
										<span class="help-block">
											<strong>{{ $errors->first('images.5.rollover_button_link') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.5.rollover_button_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[5][rollover_button_text]" value="{{(old('images.6.rollover_button_text')!= null)?old('images.6.rollover_button_text'):$images[6]['rollover_button_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.5.rollover_button_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.5.rollover_button_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>Image 6:</legend>	
							<div class="form-group row{{ $errors->has('images.6.image') ? ' has-error' : '' }}" id="image_main_div6">
								<label class="col-sm-2 form-control-label">Image</label>
								<div class="col-sm-6">
									<input id="image6" type="file" class="image" name="images[6][image]" data-1="image_main_div6" data-2="image_contain_div6" data-3="image_div6">
									@if ($errors->has('images.6.image'))
										<span class="help-block">
											<strong>{{ $errors->first('images.6.image') }}</strong>
										</span>
									@else
										<span class="help-block"></span>
									@endif
								</div>
							</div>
							<div class="form-group row" id="image_div6">
								<label class="col-sm-2 form-control-label">&nbsp;</label>
								<div class="col-sm-6" id="image_contain_div6">
								@if(!empty($images[6]['image']))
									<div class="col-sm-4 image_main_box">
										<label>
											<img class="img-responsive" src="{{URL::to('public/uploads/home/images/'.$images[6]['image'])}}" alt="Photo">
										</label>
									</div>
								@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.6.rollover_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[6][rollover_text]" value="{{(old('images.6.rollover_text')!= null)?old('images.6.rollover_text'):$images[6]['rollover_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.6.rollover_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.6.rollover_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.6.rollover_button_link') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Link<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[6][rollover_button_link]" value="{{(old('images.6.rollover_button_link')!= null)?old('images.6.rollover_button_link'):$images[6]['rollover_button_link']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.6.rollover_button_link'))
										<span class="help-block">
											<strong>{{ $errors->first('images.6.rollover_button_link') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.6.rollover_button_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[6][rollover_button_text]" value="{{(old('images.6.rollover_button_text')!= null)?old('images.6.rollover_button_text'):$images[6]['rollover_button_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.6.rollover_button_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.6.rollover_button_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>Image 7:</legend>	
							<div class="form-group row{{ $errors->has('images.7.image') ? ' has-error' : '' }}" id="image_main_div7">
								<label class="col-sm-2 form-control-label">Image</label>
								<div class="col-sm-6">
									<input id="image7" type="file" class="image" name="images[7][image]" data-1="image_main_div7" data-2="image_contain_div7" data-3="image_div7">
									@if ($errors->has('images.7.image'))
										<span class="help-block">
											<strong>{{ $errors->first('images.7.image') }}</strong>
										</span>
									@else
										<span class="help-block"></span>
									@endif
								</div>
							</div>
							<div class="form-group row" id="image_div7">
								<label class="col-sm-2 form-control-label">&nbsp;</label>
								<div class="col-sm-6" id="image_contain_div7">
								@if(!empty($images[7]['image']))
									<div class="col-sm-4 image_main_box">
										<label>
											<img class="img-responsive" src="{{URL::to('public/uploads/home/images/'.$images[7]['image'])}}" alt="Photo">
										</label>
									</div>
								@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.7.rollover_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[7][rollover_text]" value="{{(old('images.7.rollover_text')!= null)?old('images.7.rollover_text'):$images[7]['rollover_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.7.rollover_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.7.rollover_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.7.rollover_button_link') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Link<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[7][rollover_button_link]" value="{{(old('images.7.rollover_button_link')!= null)?old('images.7.rollover_button_link'):$images[7]['rollover_button_link']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.7.rollover_button_link'))
										<span class="help-block">
											<strong>{{ $errors->first('images.7.rollover_button_link') }}</strong>
										</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('images.7.rollover_button_text') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Rollover Button Text<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="images[7][rollover_button_text]" value="{{(old('images.7.rollover_button_text')!= null)?old('images.7.rollover_button_text'):$images[7]['rollover_button_text']}}" placeholder="Enter Rollover Text">
									@if ($errors->has('images.7.rollover_button_text'))
										<span class="help-block">
											<strong>{{ $errors->first('images.7.rollover_button_text') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>Image Description</legend>
							<div class="form-group row{{ $errors->has('description') ? ' has-error' : '' }}">
								<label class="col-sm-2 form-control-label">Description<span class="text-danger">*</span></label>
								<div class="col-sm-10">
									<textarea id="description" class="form-control" name="description" placeholder="Enter Description">{{(array_key_exists('description',old()))?old('description'):$obj->description}}</textarea>
									@if ($errors->has('description'))
										<span class="help-block">
											<strong>{{ $errors->first('description') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</fieldset>
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
<script>
	CKEDITOR.replace( 'description', {
		filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
		height: '300',
	});

	$(function() {
		// Multiple images preview in browser
		var imagesPreview = function(input,main_div, placeToInsertImagePreview,remove_div,input_name) {

			if (input.files) {
				var filesAmount = input.files.length;
				for (i = 0; i < filesAmount; i++) {
					var reader = new FileReader();
					var tarr = input.files[i].name.split('/');
					var file = tarr[tarr.length-1];
					var data = file.split('.');
					var data = data[data.length-1];
					//alert(data);
					var noError= true;
					if ($.inArray(data, ['jpg', 'jpeg','png','gif']) == -1) {
						noError = false;
						$('#'+main_div).addClass('has-error');
						$('#'+main_div+' .col-sm-6 span').html('Please Select Only(gif, png, jpg, jpeg)');
						$("#"+input_name).val(null);
						$('div#'+remove_div).addClass('hide');
						return false;
					}
					
					if(noError){
						reader.onload = function(event) {
							$('#'+main_div+' span').html('');
							var clone = '<div class="col-sm-4 image_main_box"><label><img class="img-responsive" src="'+event.target.result+'" alt="Photo"></label></div>';
							//alert(clone);
							$('div#'+placeToInsertImagePreview).append(clone);
							$('div#'+remove_div).removeClass('hide');
						}
						reader.readAsDataURL(input.files[i]);
					}
				}
			}

		};
		$('.image').on('change', function() {
			var id = $(this).attr('id');
			var data1 = $(this).attr('data-1');
			var data2 = $(this).attr('data-2');
			var data3 = $(this).attr('data-3');
			//alert(id+' and '+data1+' and '+data2+' and '+data3)
			$('div#'+data2).html('');
			imagesPreview(this,data1,data2, data3,id);
		});
	});
</script>
@endsection		  