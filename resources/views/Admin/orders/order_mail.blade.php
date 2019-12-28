@extends('layouts.admin_layout')
@section('content')

<!-- File Upload Drag and Drop CSS and JS -->

<link href="{{ asset('public/css/front/fileinput.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css"/>
<link href="{{ asset('public/css/front/theme.css') }}" rel="stylesheet">
<script src="{{ asset('public/js/front/fileinput.js') }}"></script>

<!-- End File Upload Drag and Drop CSS and JS -->

<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Order/Estimate Mail</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/order/edit/'.$order->id)}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
			</div>
		</div>
	</div>
</section>

<section class="invoice">
	<div class="row">
		<div class="col-xs-12">
		{{Form::model('order_mail_form',['id'=>'order_mail_form'])}}
			<div class="box box-primary">
				<div class="box-header with-border">
				  <h3 class="box-title">Send Order Detail To Customer</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon" style="min-width:100px">From:</span>
							{{Form::text('from',config('constants.ADMIN_MAIL'),['class'=>'form-control validate'])}}
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon" style="min-width:100px">To:</span>
							{{Form::text('to',$order->customer->email,['class'=>'form-control validate'])}}
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon" style="min-width:100px">CC:</span>
							{{Form::text('cc','',['class'=>'form-control'])}}
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon" style="min-width:100px">Subject:</span>
							{{Form::text('subject',$subject,['class'=>'form-control validate'])}}
						</div>
					</div>
					<div class="form-group">
						<textarea id="message" name="message" class="form-control" style="height: 300px"> {!! $mailHtml !!}						
						</textarea>
					</div>
					<div class="form-group col-sm-5 col-xs-6 upload_img1 file_upload">
						<h3>Attached PDF File</h3>
						{{Form::hidden('file_name',$pdf)}}
						<div class="file-loading">
							{{Form::file('files',['class'=>'uploaded_file'])}}
						</div>
					</div>
				</div>
				<!-- /.box-body -->
				<div class="box-footer">
					<div class="pull-right">
						<!--<button type="button" class="btn btn-default"><i class="fa fa-pencil"></i> Draft</button>-->
						<button type="button" class="btn btn-primary send"><i class="fa fa-envelope-o"></i> Send</button>
					</div>
					<!--<button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>-->
				</div>
				<!-- /.box-footer -->
			</div>
			{{Form::close()}}
		</div>
	</div>
</section>

<script>
$('.uploaded_file').fileinput({
	theme: 'fa',
	showUpload: false,
	showCaption: false,
	browseClass: "btn btn-primary btn-lg",
	fileType: "any",
	previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
	overwriteInitial: false,
	initialPreviewAsData: true,
	initialPreview: ['{{URL::to("public/pdf/front/order_receipt/".$pdf)}}'],
	initialPreviewConfig: [
			{type: "pdf", downloadUrl: '{{URL::to("public/pdf/front/order_receipt/".$pdf)}}'}, // disable download
		],
	layoutTemplates: {
		//main2: '{preview}{browse} {upload}'
		main2: '{preview}'
	},  
	fileActionSettings: {
	  showDrag: false,
	  showZoom: true,
	  showUpload: false,
	  showDelete: false,
	  showRemove: false,
	},
	pluginOptions:{
		showRemove : false
	}
});
$('button.fileinput-remove').remove();

$(document).on('click','.send',function(){
	var check = 1;
	$('.validate').each(function(){
		$(this).closest('.form-group').removeClass('has-error');
		$(this).closest('.input-group').nextAll().remove();
		if($(this).val() == ''){
			check = 0;
			$(this).closest('.form-group').addClass('has-error');
			$("<span class='help-block'>this is required</span>").insertAfter($(this).closest('.input-group'));
			$(this).focus();
		}
	});
	
	if(check == 1){
		var message = CKEDITOR.instances.message.getData();
		$('textarea').val(message);
		
		$.ajax({
			url:'{{url("admin/order/order-mail/".$order->id)}}',
			type:'post',
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			data:$('form#order_mail_form').serialize(),
			success:function(data){
				if(data.status){
					window.location.href = '{{url("admin/order/edit/".$order->id)}}';
					//window.close();
				}else{
					alert("Mail not send to vendor properly, please try again.");
				}
			}
		});
	}
});

  $(function () {
    CKEDITOR.replace( 'message', {
		filebrowserBrowseUrl: 'http://192.168.1.77/easyorderbanner/public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
    toolbarGroups: [
				{"name":"basicstyles","groups":["basicstyles","clipboard","tools"]},
				{"name":"links","groups":["links"]},
				{"name":"paragraph","groups":["list","blocks"]},
				{"name":"document","groups":["mode"]},
				{"name":"insert","groups":["insert"]},
				{"name":"styles","groups":["styles"]}
			],
	// Remove the redundant buttons from toolbar groups defined above.
	removeButtons: 'Strike,Subscript,Superscript,Anchor,Styles,Specialchar',
    height: '300',
    });

  });
</script>

@endsection		  