<div class="modal fade" id="assign_designer_model">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Assign Designer</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group assign_designer_div">
							{{Form::hidden('designer_order_id','',['id'=>'designer_order_id'])}}
							{{ Form::label('select_designer', 'Designer',array('class'=>'form-control-label'))}}	
							{{Form::select('select_designer', [''=>'Select Designer']+$designers, '',array('class'=>'form-control','id'=>'select_designer'))}}
							<span class="help-block"></span>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-xs-6">
						<div class="form-group">
							<button type="button" class="btn btn-success assign_designer">Assign Designer</button>
							<img id="designer_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.designer_btn',function(e){
		var id = $(this).attr('data');
		var order_id = $(this).attr('order-id');
		$('#designer_order_id').val(order_id);
		if(id != 0){
			$("#select_designer option[value='" + id + "']").attr("selected","selected");
		}
		else{
			$('#select_designer option:eq(0)').prop('selected', 'selected');
		}
	});
	$(document).on('click','.assign_designer',function(e){
		var designer_id = $('#select_designer').val();
		var order_id = $('#designer_order_id').val();
		if(designer_id != ''){
			$('div.assign_designer_div').removeClass('has-error');
			$('div.assign_designer_div span').html('');
			$.ajax({
				url:'{{url("admin/order/assign_designer")}}',
				type:'post',
				data:{'designer_id':designer_id,'order_id':order_id,'designer_name':$("#select_designer option:selected").text()},
				dataType:'json',
				beforeSend: function() {
					$('#designer_loader_img').fadeIn();
					$('.assign_designer').prop('disabled', true);
				},
				success:function(data){
					$('#designer_loader_img').fadeOut();
					$('.assign_designer').prop('disabled', false);
					if(data.status == 'success'){
						$('#designer_'+order_id).html(data.html);
						$('#assign_designer_model').modal('hide');
					}
				}
			});
		}else{
			$('div.assign_designer_div').addClass('has-error');
			$('div.assign_designer_div span').html('Select Designer');
		}
	});
</script>