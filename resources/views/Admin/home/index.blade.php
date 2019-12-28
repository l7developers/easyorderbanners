@extends('layouts.admin_layout')
@section('content')

<!--    Jquery for top scroll and table scroll drag ------------>
<script src="{{ asset('public/js/admin/dragscroll.js') }}"></script>
<script src="{{ asset('public/js/admin/top_scroll.js') }}"></script>


<section class="content-header">
  <h1>
	Dashboard
	<small>Control panel</small>
  </h1>
</section>

<section class="content">
	<div class="row coustomer_btn">
		<div class="col-lg-3 col-xs-6">
			<div class="small-box">
				<a href="{{url('admin/users/lists')}}" class="btn btn-block btn-success btn-lg">Customers List</a>
			</div>
		</div>
		@if(\Auth::user()->role_id == 1)
		<div class="col-lg-3 col-xs-6">
			<div class="small-box">
				<a href="{{url('admin/products/lists')}}" class="btn btn-block btn-warning btn-lg">Products List</a>
			</div>
		</div>
		@endif
		<div class="col-lg-3 col-xs-6">
			<div class="small-box">
				<a href="{{url('admin/order/lists')}}" class="btn btn-block btn-primary btn-lg">All Orders</a>
			</div>
		</div>
		<div class="col-lg-3 col-xs-6">
			<div class="small-box">
				<a href="{{url('admin/order/estimates')}}" class="btn btn-block btn-primary btn-lg">All Estimate</a>
			</div>
		</div>
		<div class="col-lg-3 col-xs-6">
			<div class="small-box">
				<a href="{{url('admin/order/add')}}" class="btn btn-block btn-info btn-lg">Create Estimate</a>
			</div>
		</div>
	</div>
	<br/>
	<div class="row">
		@php
			if($sort == 'ASC'){
				$sort = 'DESC';
				$arrow = '<i class="fa fa-arrow-up"></i>';
			}else{
				$sort = 'ASC';
				$arrow = '<i class="fa fa-arrow-down"></i>';
			}
		@endphp
	 
		<div class="col-xs-12">
			<div class="box box-primary">
				<div id="order_list" class="box-body">
					<h3>Recent Orders</h3>
					@include('Admin/orders/orders_list_table')
				</div>
			</div>
		</div>
	</div>
	<br/>	
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body no-padding">
					<div id="calendar"></div>
				</div>
			</div>
		</div>
		<!--<div class="col-md-5">
			<div class="box box-success direct-chat direct-chat-success">
				<div class="box-header with-border">
					<i class="fa fa-comments-o"></i>
					<h3 class="box-title">Messages</h3>
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
								echo '<div class="direct-chat-msg '.$class.'"><div class="direct-chat-info clearfix"><span class="direct-chat-name pull-left">'.$val->sender_name.'</span><span class="direct-chat-timestamp pull-right">('.date('d F H:i A',strtotime($val->date)).')</span></div><div class="direct-chat-text">'.htmlentities($val->message).'</div></div>';
							}
						}
					@endphp
					</div>
				</div>
				@if(\Auth::user()->role_id == 3)
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
		</div>-->
	</div>
	<div class="clearfix"></div>
	<br/><br/><br/>
	<div class="row">
        <div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<i class="fa fa-bar-chart-o"></i>
					<h3 class="box-title">Orders</h3>
				</div>
				<div class="box-body">
					<canvas id="bar-chart" ></canvas>
				</div>
			</div>
        </div>
		<div class="col-md-6">
			<div class="box box-info">
				<div class="box-header with-border">
					<i class="fa fa-shopping-bag" aria-hidden="true"></i>
					<h3 class="box-title">Sales</h3>
				</div>
				<div class="box-body chart-responsive">
					<canvas class="chart" id="line-chart"></canvas>
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
	
	/*
	 * BAR CHART
	 * ---------
	 */
		var orders_array = <?php echo json_encode($order_graph,JSON_PRETTY_PRINT);?>;
		var sales_array = <?php echo json_encode($sales_graph,JSON_PRETTY_PRINT);?>;
		var label = new Array();
		var order_js_arr = new Array();
		var order_sales_arr = new Array();
		var max_value = 0;
		
		$.each(orders_array, function (index, value) {  
			/* var temp = new Array();
			temp[0] = index;
			temp[1] = value;
			order_js_arr.push(temp); */
			
			order_js_arr.push(value);
			label.push(index);
			if(max_value < value){
				max_value = value;
			}
		});   
		
		var ctx = document.getElementById("bar-chart");
		var myChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: label,
				datasets: [{
					label: 'Total Orders',
					data: order_js_arr,
					backgroundColor: 'rgba(153, 102, 255, 0.2)',
					borderColor: 'rgba(153, 102, 255, 1)',
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true,
							suggestedMin: 0,
							suggestedMax: max_value+10,
							stepSize: 5
						}
					}]
				},
				legend: {
					display : false
					//onClick: (e) => e.stopPropagation()
				},
			}
		});
		
		
		/* END BAR CHART */
		var max_value = 0;
		$.each(sales_array, function (index, value) {  
			/* var obj = new Object();
			obj.y = index;
			obj.item1 = value;
			order_sales_arr.push(obj); */
			
			order_sales_arr.push(value);
			if(max_value < value){
				max_value = value;
			}
		});   
		
		// LINE CHART
		var line_chart_id = document.getElementById("line-chart");
		var myChart = new Chart(line_chart_id, {
			type: 'line',
			data: {
				labels: label,
				datasets: [{
					label: 'Total :',
					fill: false,
					data: order_sales_arr,
					backgroundColor: '#3c8dbc',
					borderColor: 'blue',
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true,
							suggestedMin: 0,
							suggestedMax: max_value+500,
							callback: function(value, index, values) {
								return '$' + value;
							}
						}
					}]
				},
				legend: {
					display : false
					//onClick: (e) => e.stopPropagation()
				},
			}
		});
	
	/* var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var line = new Morris.Line({
      element: 'line-chart',
      resize: true,
      data:order_sales_arr,
      xkey: 'y',
      ykeys: ['item1'],
      labels: ['Total: $'],
	  lineColors: ['#3c8dbc'],
      hideHover: 'auto',
	  xLabelFormat: function (x) { return months[x.getMonth()]; }
    }); */


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
</script> 

@include('partials.admin.event')

@include('partials.order.assign_agent')
@include('partials.order.assign_designer')
@include('partials.order.assign_vendor')
@include('partials.order.order_notes')
@include('partials.order.order_event')
@include('partials.order.order_status')

@endsection		  