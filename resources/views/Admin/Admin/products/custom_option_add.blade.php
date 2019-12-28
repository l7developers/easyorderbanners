@extends('layouts.admin_layout')

@section('content')
<?php //pr($errors->all()) ;die;//echo config('constants.SITE_URL');die;?>
<section class="content-header">
	<h1>Add Custom Option</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">				
				<div class="box-body">
					@if (count($errors) > 0)
						<ul class="errorMessages" style="display:block;">
							@foreach ($errors->all() as $error)
								<li><span class="">{{ $error }}</span></li>
							@endforeach
						</ul>
					@endif
					<form role="form" method="POST" action="{{ url('admin/products/custom/option/add') }}" enctype="multipart/form-data"/>
					{{ csrf_field() }}
					<div class="col-md-12">
						<div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Name<span class="text-danger">*</span></label>
							<div class="col-sm-4">
								<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Option Name" />
							</div>
						</div>
						<div class="form-group row{{ $errors->has('Label') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Label</label>
							<div class="col-sm-4">
								<input id="label" type="text" class="form-control" name="label" value="{{ old('label') }}" placeholder="Option Label" />
							</div>
						</div>
						<div class="form-group row{{ $errors->has('paid_free') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Free</label>
							<div class="col-sm-4" id="paid_free_div">
								<input type="checkbox" name="paid_free" id="paid_free" value="1" class="flat-red" />
							</div>
						</div>
						<div class="form-group row{{ $errors->has('field_group') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Group Field<span class="text-danger">*</span></label>
							<div class="col-sm-4">
							{{Form::select('field_group', [''=>'Select Group Field','printing'=>'Printing Options','finishing'=>'Finishing Options','production'=>'Design Services Options'],old('field_group'),array('class'=>'form-control'))}}
							</div>
						</div>
						<div class="form-group row{{ $errors->has('field_group') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Price calculation format<span class="text-danger">*</span></label>
							<div class="col-sm-4">
							{{Form::select('price_formate', [''=>'Select price calculation format','area'=>'Price by sqft area','parimeter'=>'Price by sqft parimeter','item'=>'Price by item','line_item'=>'Price by line item'],old('field_group'),array('class'=>'form-control'))}}
							</div>
						</div>
						<div class="form-group row{{ $errors->has('select_type') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Select Type<span class="text-danger">*</span></label>
							<div class="col-sm-4">
								<select name="select_type" id="select_type" class="form-control select_type">
									<option value="">Select type</option>
									<option value="1">Select Box</option>
									<option value="2">Input</option>
								</select>
							</div>
						</div>
						<div class="form-group row hide" id="custom_div">
							<label class="col-sm-3 form-control-label">&nbsp;</label>
							<div class="col-sm-9">
								<div class="panel panel-info">
									<div class="panel-heading"><h5></h5></div>
									<div class="panel-body">
										<div class="custom_option_panel"></div>
										<div class="clearfix"></div>
										<div class="row add_more_btn">
											<div class="col-md-2">
												<div class="form-group"> 
													<button class="btn btn-success add-custom-option" type="button">Add More</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group row{{ $errors->has('description') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Description</label>
							<div class="col-sm-9">
								<textarea class="form-control" name="description" id="description" placeholder="Description">{{ old('description') }} </textarea>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group row">
							<label class="col-sm-3 form-control-label">&nbsp;</label>
							<div class="col-sm-4 offset-sm-2">
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
						</div>
					</div>
					</form>
                </div>
              </div>
            </div>
          </div>
</section>
<script type="text/javascript">
$(document).ready(function () {
	var _key = parseInt($('.custom_option_panel').length);
	var paid = true;
	
	$('#paid_free_div ins').on('click',function(){
		var value = $(this).siblings('input');
		if (value.is(':checked')) {
			paid = false;
			$('div.custom_option_panel').find('.price').remove();
		} else {
			paid = true;
			$('div.custom_option_panel').find('.value').each(function() {
				var key = $(this).attr('data');
				var price_html = '<div class="col-xs-2 col-sm-2 price"><div class="form-group"><label for="">Option Price</label><input placeholder="Option price" name="options['+key+'][price]" class="form-control" required/></div></div>';
				$(this).after(price_html);
			});
		}
		//alert(paid);
	});
	
	$('.select_type').on('change',function(){
		_key = 1;
		if($(this).val() == 1){
			$('.add_more_btn').removeClass('hide');
			$('div#custom_div').find('h5').html("Option Values");
			$('div.custom_option_panel').html('');
			var str = '<div class="row optionClone value_box"><div class="col-xs-4 col-sm-4" data="'+_key+'"><div class="form-group"><label for="">Option Value</label><input placeholder="Option value" name="options['+_key+'][value]" class="form-control" required/></div></div>';

			str += '<div class="col-xs-1 col-sm-1 weight value"><div class="form-group"><label>Default</label><br/><input type="checkbox" placeholder="Default" name="options['+_key+'][default]" class="flat-red2"/></div></div>';

			if(paid){
				str += '<div class="col-xs-2 col-sm-2 price"><div class="form-group"><label for="">Option Price</label><input placeholder="Option price" name="options['+_key+'][price]" class="form-control" required/></div></div>';
			}
			str += '<div class="col-xs-2 col-sm-2 weight"><div class="form-group"><label for="">Option Weight<span class="size_msg">(In LBS)</span></label><input placeholder="Option weight" name="options['+_key+'][weight]" class="form-control"/></div></div>';

			str += '<div class="col-xs-2 col-sm-2 weight"><div class="form-group"><label for="">Flat Rate Additional Price</label><input placeholder="Flat Rate Additional Price" name="options['+_key+'][flat_rate_additional_price]" class="form-control"/></div></div>';
			
			str += '<div class="col-xs-1 col-sm-1"><div class="form-group"> <label>&nbsp;</label><div class="clearfix"></div><button class="btn btn-danger remove-custom-option" type="button"><i class="fa fa-minus"></i></button></div></div><div class="clearfix"></div></div>';
			
			$('div.custom_option_panel').append(str);
			$('#custom_div').removeClass('hide');
		}
		else if($(this).val() == 2){
			$('.add_more_btn').addClass('hide');
			$('div#custom_div').find('h5').html("Input Values");
			$('div.custom_option_panel').html('');
			var str = '<div class="row optionClone"><div class="col-md-5"><div class="form-group"><div class="input text"><label for="post-workshops-permissions-title">Price Per Character</label><input placeholder="Option value" name="options[][price]" class="form-control" required/></div></div></div><div class="clearfix"></div></div>';
			
			$('div.custom_option_panel').append(str);
			$('#custom_div').removeClass('hide');
		}
		else{
			$('div#custom_div').find('h5').html("");
			$('#custom_div').addClass('hide');
		}
	});
	
	$(document).on('click','.add-custom-option', function () {
		_key++;
		var str = '<div class="row optionClone value_box"><div class="col-xs-4 col-sm-4" data="'+_key+'"><div class="form-group"><label for="">Option Value</label><input placeholder="Option value" name="options['+_key+'][value]" class="form-control" required/></div></div>';

		str += '<div class="col-xs-1 col-sm-1 weight value"><div class="form-group"><label>Default</label><br/><input type="checkbox" placeholder="Default" name="options['+_key+'][default]" class="flat-red2"/></div></div>';

		if(paid){
			str += '<div class="col-xs-2 col-sm-2 price"><div class="form-group"><label for="">Option Price</label><input placeholder="Option" name="options['+_key+'][price]" class="form-control price" required/></div></div>';
		}
		str += '<div class="col-xs-2 col-sm-2 weight"><div class="form-group"><label for="">Option Weight<span class="size_msg">(In LBS)</span></label><input placeholder="Option weight" name="options['+_key+'][weight]" class="form-control"/></div></div>';

		str += '<div class="col-xs-2 col-sm-2 weight"><div class="form-group"><label for="">Flat Rate Additional Price</label><input placeholder="Flat Rate Additional Price" name="options['+_key+'][flat_rate_additional_price]" class="form-control"/></div></div>';
			
		str += '<div class="col-xs-1 col-sm-1"><div class="form-group"> <label>&nbsp;</label><div class="clearfix"></div><button class="btn btn-danger remove-custom-option" type="button"><i class="fa fa-minus"></i></button></div></div><div class="clearfix"></div></div>';
			
		$('.custom_option_panel').append(str);
		//alert(_key);
	});
	
	$(document).on('click', '.remove-custom-option', function () {
		//if(confirm('Are You sure to delete it ?')){
			$(this).closest('.optionClone').remove();
		//}
	});
});
</script>
<style type="text/css">
	.flat-red2
	{
		width: 20px;
		height: 20px;
	}
</style>		
@endsection		  