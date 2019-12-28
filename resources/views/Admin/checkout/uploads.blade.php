@extends('layouts.app')

@section('content')

<!-- File Upload Drag and Drop CSS and JS -->

<link href="{{ asset('public/css/front/fileinput.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css"/>
<link href="{{ asset('public/css/front/theme.css') }}" rel="stylesheet">
<script src="{{ asset('public/js/front/fileinput.js') }}"></script>

<!-- End File Upload Drag and Drop CSS and JS -->

<section class="product_cart">
	<div class="container">
		<div class="product_cart_title">
			If you have any questions about checkout or the order process, <br> please contact us at {{config('constants.site_phone_number')}}. We’re here to help!
		</div>
		<div class="cart_tab">
			<ul>
				<li class="active"> <a href="javascript:void(0)"><span>1</span> Review Cart</a></li>
				<li class="active"> <a href="javascript:void(0)"><span>2</span> Billing, shipping & Payment </a></li>
				<li class="active"> <a href="javascript:void(0)"><span>3</span> Art Uploads</a></li>
			</ul>
		</div>
		@if(isset($_GET['msg']))
		<div class="cart_tab">
			<div class="text-success"><h3>{{$_GET['msg']}}</h3></div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="clt_review">
					<h3>Your Order submitted Successfully!</h3>
					<em> <b>Thank you for order !</b></em>
				</div>
			</div>
		</div>
		@endif
		
		<div class="yourcart">
			<h4>Upload Your Files</h4>
			<div class="well well-info">
				<div class="icon"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
				<div class="infoMsg">
					<b>info: </b>Please upload your art below for each product. Only one art file per line item. <b>For 2-sided products with different art on each side, you MUST create a 2 page pdf file to upload, (one art file on page 1 and one art file on page 2. If you cannot make this change, do not worry, we’re here to help!  Please contact us 800-920-9527 and we’ll do it for you at no charge.  If your double sided product is to be printed with the same art on both sides, simply upload 1 file and you’re done.</b><br/><br/>
					
					Once you upload all your art files, our design team will email you a link to view/approve your proof. You will not see a proof on this website. All our proofing is done off site via email. You will get a notification within 24 hours of placing your order about your proof. If we are doing the design for you or if you have questions about the process, just choose “Skip Upload” and we’ll be in touch to go over the design details and collect any files you have for the design.<br/><br/>
					
					<b>As always if you have questions, please call us, we’re here to help! 800-920-9527</b>
				</div>
			</div>
			{{Form::model('uploads_files',['url'=>url('upload-fiels/'.$id),'files'=>true,'class'=>'contactpage','id'=>'uploads_files'])}}
			@foreach($order->orderProduct as $val)

				@php
				if($no_artwork_required[$val->product_id] == 1)
					continue;
					
					$FilesNames = [];
					foreach($order->files as $v){
						$FilesNames[$v['order_product_id']][$v['side']] = $v['name'];
					}
					//pr($FilesNames);die;
				@endphp

				<div class="yourcartlist">
					<div class="row">
						<div class="col-sm-6 col-md-6 col-lg-4">
							<div class="cart_product mnone">
								<h5>Product</h5>
								<div class="row">
									<div class="col-xs-6 col-sm-4">
										<div class="product_item">
											@php
											$img_url = url('/public/img/front/img.jpg');
											if(@getimagesize(url('public/uploads/product/'.$val->product->image))){
												$img_url = url('/public/uploads/product/'.$val->product->image);
											}
											@endphp
											<img src="{{$img_url}}" alt="{{$val->product->image_title}}" title="{{$val->product->image_title}}" /> <br/>
										
										</div>
									</div>
									<div class="col-xs-6 col-sm-8">
										<span> {{$val->product->catgory->name}}</span>
										<p>
											@if($val->product_name !="")		
												{{$val->product_name}}
											@else
												{{$val->product->name}}
											@endif
										</p>
									</div>
									<div class="col-xs-12">
										<strong>Project Name:</strong>{{(!empty($val->project_name)?$val->project_name:'None')}}<br/>
										<strong>Comments:</strong>	{{(!empty($val->comments)?$val->comments:'None')}}<br/>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-6 col-lg-4">
							<div class="cart_options">
								<h5>Options</h5>
								<ul class="style_none">
									{!!$val->description!!}
									@foreach($order->orderProductOptions as $options)
										@if($val->id == $options->order_product_id)
											@if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height')
											<li>{{$options->custom_option_name.'(ft)'}}: {{$options->value}}</li>
											@else
												<li>{{$options->custom_option_name}}: {{$options->value}}</li>
											@endif	
										@endif
									@endforeach
								</ul>
							</div>
						</div>
						@if($no_artwork_required[$val->product_id] != 1)
						<div class="col-sm-12 col-md-12 col-lg-4 no-padding">
							@php
								$upload = 0;
								$file_name = '';
								$nameArr = [];
								foreach($order->files as $file){
									if($file->order_product_id == $val->id){
										$upload = 1;
										$file_name = $file->name;
										$nameArr = explode('.',$file_name);
									}
								}
							@endphp
							@if($upload)
								<div class="col-sm-12 upload_img1 file_upload no-padding" data-id="{{$val->id}}">
									@if(in_array($nameArr[1],['AI','ai','EPS','eps']))	
										<div class="file-loading">				{{Form::file('files['.$val->id.'][]',['class'=>'uploaded_file','data-url'=>$file_name])}}
										</div>
									@else
										<strong>File Uploaded Successfully.</strong>
									@endif
								</div>
							@else
								<div class="col-sm-12 upload_img1 file_upload dropzone no-padding file_uploadDiv_{{$val->id}}_0" data-id="{{$val->id}}" data-key="0">
									<div class="file-loading">	{{Form::file('files['.$val->id.'][0]',['class'=>'file file_input_'.$val->id.'_0','multiple'=>false,'id'=>'filesInput_'.$val->id.'_1','data-min-file-count'=>'1','data-show-upload'=>'false','data-show-caption'=>'true','data-msg-placeholder'=>'Select {files} for upload...','data-key'=>0,'required'=>true])}}
									</div>
									<span class="error_msg">			@if($errors->has('files.'.$val->id.'.0'))
										{{ $errors->first('files.'.$val->id.'.0') }}
									@endif
									</span>
								</div>
								
								<div class="col-sm-12 text-right no-padding">
									<a href="javascript:void(0)" class="skipupload" data-id="{{$val->id}}" data-key="0" data-skip="0">Skip Upload</a>
								</div>
							@endif
						</div>
						@endif
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="space"></div>
			@endforeach	
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12 progressDiv" style="display:none;">
					<h5>Files Uploading...</h5>
					<div class="row">
						<p style="font-size:12px;padding:0 20px;font-style: italic;">Please do not close or refresh this page until your file uploads have completed. You will be redirected to your account page upon successful upload..</p>
					</div>
					<div class="clearfix"></div>
					<div class="progress">
						<div class="progress-bar progress-bar-success progress-bar-striped active percent" role="progressbar" style="width:0%"></div>
					</div>
				</div>
				<div class="col-sm-12 col-md-12 col-lg-12">
					<div class="right_text">
						<div id="order_image_div" class="form-group">
							<i class="fa fa-spinner fa-spin hide loderSpin" style="font-size:24px"></i>
							
							<!--<a href="{{url('/order/view/'.$id.'/print')}}" class="update">Print/View Your Order</a>-->
							
							{{Form::submit('Proceed',['class'=>'btn btn-default custom_btn','id'=>'upload'])}}				
						</div>
					</div>
				</div>
			</div>
			{{Form::close()}}
		</div>
	</div>
</section>

<script>
$('.skipupload').click(function(){
	var id = $(this).attr('data-id');
	var key = $(this).attr('data-key');
	var skip = $(this).attr('data-skip');
	if(skip == 0){
		$(this).addClass('skipSelect');
		$(this).attr('data-skip',1);
		$('input[name="files['+id+']['+key+']"].file_input_'+id+'_'+key).prop('required',false);
	}else{
		$(this).removeClass('skipSelect');
		$(this).attr('data-skip',0);
		$('input[name="files['+id+']['+key+']"].file_input_'+id+'_'+key).prop('required',true);
	}
});

$(".uploaded_file").each(function(i,v){
	//alert($(this).attr('data-url'));
	var url = $(this).attr('data-url');
	$(this).fileinput({
        theme: 'fa',
        showUpload: false,
        showCaption: false,
        browseClass: "btn btn-primary btn-lg",
        fileType: "any",
        previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
        overwriteInitial: false,
        initialPreviewAsData: true,
        initialPreview: ['{{URL::to("public/uploads/orders/")}}/'+url],
		layoutTemplates: {
			//main2: '{preview}{browse} {upload}'
			main2: '{preview}'
		},  
		fileActionSettings: {
		  showDrag: false,
		  showZoom: true,
		  showUpload: false,
		  showDelete: false,
		},
    });
});

$(".file").fileinput({
	'showUpload':true,
	'maxFileCount':1,
	'autoReplace' : true,
	'initialPreviewAsData': false,
	//'previewFileIcon': "<i class='glyphicon glyphicon-king'></i>",
	'allowedFileExtensions' : ['eps','pdf','jpg','id','png','ai'],
	'uploadUrl':'{{url("uploads/".$id)}}',
	'required': true,
	'defaultPreviewContent':'<a href="javascript:void(0)" class="upload open_brows"><img src="'+"{{URL::to('public/img/front/upload_icon.jpg')}}"+'" alt="Your Avatar"></a><strong>Drag &amp; Drop File Here to Upload</strong><span style="color: #7c7c7c;font-size:12px;"><i style="color: #8bc242;"><b style="color: red;">Only One Art File per Job Allowed. </b><br>For 2-sided products with different art on each side please see “info” above. If you have questions, please call us, we’re here to help! 800-920-9527.</i><br><i>Acceptable file formats: EPS, PDF, JPG, PNG</i></span>',
	'previewFileType':'any'
});

$('.dropzone').on('drop', function(e){
	if(e.originalEvent.dataTransfer.files.length < 2){
		var id = $(this).find('input[type="file"]').attr('id');
		var key = $(this).find('input[type="file"]').attr('data-key');
		$('#'+id)[0].files = e.originalEvent.dataTransfer.files;
		$('.file-preview-other-frame').css({'width':'0px','height':'0px'});
		setTimeout(function(){$('.kv-file-content embed').css({'width':'100px','height':'70px'});},100);
	}
});


$(document).on('click','.open_brows',function(){
	var id = $(this).closest('div.file_upload').attr('data-id');
	var key = $(this).closest('div.file_upload').attr('data-key');
	$('.file_input_'+id+'_'+key).trigger("click");
});

$(document).on('change','.upload_img1 input[type="file"]',function(){
	setTimeout(function(){$('.kv-file-content embed').css({'width':'100px','height':'70px'});},100);
	$('.file-preview-other-frame').css({'width':'0px','height':'0px'});
});


</script>

<script src="{{ asset('public/js/front/jquery.form.js') }}"></script>

<!--<script src="https://malsup.github.com/jquery.form.js"></script>-->

<script type="text/javascript">
 
(function() {

	var percent = $('.percent');
	
	$('form').ajaxForm({
		beforeSend: function() {
			var percentVal = '0%';
			percent.width(percentVal)
			percent.html(percentVal);
			$('.loderSpin').removeClass('hide');
			percent.removeClass('progress-bar-danger');
			percent.addClass('progress-bar-success');
			$('#upload').prop("disabled", true);
		},
		uploadProgress: function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			$('.progressDiv').fadeIn(500)
			percent.width(percentVal);
			if(percentComplete < 100)
				percent.html(percentVal);
			else
				percent.html("Please wait for completion, do not leave page.");
		},
		success: function(data) {
			data = jQuery.parseJSON(data);
			$('.loderSpin').addClass('hide');
			$('#upload').prop("disabled", false);
			
			if(data.status){
				var percentVal = data.success_msg;
				window.location.href = "{{url('/orders')}}";
			}else{
				var percentVal = data.error_msg;
				percent.removeClass('progress-bar-success');
				percent.addClass('progress-bar-danger');
				
				$.each(data.error_messages, function(k,v){
					$.each(v, function(k1,v1){
						$('.file_uploadDiv_'+k+'_'+k1).find('span.error_msg').html(v1);
					});
				});
				percent.html(percentVal);
			}
		},
		complete: function(xhr) {
			var data = jQuery.parseJSON(xhr.responseText);
		}
	});
 
})();

$('input[type="file"]').on('change',function(){
	//alert($(this).closest('.file_upload .file-input').find('.file-caption input').val());
	var _this = $(this);
	setTimeout(function(){
		var fileName = _this.closest('.file_upload .file-input').find('.file-caption input').val();
		fileName = fileName.split('.');
		
		$('.krajee-default .file-other-icon').css({'font-size':'0em'});
		
		if(fileName[1] == 'ai' || fileName[1] == 'AI'){
			_this.closest('.file_upload .file-input').find('.file-preview-other span.file-other-icon').html('<img style="width:66px" src="'+"{{URL::to('public/img/front/ai.png')}}"+'" />');
		}else if(fileName[1] == 'eps' || fileName[1] == 'EPS'){
			_this.closest('.file_upload .file-input').find('.file-preview-other span.file-other-icon').html('<img style="width:66px" src="'+"{{URL::to('public/img/front/eps.png')}}"+'" />');
		}
	},500);
});
</script>

@endsection
