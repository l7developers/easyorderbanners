@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Add Discount</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">		
					<form role="form" method="POST" action="{{ url('admin/discount/add') }}">
					{{ csrf_field() }}
						<div class="col-sm-12">
							<div class="form-group row{{ $errors->has('quantity') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Quantity<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::text('quantity', old('quantity') ,['class'=>'form-control','placeholder'=>'Enter quantity'])}}
									@if ($errors->has('quantity'))
										<span class="help-block">{{ $errors->first('quantity') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('percent') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Percent<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">%</span>
										{{Form::text('percent', old('percent') ,['class'=>'form-control','placeholder'=>'Enter percent'])}}
									</div>
									@if ($errors->has('percent'))
										<span class="help-block">{{ $errors->first('percent') }}</span>
									@endif
									
								</div>
							</div>
							<div class="form-group row{{ $errors->has('product_type') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Products<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::select('product_type',[''=>'Select Product','1'=>'All','2'=>'Selected Product'] ,old('product_type') ,['class'=>'form-control','id'=>'product_type'])}}
									@if ($errors->has('product_type'))
										<span class="help-block">{{ $errors->first('product_type') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('products') ? ' has-error' : '' }} product_div {{(old('product_type') == 2)?'':'hide'}}">
								{{ Form::label('products[]', '&nbsp;',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">	{{Form::select('products[]',$products,old('products'),['class'=>'form-control select2','id'=>'products','multiple'])}}
									@if ($errors->has('products'))
										<span class="help-block">{{ $errors->first('products') }}</span>
									@endif
								</div>
							</div>
							<div class="line"></div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-6 offset-sm-2">
								<button type="submit" class="btn btn-primary">Save</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
$(document).on('change','#product_type',function(){
	var value = $(this).val();
	if(value == 2){
		$('.product_div').removeClass('hide');
		$('.product_div').slideDown();
		//$('#products').attr('required', true);
		$(".select2").select2({placeholder: "Select Values",});
	}else{
		$('.product_div').slideUp();
		//$('#products').attr('required', false);
		$("#products").val("");
	}
});
</script> 		  
@endsection		  