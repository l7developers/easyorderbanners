<div class="modal fade" id="order_messages">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-comments-o"></i> Messages</h4>
			</div>
			<div class="modal-body">
				<div class="row events_div">
					<div class="col-md-12">
						<div class="direct-chat direct-chat-success">
							<div class="box-body">
								<div class="direct-chat-messages messages_list"></div>
							</div>
							<div class="box-footer">
								<div class="input-group">
									{{ Form::hidden('message_order_id','',['id'=>'message_order_id']) }}
									{{ Form::text('message','',['class'=>'form-control','id'=>'message','placeholder'=>'Type Message ...'])}}
									<span class="input-group-btn">
										{{ Form::button('Send',['class'=>'btn btn-warning btn-flat send_msg'])}}
									</span>
								</div>
								<div class="input-group">
									<img id="msg_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
								</div>
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
		
		var order_id = $(this).attr('data');
		$('#message_order_id').val(order_id);
		
		$.ajax({
			url:'{{url("admin/order/messages")}}',
			type:'post',
			data:{'type':'list','order_id':order_id},
			dataType:'json',
			success:function(data){
				if(data.status == 'success'){
					$('.messages_list').html(data.html);
					$('#order_messages').modal('show');
				}
			}
		});
		
	});
	
	$(document).on('click','.send_msg',function(e){
		var message = $('#message').val();
		if( message != ''){
			$.ajax({
				url:'{{url("admin/order/messages")}}',
				type:'post',
				data:{'type':'add','message':message,'order_id':$('#message_order_id').val()},
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
	});
	
	$( "#date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		minDate: 0,
	});
	
</script>