@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Export PO</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-body">
					{{Form::model('po_export')}}
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('from_date', 'From Date',array('class'=>'form-control-label'))}}	{{Form::text('from_date',\session()->get('reports.customer.from_date'),['class'=>'form-control','placeholder'=>'From date','readonly'])}}
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="form-group">
							{{ Form::label('end_date', 'End Date',array('class'=>'form-control-label'))}}	{{Form::text('end_date',\session()->get('reports.customer.end_date'),['class'=>'form-control','placeholder'=>'From date','readonly'])}}
						</div>
					</div>
					
					<div class="col-lg-2 col-md-3 col-sm-6">
						<div class="form-group">
							<label class="control-label" for="Filter_Search">&nbsp;</label>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-sm btn-primary">Export PO</button>
							</div>
						</div>
					</div>
					{{Form::close()}}
				</div>
			</div>
		</div>
	</div>
</section>  
@endsection		  