<div class="modal fade" id="order_notes">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-text-width"></i> Notes</h4>
			</div>
			<div class="modal-body">
				<div class="row notes_div">
					<div class="col-md-12 notes_list"></div>
					<div class="col-md-12">
						{{ Form::model('notes_add', ['url' => ['admin/order/list'],'files'=>true,'id'=>'notes_add']) }}
							{{ Form::hidden('note_user_id','',['id'=>'note_user_id']) }}
							{{ Form::hidden('note_order_id','',['id'=>'note_order_id']) }}
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::label('note', 'Note',array('class'=>'form-control-label'))}}	
									{{ Form::textarea('note','',['class'=>'form-control','id'=>'note','rows'=>4])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::button('Add note',['class'=>'btn btn-success add_note'])}}
									<img id="note_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
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
	$(document).on('click','.notes_btn',function(e){
		var id = '{{\Auth::user()->id}}';
		var order_id = $(this).attr('data');
		
		$('#note_user_id').val(id);
		$('#note_order_id').val(order_id);
		
		$.ajax({
			url:'{{url("admin/order/notes")}}',
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
					$('.notes_list').html(data.html);
					$('#order_notes').modal('show');
				}
			}
		});
		
	});
	
	$(document).on('click','.add_note',function(e){
		var id = '{{\Auth::user()->id}}';
		var form_data = $('form#notes_add').serialize();
		$.ajax({
			url:'{{url("admin/order/notes")}}',
			type:'post',
			data:{'type':'add','data':form_data},
			dataType:'json',
			beforeSend: function() {
				$('#note_loader_img').fadeIn();
				$('.add_note').prop('disabled', true);
			},
			success:function(data){
				if(data.status == 'success'){
					$('#note_loader_img').fadeOut();
					$('.add_note').prop('disabled', false);
					
					if($('div.notes_list div.box-body').length > 0){
						$('div.notes_list div.box-body').append(data.html);
					}
					else{
						$('div.notes_list').append('<div class="box box-solid"><div class="box-body">'+data.html+'</div></div>');
					}
					document.getElementById("notes_add").reset();
				}
			}
		});
		
	});
	
	$(document).on('click','.delete_note',function(e){
		var id = $(this).attr('data-id');
		var item_id = $(this).attr('data-item-id');
		var count = Number($('.note_count_'+item_id).html());
		if(confirm(" Are you sure to delete this?")){
			$.ajax({
				url:'{{url("admin/actions/delete")}}',
				type:'post',
				dataType:'json',
				data:{'table':'notes','id':id},
				beforeSend: function () {
					$('#note_loader_img').fadeIn();
					$('.add_note').prop('disabled', true);
				},
				complete: function () {
					$('#note_loader_img').fadeOut();
					$('.add_note').prop('disabled', false);
				},
				success:function(data){
					if(data.status == 'success'){
						$('.note_li_'+id).remove();
						if(count-- > 0)
							$('.note_count_'+item_id).html(count--);
						else
							$('.note_count_'+item_id).html('');
					}
				}
			});
		}
	});
	
</script>