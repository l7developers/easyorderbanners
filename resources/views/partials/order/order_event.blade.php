<div class="modal fade" id="order_events">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-calendar"></i> Events</h4>
			</div>
			<div class="modal-body">
				<div class="row events_div">
					<div class="col-md-12 events_list"></div>
					<div class="col-md-12">
						{{ Form::model('events_add', ['url' => ['admin/order/list'],'files'=>true,'id'=>'events_add']) }}
							{{ Form::hidden('event_user_id','',['id'=>'event_user_id']) }}
							{{ Form::hidden('event_order_id','',['id'=>'event_order_id']) }}
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
									{{ Form::button('Add Event',['class'=>'btn btn-success add_event'])}}
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
	$(document).on('click','.events_btn',function(e){
		var id = '{{\Auth::user()->id}}';
		var order_id = $(this).attr('data');
		
		$('#event_user_id').val(id);
		$('#event_order_id').val(order_id);
		
		$.ajax({
			url:'{{url("admin/order/events")}}',
			type:'post',
			data:{'type':'list','user_id':id,'order_id':order_id},
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			dataType:'json',
			success:function(data){
				if(data.status == 'success'){
					$('.events_list').html(data.html);
					$('#order_events').modal('show');
				}
			}
		});
		
	});
	
	$(document).on('click','.add_event',function(e){
		var id = '{{\Auth::user()->id}}';
		var form_data = $('form#events_add').serialize();
		$.ajax({
			url:'{{url("admin/order/events")}}',
			type:'post',
			data:{'type':'add','data':form_data},
			dataType:'json',
			beforeSend: function() {
				$('#event_loader_img').fadeIn();
				$('.add_event').prop('disabled', true);
			},
			success:function(data){
				if(data.status == 'success'){
					$('#event_loader_img').fadeOut();
					$('.add_event').prop('disabled', false);
				
					if($('div.events_list div.box-body').length > 0){
						$('div.events_list div.box-body').append(data.html);
					}
					else{
						$('div.events_list').append('<div class="box box-solid"><div class="box-body">'+data.html+'</div></div>');
					}
					document.getElementById("events_add").reset();
				}
			}
		});
		
	});
	
	$(document).on('click','.event_delete',function(e){
		var id = $(this).attr('data-id');
		var item_id = $(this).attr('data-item-id');
		if(confirm(" Are you sure to delete this?")){
			$.ajax({
				url:'{{url("admin/actions/delete")}}',
				type:'post',
				dataType:'json',
				data:{'table':'events','id':id},
				beforeSend: function () {
					$('#event_loader_img').fadeIn();
					$('.add_event').prop('disabled', true);
				},
				complete: function () {
					$('#event_loader_img').fadeOut();
					$('.add_event').prop('disabled', false);
				},
				success:function(data){
					if(data.status == 'success'){
						$('.event_li_'+id).remove();
					}
				}
			});
		}
	});
	
	$( "#date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		minDate: 0,
	});
	
</script>