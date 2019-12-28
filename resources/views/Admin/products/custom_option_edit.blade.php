@extends('layouts.admin_layout')

@section('content')
<?php //pr($errors->all()) ;die;//echo config('constants.SITE_URL');die;?>
<script>
$(document).ready(function () {
	var _key = parseInt($('.custom_option_panel').length);
});
</script>
<section class="content-header">
	<h1>Edit Custom Option</h1>
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
					<form role="form" method="POST" action="{{ url('admin/products/custom/option/edit/'.$id) }}" enctype="multipart/form-data"/>
					{{ csrf_field() }}
					<div class="col-md-12">
						<div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Name<span class="text-danger">*</span></label>
							<div class="col-sm-4">
								<input id="name" type="text" class="form-control" name="name" value="{{ array_key_exists('name',old())?old('name'):$option->name }}" placeholder="Option Name" required />
							</div>
						</div>
						<div class="form-group row{{ $errors->has('label') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Label</label>
							<div class="col-sm-4">
								<input id="label" type="text" class="form-control" name="label" value="{{ array_key_exists('label',old())?old('label'):$option->label }}" placeholder="Option label" required />
							</div>
						</div>
						<div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Free</label>
							<div class="col-sm-4" id="paid_free_div">
								<?php 
								$checked = '';
								if(array_key_exists('paid_free',old())){
									if(old('paid_free') == 1){	$checked = 'checked'; }
								}else{	if($option->free == 1){ $checked = 'checked';} }
								?>
								<input type="checkbox" name="paid_free" id="paid_free" value="1" {{$checked}} class="flat-red" />
							</div>
						</div>
						<div class="form-group row{{ $errors->has('field_group') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Group Field<span class="text-danger">*</span></label>
							<div class="col-sm-4" id="paid_free_div">
							{{Form::select('field_group', [''=>'Select Group Field','printing'=>'Printing Options','finishing'=>'Finishing Options','production'=>'Design Services Options'], array_key_exists('field_group',old())?old('field_group'):$option->field_group,array('class'=>'form-control'))}}
							</div>
						</div>
						<div class="form-group row{{ $errors->has('field_group') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Price calculation format<span class="text-danger">*</span></label>
							<div class="col-sm-4">
							{{Form::select('price_formate', [''=>'Select price calculation format','area'=>'Price by sqft area','parimeter'=>'Price by sqft parimeter','item'=>'Price by item','line_item'=>'Price by line item'],array_key_exists('price_formate',old())?old('price_formate'):$option->price_formate,array('class'=>'form-control'))}}
							</div>
						</div>
						<div class="form-group row{{ $errors->has('select_type') ? ' has-error' : '' }}">
							<label class="col-sm-3 form-control-label">Select Type<span class="text-danger">*</span></label>
							<div class="col-sm-4">
								<select name="select_type" id="select_type" class="form-control select_type" required>
									<option value="">Select type</option>
									<option <?php echo ($option->option_type) == 1? 'selected':'';?> value="1">Select Box</option>
									<option <?php echo ($option->option_type) == 2? 'selected':'';?> value="2">Input</option>
								</select>
							</div>
						</div>
						<div class="form-group row" id="custom_div">
							<label class="col-sm-3 form-control-label">&nbsp;</label>
							<div class="col-sm-9">
								<div class="panel panel-info">
									<div class="panel-heading"><h5>{{($option->option_type) == 1? 'Option Values':'Input Values'}}</h5></div>
									<div class="panel-body">
										<div class="custom_option_panel">
											<?php
											$data = json_decode($option->option_keys,true);	
											//pr($data);die;
											$i = 1;
											if($option->option_type == 1){
												foreach($data as $key=>$val){
													$str = '<div class="row optionClone value_box"><div class="col-xs-4 col-sm-4" data="'.$key.'"><div class="form-group"><div class="input text"><label for="post-workshops-permissions-title">Option Value</label><input placeholder="Option value" name="options['.$key.'][value]" class="form-control" value=\''.$val['value'].'\' required/></div></div></div>';

													$default_checked = '';
													if(array_key_exists('default',$val) && $val['default']==1){
														$default_checked ='checked';
													}
													$str .= '<div class="col-xs-1 col-sm-1 weight value"><div class="form-group"><label for="">Default</label><br/><input  type="checkbox" name="options['.$key.'][default]" class="flat-red2" value="1" '.$default_checked.'/></div></div>';
													
													if($option->free == 0){
														$price = '';
														if(array_key_exists('price',$val)){
															$price = $val['price'];
														}
														$str .= '<div class="col-xs-2 col-sm-2 price"><div class="form-group"><label for="">Option Price</label><input placeholder="Option price" name="options['.$key.'][price]" class="form-control" value="'.$price.'" required/></div></div>';
													}
													
													$weight = '';
													if(array_key_exists('weight',$val)){
														$weight = $val['weight'];
													}
													$str .= '<div class="col-xs-2 col-sm-2 weight"><div class="form-group"><label for="">Option Weight<span class="size_msg">(In LBS)</span></label><input placeholder="Option weight" name="options['.$key.'][weight]" class="form-control" value="'.$weight.'"/></div></div>';

													$flat_rate_additional_price = '';
													if(array_key_exists('flat_rate_additional_price',$val)){
														$flat_rate_additional_price = $val['flat_rate_additional_price'];
													}
													$str .= '<div class="col-xs-2 col-sm-2 weight"><div class="form-group"><label for="">Flat Rate Additional Price</span></label><input placeholder="Flat Rate Additional Price" name="options['.$key.'][flat_rate_additional_price]" class="form-control" value="'.$flat_rate_additional_price.'"/></div></div>';
													
													
													$str .= '<div class="col-xs-1 col-sm-1"><div class="form-group"> <label>&nbsp;</label><div class="clearfix"></div><button class="btn btn-danger remove-custom-option" type="button"><i class="fa fa-minus"></i></button></div></div><div class="clearfix"></div></div>';
													echo $str;
													$i++;
												}
												echo '<script> var _key = '.$key++.'</script>';
											}
											else{
												echo '<div class="row optionClone"><div class="col-md-5"><div class="form-group"><div class="input text"><label for="post-workshops-permissions-title">Price Per Character</label><input placeholder="Option value" name="options[][price]" class="form-control"  value="'.$data[0]['price'].'" required/></div></div></div><div class="clearfix"></div></div>';
											}
											?>
										</div>
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
								<textarea class="form-control" name="description" id="description" placeholder="Description"> {{ array_key_exists('description',old())?old('description'):$option->description }}</textarea>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group row">
							<label class="col-sm-3 form-control-label">&nbsp;</label>
							<div class="col-sm-4 offset-sm-2">
								<button type="submit" class="btn btn-primary">Update</button>
								<a href="{{url('/admin/products/custom/option/lists')}}" class="btn btn-warning">Back</a>
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
	<?php if($option->free == 0){ ?>	var paid = true;	<?php } else{ ?>	var paid = false;	<?php } ?>
	
	$('#paid_free_div ins').on('click',function(){
		var value = $(this).siblings('input');
		if (value.is(':checked')) {
			paid = false;
			$('div.custom_option_panel').find('.price').remove();
			$('input[name="options[][price]"]').val('').prop('required',false);
		} else {
			paid = true;
			$('div.custom_option_panel').find('.value').each(function() {
				var key = $(this).attr('data');
				var price_html = '<div class="col-xs-2 col-sm-2 price"><div class="form-group"><label for="">Option Price</label><input placeholder="Option price" name="options['+key+'][price]" class="form-control" required/></div></div>';
				$(this).after(price_html);
			});
			$('input[name="options[][price]"]').val('').prop('required',true);
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