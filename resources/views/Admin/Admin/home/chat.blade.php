@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>
	Chat
  </h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-success direct-chat direct-chat-success">
				<div class="box-header with-border">
					<i class="fa fa-comments-o"></i>
					<h3 class="box-title">Messages</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool refresh" title="Refresh"><i class="fa fa-refresh"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="direct-chat-messages messages_list">
					@php
						if(!empty($messages->toArray())){
							foreach($messages as $val){
								$class = 'pull-left';
								if($val->from_id == \Auth::user()->id){
									$class = 'pull-right right';
								}
								echo '<div class="direct-chat-msg '.$class.'" style="width:40%;"><div class="direct-chat-info clearfix"><span class="direct-chat-name pull-left">'.$val->sender_name.'</span><span class="direct-chat-timestamp pull-right">('.date('d F H:i A',strtotime($val->date)).')</span></div><div class="direct-chat-text">'.htmlentities($val->message).'</div></div><div class="clearfix"></div>';
							}
						}
					@endphp
					</div>
				</div>
				@if(\Auth::user()->role_id == 2)
				<div class="box-footer">
				{{ Form::model('message',['name'=>'message_form','id'=>'message_form']) }}
					<div class="input-group">
						{{ Form::text('message','',['class'=>'form-control','id'=>'message','placeholder'=>'Type Message ...'])}}
						<span class="input-group-btn">
							{{ Form::button('Send',['type'=>'submit','class'=>'btn btn-warning btn-flat send_msg'])}}
						</span>
					</div>
					<div class="input-group">
						<img id="msg_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
					</div>
				{{ Form::close() }}
				</div>
				@endif
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</section>

<script>
$('#message_form').on('submit',function(){
	//alert(123);
	var message = $('#message').val();
	if( message != ''){
		$.ajax({
			url:'{{url("admin/home/messages")}}',
			type:'post',
			data:{'type':'add','from_id':'{{\Auth::user()->id}}','message':message},
			dataType:'json',
			beforeSend: function() {
				$('#msg_loader_img').fadeIn();
				$('.send_msg').prop('disabled', true);
			},
			success:function(data){
				$('#msg_loader_img').fadeOut();
				$('.send_msg').prop('disabled', false);
				if(data.status == 'success'){
					$('div.messages_list').append(data.html);
					$('#message').val('');
				}
			}
		});
	}else{
		
	}
	return false;
});

$('.refresh').on('click',function(){
	
	$.ajax({
		url:'{{url("admin/home/messages")}}',
		type:'post',
		data:{'type':'list','agent_id':'{{\Auth::user()->id}}'},
		dataType:'json',
		success:function(data){
			if(data.status == 'success'){
				$('.messages_list').html(data.html);
				$('#agent_messages').modal('show');
			}
		}
	});
});
</script> 
@endsection		  