@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>My Artwork</h2>
	</div>
</section>

<!-- File Upload Drag and Drop CSS and JS -->

<link href="{{ asset('public/css/front/fileinput.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css"/>
<link href="{{ asset('public/css/front/theme.css') }}" rel="stylesheet">
<script src="{{ asset('public/js/front/fileinput.js') }}"></script>

<!-- End File Upload Drag and Drop CSS and JS -->

<section class="product_cart">
	<div class="container">
		@include('partials.front.account_nav_bar')
		<div class="yourcart">
			<h4>My Artwork Files</h4>	{{Form::model('uploads_files',['url'=>url('/my-artwork-files-upload'),'files'=>true,'class'=>'contactpage','id'=>'uploads_files'])}}
			<div class="yourcartlist">
				<div class="row">
					<div class="col-sm-12 col-md-6 col-lg-8">
						<div class="cart_product mnone">
							<p>This area reserved for you to upload any additional art files. If you have requested our design service for your project, this is where you will need to upload you graphic elements. If you have made an order and uploaded your artwork files during your checkout process, you can find links to those files below.
						</div>
						<div class="myartworkfiles">
							@foreach($artworkFiles as $myfiles)
							<div class="col-xs-12 myartworkfileslist">
								<b>Project Name : {{$myfiles->project_name}}</b><br/>
								File Path : <a href="{{$myfiles->files_url}}">{{$myfiles->files_url}}</a><br/> 
								Comment : {{$myfiles->comment}}<br/> 
								Uploaded On : {{$myfiles->created_at}}<br/> 
								<a href="{{url('my-artwork-files-delete/'.$myfiles->id)}}" class="btn btn-danger btn-xs">Delete</a><br/> 
							</div>
							@endforeach
						</div>
					</div>
					<div class="col-sm-12 col-md-6 col-lg-4 no-padding">
						<div class="form-group col-sm-12 upload_img1 file_upload no-padding{{ $errors->has('files') ? ' has-error' : '' }}">
							<div class="file-loading">	{{Form::file('files[]',['class'=>'file','multiple'=>false,'id'=>'files','data-min-file-count'=>'1','data-show-upload'=>'false','data-show-caption'=>'true','data-msg-placeholder'=>'Select {files} for upload...','required'])}}
							</div>
							<span class="error_msg error_files">{{ $errors->first('files') }}</span>
						</div>
						<div class="form-group col-sm-12 no-padding progressDiv" style="display:none;">
							<h5>Files Uploading...</h5>
							<div class="row">
								<p style="font-size:12px;padding:0 20px;font-style: italic;">Please do not close or refresh this page until your file uploads have completed. You will be redirected to your account page upon successful upload..</p>
							</div>
							<div class="progress">
								<div class="progress-bar progress-bar-success progress-bar-striped active percent" role="progressbar" style="width:0%"></div>
							</div>
						</div>
						<div class="form-group col-sm-12 no-padding{{ $errors->has('project_name') ? ' has-error' : '' }}">
							<label>Project Name</label>	{{Form::text('project_name',old('project_name'),['class'=>'form-control','placeholder'=>'Enter Project Name'])}}
							<span class="error_msg error_project_name">{{ $errors->first('project_name') }}</span>
						</div>
						<div class="form-group col-sm-12 no-padding{{ $errors->has('comment') ? ' has-error' : '' }}">
							<label>Comment</label>	{{Form::textarea('comment',old('project_name'),['class'=>'form-control','placeholder'=>'Enter Comment','rows'=>3])}}
							<span class="error_msg error_comment">{{ $errors->first('comment') }}</span>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="space"></div>
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12">
					<div class="right_text">
						<div id="order_image_div" class="form-group">
							<i class="fa fa-spinner fa-spin hide loderSpin" style="font-size:24px"></i>
							{{Form::submit('Upload',['class'=>'btn btn-default custom_btn','id'=>'upload'])}}				
						</div>
					</div>
				</div>
			</div>
			{{Form::close()}}
		</div>
	</div>
</section>

<style type="text/css">
.myartworkfileslist
{
	padding: 15px 0px;
    line-height: 23px;
}    
</style>
<script>
var csrf_token = '{{csrf_token()}}';
//alert(csrf_token)
//alert('{{url("/my-artwork-files-upload") }}');
$(".file").fileinput({
	'showUpload':false,
	'allowedFileExtensions' : ['eps','pdf','jpg','id','png','psd','ai','doc','docx','zip','indd','webp','psb','tif','tiff','tga','vda','ibc','vst','raw','jpf','jpx','jp2','j2c','j2k','jpc','iff','bmp'],
	'uploadUrl':'{{ URL::To("my-artwork-files-upload") }}',
	'uploadAsync': false,
    'uploadExtraData': function() {
        return {
            'X-CSRF-TOKEN': csrf_token,
        };
    },
	'required': true,
	'defaultPreviewContent':'<a href="javascript:void(0)" class="upload open_brows"><img src="'+"{{URL::to('public/img/front/upload_icon.jpg')}}"+'" alt="Your Avatar"></a><strong>Drag &amp; Drop File Here to Upload</strong><span style="color:#7ac043;font-size:12px"><i>Only one file per job allowed per upload.</i></span>',
	'previewFileType':'any'
}).on('fileuploaded', function(event, data, previewId, index) {

        console.log(data);

    }).on('filebatchuploadsuccess', function(event, data, previewId, index) {
		console.log(data)
    }).on("filebeforedelete", function() {

        return !window.confirm('Are you sure you want to delete this file?');

    });

$('.file_upload').on('drop', function(e){
	$('#files')[0].files = e.originalEvent.dataTransfer.files;
	$('.file-preview-other-frame').css({'width':'0px','height':'0px'});
	setTimeout(function(){$('.kv-file-content embed').css({'width':'100px','height':'70px'});},100);
});


$(document).on('change','.upload_img1 input[name="files[]"]',function(){
	setTimeout(function(){$('.kv-file-content embed').css({'width':'100px','height':'70px'});},100);
	$('.file-preview-other-frame').css({'width':'0px','height':'0px'});
});


$(document).on('click','.open_brows',function(){
	$('input[name="files[]"]').trigger("click");
});
</script>

<script src="{{ asset('public/js/front/jquery.form.js') }}"></script>

<!--<script src="https://malsup.github.com/jquery.form.js"></script>-->
<script>
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
			$('.error_msg').html('');
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
				window.location.href = "{{url('/my-artwork-files')}}";
			}else{
				var percentVal = data.error_msg;
				percent.removeClass('progress-bar-success');
				percent.addClass('progress-bar-danger');
				
				$.each(data.error_messages, function(k,v){
					$('.error_'+k).html(v);
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