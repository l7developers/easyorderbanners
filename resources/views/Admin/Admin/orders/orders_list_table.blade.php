<div class="dragscroll" id="container2">
	<table class="table table-bordered table-hover table-striped addedfeature order_tabel" border="1" style="width:100%;border-collapse:collapse;">
		<thead>
			<tr>
				<th class="nowrap">S.No.</th>
				<th class="nowrap"><input type="checkbox" id="check_all"></th>
				<th class="nowrap">
					<a href="{{url('admin/order/lists/date/'.$sort)}}" class="sort_link">Date</a>
					@if($field == 'created_at')
						{!!$arrow!!}
					@else
						<i class="fa fa-arrows-v"></i>
					@endif
				</th>
				<th class="nowrap">
					<a href="{{url('admin/order/lists/name/'.$sort)}}" class="sort_link">Customer</a>
					@if($field == 'name')
						{!!$arrow!!}
					@else
						<i class="fa fa-arrows-v"></i>
					@endif
				</th>								
				<th class="nowrap">
					<a href="{{url('admin/order/lists/order/'.$sort)}}" class="sort_link">Order#</a>
					@if($field == 'id' and $extra_admin == 'order')
						{!!$arrow!!}
					@else
						<i class="fa fa-arrows-v"></i>
					@endif
				</th>
				<th class="nowrap">Assign</th>
				<th class="nowrap">
					<a href="{{url('admin/order/lists/customer-status/'.$sort)}}" class="sort_link">Customer Status</a>
					@if($field == 'customer_status')
						{!!$arrow!!}
					@else
						<i class="fa fa-arrows-v"></i>
					@endif
				</th>
				<th class="nowrap">
					<a href="{{url('admin/order/lists/payment-status/'.$sort)}}" class="sort_link">Payment Status</a>
					@if($field == 'payment_status')
						{!!$arrow!!}
					@else
						<i class="fa fa-arrows-v"></i>
					@endif
				</th>
				<th class="nowrap">Total</th>
				<!--<th class="nowrap">tFlow Id#</th>-->
				<th class="nowrap">QB Id#</th>
				<th class="nowrap">Action</th>
			</tr>
		</thead>
		<tbody>
		@if(count($orders)>=1)
			@php 
				$status = config('constants.Order_status');
				if(isset($_GET['page'])){
					$i=($limit*$_GET['page'])-$limit;
				}
				else{
					$i=0;
				}
			@endphp
			
			@foreach($orders as $order)
				<tr>
					<td class="nowrap">
						<strong>{{ ++$i }}</strong>&nbsp;&nbsp;
						@php
							$icon_class = 'fa-plus';
							$rel = 0;
							$btn_class = 'btn-success';
							$child_row = 'display:none;';
							if(\session()->get('orders.vendor') != ''){
								$icon_class = 'fa-minus';
								$rel=1;
								$btn_class = 'btn-danger';
								$child_row = '';
							}
						@endphp
						<button type="button" class="btn {{$btn_class}} btn-xs order_row" data-id="{{$order->id}}" rel="{{$rel}}" style="border-radius: 12px !important;"><i class="fa {{$icon_class}}"></i></button>
					</td>
					<td class="nowrap">
						@if($order->qb_id == "" && $order->payment_method !="")
							<input type="checkbox" class="ids" name="ids" value="{{$order->id}}">
						@endif
					</td>
					<td class="nowrap">{{date('m-d-Y',strtotime($order->created_at))}}</td>
					<td class="nowrap" scope="row">
						<a href="{{url('admin/users/edit/'.$order->user_id)}}" target="_blank">
						<b>Company Name: </b>{{$order->customer_company_name}}<br/>
						<b>Name: </b>{{$order->customer_name}}<br/>
						<b>Email: </b>{{$order->customer_email}}<br/>
						<b>Phone Number: </b>{{$order->customer_phone_number}}
						</a>
					</td>									
					<td class="nowrap" scope="row">
						<a href="{{url('/admin/order/edit/'.$order->id)}}" class="btn btn-xs bg-olive margin">#{{$order->id}}</a>
						<?php /*@if($order->payment_status == 0)
							<a href="{{url('/admin/order/edit/'.$order->id)}}" class="btn btn-xs bg-olive margin">#E-{{$order->id}}</a>
						@else
							<a href="{{url('/admin/order/edit/'.$order->id)}}" class="btn btn-xs bg-olive margin">#{{$order->id}}</a>
						@endif */?>
					</td>
					<td class="nowrap">
						@if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2)
						<div class='agent_{{$order->id}}'>
							<b>Agent</b><br/>
							@if(!empty($order->agent_name))
								<button class="btn btn-xs btn-primary agent_btn" data="{{$order->agent_id}}" order-id="{{$order->id}}" data-toggle="modal" data-target="#assign_agent_model" title="Assign Agent">{{$order->agent_name}}</button>
							@else
								<button class="btn btn-xs btn-warning agent_btn" data="0" order-id="{{$order->id}}" data-toggle="modal" data-target="#assign_agent_model" title="Assign Agent">Assign Agent</button>
							@endif
						</div>
						@else
							<b>Agent</b><br/>
							{{$order->agent_name}}
						@endif
						<br/>
						<div id='designer_{{$order->id}}'>
							<b>Designer</b><br/>
							@if(!empty($order->designer_id))
								<div class="btn btn-xs btn-primary designer_btn" data="{{$order->designer_id}}" order-product-id="{{$order->id}}" order-id="{{$order->id}}" data-toggle="modal" data-target="#assign_designer_model" title="Assign Designer">{{$order->designer_name}}</div>
							@else
								<div class="btn btn-xs btn-warning designer_btn" data="0" order-id="{{$order->id}}" data-toggle="modal" data-target="#assign_designer_model" title="Assign Designer">Assign Designer</div>
							@endif
						</div>
					</td>
					<td class="nowrap customer_status_{{$order->id}}">
						@if($order->customer_status != 0)
							<button style="background:{{config('constants.customer_status_color.'.$order->customer_status)}} !important;border-color:{{config('constants.customer_status_color.'.$order->customer_status)}} !important" type="button" class="btn btn-xs bg-olive margin customer_status" data="{{$order->customer_status}}" order-id="{{$order->id}}" data-toggle="modal" data-target="#customer_status" title="Set Customer Status">{{config('constants.customer_status.'.$order->customer_status)}}</button>
						@else
							<button style="background:{{config('constants.customer_status_color.0')}} !important;border-color:{{config('constants.customer_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin customer_status" data="0" order-id="{{$order->id}}" data-toggle="modal" data-target="#customer_status" title="Set customer Status">Set Status</button>
						@endif
					</td>
					<td class="nowrap payment_status_{{$order->id}}">
						@if($order->payment_status != 0)
							<button style="background:{{config('constants.payment_status_color.'.$order->payment_status)}} !important;border-color:{{config('constants.payment_status_color.'.$order->payment_status)}} !important" type="button" class="btn btn-xs bg-navy margin payment_status" data="{{$order->payment_status}}" order-id="{{$order->id}}" product_item_id="{{$order->id}}" data-toggle="modal" data-target="#payment_status" title="Set Payment Status">{{config('constants.payment_status.'.$order->payment_status)}}
							</button><br/>							 
							@php
								if($order->payment_method == 'paypal')
									echo "<b>Payment By :</b>Paypal<br/>";
								elseif($order->payment_method == 'authorized')
									echo "<b>Payment By :</b>Credit Card<br/>";
								elseif($order->payment_method == 'pay_by_invoice')
									echo "<b>Payment By :</b>Pay By Invoice<br/>";
							@endphp
							@if(!empty($order->payment_id))
								<b>Payment Id :</b> {{$order->payment_id}}
							@endif
						@else
							<button style="background:{{config('constants.payment_status_color.0')}} !important;border-color:{{config('constants.payment_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin payment_status" data="0" order-id="{{$order->id}}" product_item_id="{{$order->id}}" data-toggle="modal" data-target="#payment_status" title="Set Payment Status">Set Status</button>
						@endif
					</td>
					<td class="nowrap">${{priceFormat($order->total)}}</td>
					<!--<td class="nowrap">{{(!empty($order_product->tflow_order_id))?$order_product->tflow_order_id:'Not set'}}</td>-->
					<td class="nowrap">{{(!empty($order->qb_id))?'#'.$order->qb_id:'Not set'}}</td>
					<td class="nowrap">	
						@if($order->status == 1)
							<a href="{{url('/admin/order/change-status/'.$order->id.'/2')}}" class="btn btn-xs btn-warning" title="Archive" onclick="return confirm('Are you sure to Archive this Order?')"><i class="fa fa-file-archive-o"></i></a>
						@else
							<a href="{{url('/admin/order/change-status/'.$order->id.'/1')}}" class="btn btn-xs btn-warning" title="UnArchive" onclick="return confirm('Are you sure to UnArchive this Order?')"><i class="fa fa-file-archive-o"></i></a>
						@endif
						<a href="{{url('/admin/order/delete/'.$order->id)}}" class="btn btn-xs btn-danger" title="Delete order" onclick="return confirm('Are you sure to delete this ?')"><i class="fa fa-times" aria-hidden="true"></i></a>

						@if($order->qb_id == "" && ($order->payment_status == 2 || $order->payment_status == 7))
						<a href="#" onClick="window.open('{{url('/admin/quickbook/exporttoqb/'.$order->id)}}','qbwindow','height=600,width=800,top=10,left=100')" class="btn btn-xs btn-info" title="{{$order->id}}">QB</a>
						@endif
					</td>
				</tr>
				@php
					$j = 1;
				@endphp
				@foreach($order->orderProductsDetails as $order_product)
					@if($j == 1)
						<tr class="child_row_{{$order->id}}" style="{{$child_row}};background-color: #ccc;">
							<th class="" style="width:5%;">Sr.No</th>
							<th class="" style="width:20%;" colspan="2">Product Name</th>
							<th class="" style="width:3%;">PO#</th>
							<th class=""  style="width:10%;">Assign</th>
							<th class="" style="width:10%;">ArtWork Status</th>
							<th class="" style="width:10%;">Vendor Status</th>
							<th class="" style="width:2%;">Due Date</th>
							<th class="" style="width:15%;">Tracking Number</th>
							<th class="" style="width:15%;">tFlow Job No.</th>
							<th class="" style="width:10%;">Action</th>
						</tr>
					@endif
					<tr class="child_row_{{$order->id}}" style="{{$child_row}};background-color: #ccc;">
						<td class="" style="width:5%;">{{$j}}</td>
						<td class="nowrap" style="width:15%;" colspan="2">
							<b>Product: </b>
							@if($order_product->product_name !="")		
								{{$order_product->product_name}}
							@else
								{{$order_product->productName}}
							@endif
							<br/>
							@if($order_product->tflow_job_id >= 1 && $order_product->art_work_status==6)
							<b>Art Link: </b><a target="_blank" href="http://108.61.143.179:9016/application/job/{{$order_product->tflow_job_id}}/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT">View ArtWork File</a>
							@endif										
						</td>				
						<td class="po_td_{{$order_product->id}}" scope="row" style="width:3%;">
						@if($order->po_status >= 1)
							@php
								if($order_product->po_id != ''){
									$url = url('/admin/order/po/'.$order_product->po_id);
									$po_name = $order_product->po_id;
								}
								else{
									$po_name = 'Create PO';
									$url = url('/admin/order/po/create/'.$order_product->order_id.'/'.$order_product->product_id);
								}
							@endphp
							<a href="{{$url}}" class="btn btn-xs bg-purple margin">{{$po_name}}</a>
							<br/>
						@endif
						</td>
						<td class="nowrap" style="width:10%;">
							<div id='vendor_{{$order_product->id}}'>
								<b>Vendor</b><br/>
								@if(!empty($order_product->vendor_id))														
									<div class="btn btn-xs btn-primary vendor_btn" data="{{$order_product->vendor_id}}" order-product-id="{{$order_product->id}}" order-id="{{$order->id}}"
									order-product="{{$order_product->product_id}}" data-toggle="modal" data-target="#assign_vendor_model" title="Assign Vendor">{{(!empty($order_product->vendor->company_name))?$order_product->vendor->company_name:$order_product->vendor->fname.' '.$order_product->vendor->lname}}</div>
									
								@else
									<div class="btn btn-xs btn-warning vendor_btn" data="0" order-product-id="{{$order_product->id}}" order-id="{{$order->id}}" order-product="{{$order_product->product_id}}" order-po="{{$order_product->po_id}}" data-toggle="modal" data-target="#assign_vendor_model" title="Assign Vendor">Assign Vendor</div>
								@endif
							</div>
						</td>
						<td class="nowrap" id="art_work_status_{{$order_product->item_id}}" style="width:10%;">
						@if($order_product->product_art_work != 1)
							@if($order_product->art_work_status != 0)
								<button style="background:{{config('constants.art_work_status_color.'.$order_product->art_work_status)}} !important;border-color:{{config('constants.art_work_status_color.'.$order_product->art_work_status)}} !important" type="button" class="btn btn-xs btn-danger margin art_work_status" data="{{$order_product->art_work_status}}" date="{{$order_product->art_work_date}}" order-product-id="{{$order_product->id}}" order-id="{{$order_product->order_id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#art_work_status" title="Set Art Work Status">{{config('constants.art_work_status.'.$order_product->art_work_status)}}<br/>
								@if(!empty($order_product->art_work_date))
									{{$order_product->art_work_date}}
								@endif
								</button>
							@else
								<button style="background:{{config('constants.art_work_status_color.0')}} !important;border-color:{{config('constants.art_work_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin art_work_status" data="0" date="" order-product-id="{{$order_product->id}}"  order-id="{{$order_product->order_id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#art_work_status" title="Set Art Work Status">Set Status</button>
							@endif
						@else
							No Art Work Required.
						@endif
						</td>
						<td class="nowrap" id="vendor_status_{{$order_product->item_id}}" style="width:10%;">
							@if($order_product->vendor_status != 0)
								<button style="background:{{config('constants.vendor_status_color.'.$order_product->vendor_status)}} !important;border-color:{{config('constants.vendor_status_color.'.$order_product->vendor_status)}} !important" type="button" class="btn btn-xs btn-info margin vendor_status" data="{{$order_product->vendor_status}}" order-product-id="{{$order_product->id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#vendor_status" title="Set Vendor Status">{{config('constants.vendor_status.'.$order_product->vendor_status)}}</button>
							@else
								<button style="background:{{config('constants.vendor_status_color.0')}} !important;border-color:{{config('constants.vendor_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin vendor_status" data="0" order-product-id="{{$order_product->id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#vendor_status" title="Set Vendor Status">Set Status</button>
							@endif
						</td>
						<td class="" id="due_date_{{$order_product->item_id}}" style="width:2%;">
							@if(!empty($order_product->due_date))
								@php
								if($order_product->due_date_type == 'soft_date'){
									$class_name = 'btn-success';
								}else{
									$class_name = 'btn-danger';
								}
								@endphp
								<button type="button" class="btn btn-xs {{$class_name}} margin due_date" data="{{$order_product->due_date}}" data-type="{{$order_product->due_date_type}}" order-product-id="{{$order_product->id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#due_date" title="Due Date">{{$order_product->due_date}}</button>
							@else
								<button type="button" class="btn btn-xs bg-orange margin due_date" data="0" data-type="" order-product-id="{{$order_product->id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#due_date" title="Set Due Date">Set Due Date</button>
							@endif
						</td>
						<td class="nowrap" id="tracking_id_{{$order_product->item_id}}" style="width:15%;">
							@if(!empty($order_product->tracking_id))
								<b>{{($order_product->shipping_type == 3)?'Shipping Career':'Shipped Via'}} :</b><br/>
								{{$order_product->shipping_career}}<br/>
								<b>Tracking Number :</b><br/> {{$order_product->tracking_id}}<br/><br/>
								<button style="background:#868686 !important" type="button" class="btn btn-xs bg-maroon margin tracking_id" data="{{$order_product->tracking_id}}" data-type="{{$order_product->shipping_type}}" data-care="{{$order_product->shipping_career}}" order-product-id="{{$order_product->id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#tracking_id" title="Tracking Id">Change Tracking</button>
								
								@if(!empty($order_product->tracking_link))<br/>
								<a style="background:#868686 !important" href="{{$order_product->tracking_link}}" target="_blank" class="btn btn-xs bg-olive margin">Track Order</a>
								@endif
							@else
								<button style="background:#868686 !important" type="button" class="btn btn-xs bg-orange margin tracking_id" data="0" order-product-id="{{$order_product->id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#tracking_id" title="Set Tracking Id">Set Tracking</button>
							@endif
						</td>
						<td style="width:15%;">{{(!empty($order_product->tflow_job_id))?$order_product->tflow_job_id:'Not set'}}</td>
						<td class="nowrap" style="width:10%;">	
							@php
								if(count($order_product->notes) > 0){ 
									$count = '('.count($order_product->notes).')';
								}else{
									$count = '';
								}
							@endphp	
							{{Form::button('<span class="note_count_'.$order_product->item_id.'">'.$count.'</span><i class="fa fa-text-width"></i>',['class'=>'btn btn-xs btn-primary notes_btn','data'=>$order_product->item_id,'data-target'=>'#order_notes','title'=>'Notes'])}}
							
							{{Form::button('<i class="fa fa-calendar"></i>',['class'=>'btn btn-xs btn-warning events_btn','data'=>$order_product->item_id,'data-target'=>'#order_events','title'=>'Events'])}}
							
							{{Form::button('<i class="fa fa-clone"></i>',['class'=>'btn btn-xs btn-success productClone','data-id'=>$order_product->id,'title'=>'Duplicate this item'])}}
							
							{{Form::button('<i class="fa fa-trash"></i>',['class'=>'btn btn-xs btn-danger deleteItem','data-order'=>$order_product->order_id,'data-id'=>$order_product->id,'data-po'=>$order_product->po_id,'title'=>'Delete this item','onclick' =>"return confirm('Are you sure to delete this ?')"])}}
						</td>
					</tr>
					@php
					$j++
					@endphp
				@endforeach
			@endforeach 
		@else
			<tr>
				<td colspan="11"><center><b>No Data Found here</b></center></td>
			</tr>
		@endif
		</tbody>
	</table>
</div>						
<div class="pull-left orderlistaction"> <button class="btn btn-md btn-info exporttoqb" style="margin-top: -20px">Export TO QB</button>   {{ $orders->links() }} </div>
				