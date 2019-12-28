<div class="modal fade" id="assign_agent_model">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Assign Agent</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group assign_agent_div">
							{{Form::hidden('agent_order_id','',['id'=>'agent_order_id'])}}
							{{ Form::label('select_agent', 'Agent',array('class'=>'form-control-label'))}}	
							{{Form::select('select_agent', [''=>'Select Agent']+$agents, '',array('class'=>'form-control','id'=>'select_agent'))}}
							<span class="help-block"></span>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-xs-6">
						<div class="form-group">
							<button type="button" class="btn btn-success assign_agent">Assign Agent</button>
							<img id="agent_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.agent_btn',function(e){
		var id = $(this).attr('data');
		var order_id = $(this).attr('order-id');
		$('#agent_order_id').val(order_id);
		if(id != 0){
			$("#select_agent option[value='" + id + "']").attr("selected","selected");
		}
		else{
			$('#select_agent option:eq(0)').prop('selected', 'selected');
		}
	});
	$(document).on('click','.assign_agent',function(e){
		var agent_id = $('#select_agent').val();
		var order_id = $('#agent_order_id').val();
		if(agent_id != ''){
			$('div.assign_agent_div').removeClass('has-error');
			$('div.assign_agent_div span').html('');
			$.ajax({
				url:'{{url("admin/order/assign_agent")}}',
				type:'post',
				data:{'agent_id':agent_id,'order_id':order_id,'agent_name':$("#select_agent option:selected").text()},
				dataType:'json',
				beforeSend: function() {
					$('#agent_loader_img').fadeIn();
					$('.assign_agent').prop('disabled', true);
				},
				success:function(data){
					$('#agent_loader_img').fadeOut();
					$('.assign_agent').prop('disabled', false);
					if(data.status == 'success'){
						$('.agent_'+order_id).html(data.html);
						$('#assign_agent_model').modal('hide');
					}
				}
			});
		}else{
			$('div.assign_agent_div').addClass('has-error');
			$('div.assign_agent_div span').html('Select Agent');
		}
	});
</script>