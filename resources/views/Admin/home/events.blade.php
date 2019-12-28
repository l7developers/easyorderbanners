@extends('layouts.admin_layout')
@section('content')

<section class="content-header">
  <h1>
	Events
	<small>Control panel</small>
  </h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body no-padding">
					<div id="calendar"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
var events_array = <?php echo json_encode($events,JSON_PRETTY_PRINT);?>;
var event_js_arr=new Array();
$.each(events_array, function (index, value) {  
  var obj=new Object();
  obj.id=value.id;
  if(value.order_id != null){
	  obj.type = 'order';
  }
  else if(value.customer_id != null){
	  obj.type = 'customer';
  }
  
  obj.start=new Date(value.date);
  obj.order_id=value.order_id;
  obj.title=value.title;
  obj.user_name=value.user_name;
  obj.customer_name=value.customer_name;
  obj.message=value.message;
  obj.backgroundColor="#f56954";
  obj.borderColor="#f56954";
  event_js_arr.push(obj);
  
});   
//console.log(event_js_arr);


$(function () {		

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date()
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()
	//alert(m);
    $('#calendar').fullCalendar({
      header    : {
        left  : 'prev,next today',
        center: 'title',
        //right : 'month,agendaWeek,agendaDay'
        //right : 'month'
      },
      buttonText: {
        today: 'today',
        month: 'month',
        week : 'week',
        day  : 'day'
      },
      //Random default events
      events    : event_js_arr,
      editable  : false,
      droppable : true, // this allows things to be dropped onto the calendar !!!
      drop      : function (date, allDay) { // this function is called when something is dropped
			
        // retrieve the dropped element's stored Event Object
        var originalEventObject = $(this).data('eventObject')

        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject)

        // assign it the date that was reported
        copiedEventObject.start           = date
        copiedEventObject.allDay          = allDay
        copiedEventObject.backgroundColor = $(this).css('background-color')
        copiedEventObject.borderColor     = $(this).css('border-color')

        // render the event on the calendar
        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)

        // is the "remove after drop" checkbox checked?
        if ($('#drop-remove').is(':checked')) {
          // if so, remove the element from the "Draggable Events" list
          $(this).remove()
        }

      },
      eventClick: function(calEvent, jsEvent, view) {
			var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
							];
			var year,month,day,date,date_name;
			var dateObj = new Date(calEvent.start);
			year    = dateObj.getFullYear ();
			month   = dateObj.getMonth ();
			day     = dateObj.getDate ();
			date = year+'-'+("0" + (month + 1)).slice(-2)+'-'+day;
			date_name = day+'-'+monthNames[month]+'-'+year;
			//alert('Event: ' + calEvent.message);
			//console.log(calEvent);

			var id = '{{\Auth::user()->id}}';
			var event_id = calEvent.id;
			var user_name = calEvent.user_name;
			var customer_name = calEvent.customer_name;
			
			var str = '<div class="box box-solid"><div class="box-body"><li><i class="fa fa-calendar-plus-o"></i> '+date_name+'<button type="button" class="btn btn-xs btn-danger pull-right event_delete"><i class="fa fa-trash"></i></button>&nbsp;<button type="button" class="btn btn-xs btn-info pull-right event_edit"><i class="fa fa-edit"></i></button><br/>';
			if(calEvent.type == 'order'){
				var order_id = calEvent.order_id;
				var orderId = order_id.split("-");
				var link = '{{url("admin/order/edit/")}}/'+orderId[0];
				str += '<b><a href="'+link+'">#'+order_id+'</b></a><br/>';
				str += '<b>Created By : </b>'+user_name+'<br/>';
			}else if(calEvent.type == 'customer'){
				str += '<b>Created By : </b>'+user_name+'<br/>';
				str += '<b>Customer Name : </b>'+customer_name+'<br/>';
			}
			
			str += '<b>'+calEvent.title+'</b><br/>'+calEvent.message+'</li></div></div>';
			
			$('.edit_event_div #event_id').val(event_id);
			$('.edit_event_div #date').val(date);
			$('.edit_event_div #title').val(calEvent.title);
			$('.edit_event_div #message').val(calEvent.message);
			
			$('.events_list').html(str);
			
			$(".edit_event_div").slideUp();
			$('#events').modal('show');
     }
    })

  });
	
	$(document).on('click','.event_delete',function(e){
		var id = $('.edit_event_div #event_id').val();
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
						window.location.reload();
						//$('#events').modal('hide');
					}
				}
			});
		}
	});

</script> 

@include('partials.admin.event')


@endsection		  