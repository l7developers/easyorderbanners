<div class="modal fade" id="events">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-calendar"></i> Event</h4>
			</div>
			<div class="modal-body">
				<div class="row events_div">
					<div class="col-md-12 events_list"></div>
					<div class="col-md-12 edit_event_div" style="display:none;">
						{{ Form::model('events_edit', ['url' => ['admin/users/list'],'files'=>true,'id'=>'events_edit']) }}
							{{ Form::hidden('event_id','',['id'=>'event_id']) }}
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('date', 'Date',array('class'=>'form-control-label'))}}	
									{{ Form::text('date','',['class'=>'form-control','id'=>'date','placeholder'=>'Select date'])}}
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('title', 'Title',array('class'=>'form-control-label'))}}	
									{{ Form::text('title','',['class'=>'form-control','id'=>'title','placeholder'=>'Enter Title'])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::label('message', 'Message',array('class'=>'form-control-label'))}}	
									{{ Form::textarea('message','',['class'=>'form-control','id'=>'message','placeholder'=>'Enter message','rows'=>3])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::button('Update',['class'=>'btn btn-success edit_event'])}}
									<img id="event_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
								</div>
							</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.event_edit',function(e){
		$(".edit_event_div").slideDown( "slow");
	});
	
	$(document).on('click','.edit_event',function(e){
		var id = '{{\Auth::user()->id}}';
		var form_data = $('form#events_edit').serialize();
		$.ajax({
			url:'{{url("admin/home/events")}}',
			type:'post',
			data:{'data':form_data},
			dataType:'json',
			beforeSend: function() {
				$('#event_loader_img').fadeIn();
				$('.edit_event').prop('disabled', true);
			},
			success:function(data){
				if(data.status == 'success'){
					$('#event_loader_img').fadeOut();
					$('.edit_event').prop('disabled', false);
					window.location.reload();
				}
			}
		});
		
	});
	
	$( "#date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		minDate: 0,
	});
	
</script>