@foreach($order->orderProductsDetails as $order_product)
	@if($order_product->id == $id)
		@if($type == 'estimates')
			<tr class="child_row_{{$order->id}}" style="background-color: #ccc;">
				<td class="nowrap">{{$itemNumber}}</td>
				<td class="nowrap" colspan="7">
					<b>Product: </b>
					@if($order_product->product_name !="")		
						{{$order_product->product_name}}
					@else
						{{$order_product->productName}}
					@endif
					<br/>
					@if($order_product->tflow_job_id >= 1)
					<b>Art Link: </b><a target="_blank" href="http://108.61.143.179:9016/application/job/{{$order_product->tflow_job_id}}/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT">Click Here o View Production Ready Art Link</a>
					@endif										
				</td>																		
				<td class="nowrap">	
					@php
						if(count($order_product->notes) > 0){ 
							$count = '('.count($order_product->notes).')';
						}else{
							$count = '';
						}
					@endphp	
					{{Form::button('<span class="note_count_'.$order_product->item_id.'">'.$count.'</span><i class="fa fa-text-width"></i>',['class'=>'btn btn-xs btn-primary notes_btn','data'=>$order_product->item_id,'data-target'=>'#order_notes','title'=>'Notes'])}}
					
					{{Form::button('<i class="fa fa-calendar"></i>',['class'=>'btn btn-xs btn-danger events_btn','data'=>$order_product->item_id,'data-target'=>'#order_events','title'=>'Events'])}}
					
					{{Form::button('<i class="fa fa-clone"></i>',['class'=>'btn btn-xs btn-success productClone','data-id'=>$order_product->id,'title'=>'Clone order product'])}}
				</td>
			</tr>
		@else
			<tr class="child_row_{{$order->id}}" style="background-color: #ccc;">
				<td class="nowrap">{{$itemNumber}}</td>
				<td class="nowrap" colspan="2">
					<b>Product: </b>
					@if($order_product->product_name !="")		
						{{$order_product->product_name}}
					@else
						{{$order_product->productName}}
					@endif
					<br/>
					@if($order_product->tflow_job_id >= 1 && $order_product->art_work_status==6)
					<b>Art Link: </b><a target="_blank" href="http://108.61.143.179:9016/application/job/{{$order_product->tflow_job_id}}/download/preflighted?hash=GdDF7OAwo2xvxqbNKge6z5SXxYB81hHrhojPoD5KkPvZC33z77MR7KvOVqkCw4ZT">Click Here To View Production Ready Art Link</a>
					@endif										
				</td>				
				<td class="nowrap po_td_{{$order_product->id}}" scope="row">
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
				<td class="nowrap">
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
				<td class="nowrap" id="art_work_status_{{$order_product->item_id}}">
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
				<td class="nowrap" id="vendor_status_{{$order_product->item_id}}">
					@if($order_product->vendor_status != 0)
						<button style="background:{{config('constants.vendor_status_color.'.$order_product->vendor_status)}} !important;border-color:{{config('constants.vendor_status_color.'.$order_product->vendor_status)}} !important" type="button" class="btn btn-xs btn-info margin vendor_status" data="{{$order_product->vendor_status}}" order-product-id="{{$order_product->id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#vendor_status" title="Set Vendor Status">{{config('constants.vendor_status.'.$order_product->vendor_status)}}</button>
					@else
						<button style="background:{{config('constants.vendor_status_color.0')}} !important;border-color:{{config('constants.vendor_status_color.0')}} !important" type="button" class="btn btn-xs bg-orange margin vendor_status" data="0" order-product-id="{{$order_product->id}}" product_item_id="{{$order_product->item_id}}" data-toggle="modal" data-target="#vendor_status" title="Set Vendor Status">Set Status</button>
					@endif
				</td>
				<td class="nowrap" id="due_date_{{$order_product->item_id}}">
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
				<td class="nowrap" id="tracking_id_{{$order_product->item_id}}">
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
				<td>{{(!empty($order_product->tflow_job_id))?$order_product->tflow_job_id:'Not set'}}</td>
				<td class="nowrap">	
					@php
						if(count($order_product->notes) > 0){ 
							$count = '('.count($order_product->notes).')';
						}else{
							$count = '';
						}
					@endphp	
					{{Form::button('<span class="note_count_'.$order_product->item_id.'">'.$count.'</span><i class="fa fa-text-width"></i>',['class'=>'btn btn-xs btn-primary notes_btn','data'=>$order_product->item_id,'data-target'=>'#order_notes','title'=>'Notes'])}}
					
					{{Form::button('<i class="fa fa-calendar"></i>',['class'=>'btn btn-xs btn-danger events_btn','data'=>$order_product->item_id,'data-target'=>'#order_events','title'=>'Events'])}}
					
					{{Form::button('<i class="fa fa-clone"></i>',['class'=>'btn btn-xs btn-success productClone','data-id'=>$order_product->id,'title'=>'Clone order product'])}}
					
					{{Form::button('<i class="fa fa-trash"></i>',['class'=>'btn btn-xs btn-danger deleteItem','data-order'=>$order_product->order_id,'data-id'=>$order_product->id,'data-po'=>$order_product->po_id,'title'=>'Delete this item','onclick' =>"return confirm('Are you sure to delete this ?')"])}}
				</td>
			</tr>
		@endif
	@endif
@endforeach