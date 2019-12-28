@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Sales Reports</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-body">
					{{Form::model('filter')}}
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('from_date', 'From Date',array('class'=>'form-control-label'))}}	{{Form::text('from_date',\session()->get('reports.sales.from_date'),['class'=>'form-control','placeholder'=>'From date','readonly'])}}
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('end_date', 'End Date',array('class'=>'form-control-label'))}}	{{Form::text('end_date',\session()->get('reports.sales.end_date'),['class'=>'form-control','placeholder'=>'From date','readonly'])}}
						</div>
					</div>
					
					<div class="col-lg-2 col-md-3 col-sm-6">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
								<a href="{{url('/admin/reports/sales?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
							</div>
						</div>
					</div>
					{{Form::close()}}
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-primary col-xs-12 col-sm-6">
				<div class="box-body table-responsive col-sm-9 col-xs-10">
					<canvas id="line-chart" ></canvas>
				</div>
				<div class="box-body table-responsive col-sm-3 col-xs-2">
					@if(count($orders) > 0)
						<a href="{{url('admin/reports/sales/export')}}" class="btn bg-olive pull-right" target="_blank">Export CSV</a>
					@else
						<a href="javascript::void(0)" class="btn bg-olive pull-right">No-Data</a>
					@endif
				</div>
			</div>
		</div>
	</div>
</section>  
<script>
/*
 * BAR CHART
 * ---------
 */
	var sales_array = <?php echo json_encode($sales_graph,JSON_PRETTY_PRINT);?>;
	var label = new Array();
	var order_sales_arr = new Array();
	var max_value = 0;
	
	$.each(sales_array, function (index, value) {  
		order_sales_arr.push(value);
		label.push(index);
		if(max_value < value){
			max_value = value;
		}
	});   
	
	var ctx = document.getElementById("line-chart");
	var myChart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: label,
			datasets: [{
				label: 'Total',
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
						//stepSize: 5,
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
			/* title: {
				display: true,
				text: 'Sales Report'
			} */
		}
	});
	
	
	/* END BAR CHART */
</script>
@endsection		  