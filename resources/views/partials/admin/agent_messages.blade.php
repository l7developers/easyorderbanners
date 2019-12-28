<div class="modal fade" id="agent_messages">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-comments-o"></i> Messages</h4>
			</div>
			<div class="modal-body">
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool messages_btn" title="Refresh"><i class="fa fa-refresh"></i></button>
				</div>
				<div class="row events_div">
					<div class="col-md-12">
						<div class="direct-chat direct-chat-success">
							<div class="box-body">
								<div class="direct-chat-messages messages_list"></div>
							</div>
							<div class="box-footer">
							{{ Form::model('message',['name'=>'message_form','id'=>'message_form']) }}
								<div class="input-group">
									{{ Form::hidden('message_agent_id','',['id'=>'message_agent_id']) }}
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
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.messages_btn',function(e){
		
		var agent_id = $(this).attr('data');
		$('#message_agent_id').val(agent_id);
		$('.messages_btn').attr('data',agent_id);
		
		$.ajax({
			url:'{{url("admin/home/messages")}}',
			type:'post',
			data:{'type':'list','agent_id':agent_id},
			dataType:'json',
			success:function(data){
				if(data.status == 'success'){
					$('.messages_list').html(data.html);
					$('#agent_messages').modal('show');
				}
			}
		});
		
	});
	
	$('#message_form').on('submit',function(){
		//alert(123);
		var message = $('#message').val();
		if( message != ''){
			$.ajax({
				url:'{{url("admin/home/messages")}}',
				type:'post',
				data:{'type':'add','from_id':'{{\Auth::user()->id}}','to_id':$('#message_agent_id').val(),'message':message},
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
	
	$( "#date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		minDate: 0,
	});
	
</script>