@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Custom Option Detail</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/products/custom/option/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
			</div>
		</div>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body admin-view">	
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Name</b></div>
						<div class="col-lg-6">{{$options->name}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Label</b></div>
						<div class="col-lg-6">{{$options->label}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Free</b></div>
						<div class="col-lg-6">{{$options->free == 1 ?'Yes':'No'}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Group Field</b></div>
						<div class="col-lg-6">
							@php 
								if($options->field_group == 'printing')
									echo 'Printing Options';
								else if($options->field_group == 'production')
									echo 'Design Services Options';
								else if($options->field_group == 'finishing')
									echo 'Finishing Options';
							@endphp
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Price calculation format</b></div>
						<div class="col-lg-6">
							@php 
								if($options->price_formate == 'gross')
									echo 'Price to product gross price';
								else if($options->price_formate == 'area')
									echo 'Price by sqft area';
								else if($options->price_formate == 'parimeter')
									echo 'Price by sqft parimeter';
								else if($options->price_formate == 'item')
									echo 'Price by item';
								else if($options->price_formate == 'line_item')
									echo 'Price by line item';	
							@endphp
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Option Type</b></div>
						<div class="col-lg-6">{{$options['option_type'] == 1 ? 'Select box' : 'Input'}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Status</b></div>
						<div class="col-lg-6">
							@if($options->status == 0)
								<span class="badge">Deactive</span>
							@else
								<span class="badge">Active</span>
							@endif
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Created</b></div>
						<div class="col-lg-6">{{$options->created_at}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>Modified</b></div>
						<div class="col-lg-6">{{$options->updated_at}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2"><b>{{$options['option_type'] == 1 ? 'Options' : 'Price per character'}}</b></div>
						<div class="col-lg-10">
							<?php
								$data = json_decode($options->option_keys,true);
								if($options['option_type'] == 2){
									echo "<div class='col-lg-12'>".$data[0]['price']."</div>";
								}
								else{
									$i = 1;
									echo '<div class="table-responsive"><table class="table"><tbody>';
									foreach($data as $val){
										//pr($val);
										$str = '<tr><td>'.$i.'</td><td>Value:</td><td>'.$val['value'].'</td>';
										if($options->free == 0){
											$str .= '<td>Price:</td><td>$'.$val['price'].'</td>';
										}
										$str .= '<td>Default:</td><td>'.@$val['default'].'</td>';
										$str .= '<td>Weight:</td><td>'.@$val['weight'].'</td>';
										$str .= '<td>Flat Rate Additional Price:</td><td>$'.@$val['flat_rate_additional_price'].'</td>';
										$str .= '</tr>';
										echo $str;
										$i++;
									}
									echo '</tbody></table></div>';
								}
							?>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>	  
@endsection		  