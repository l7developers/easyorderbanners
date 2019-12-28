<table>
    <thead style="background-color:red;color:white;">
    <tr>
        <th>Sr.No</th>
		<th>Order Id#</th>
		<th>PO#</th>
		<th>Customer Name</th>
		<th>Customer Email</th>
		<th>Customer Phone Number</th>
		<th>Customer Company Name</th>
		<th>Order Received</th>
		<th>Agent</th>
		<th>Designer</th>
		<th>Vendor</th>
		<th>ArtWork Status</th>
		<th>Vendor Status</th>
		<th>Order Status</th>
		<th>Discount</th>
		<th>Shipping Type</th>
		<th>Shipping price</th>
		<th>Total Order Price</th>
		<th>Payment Type</th>
		<th>Payment ID</th>
		<th>Billing address</th>
		<th>Shipping address</th>
		<th>Tracking Number</th>
		<th>Product Ordered<th>
    </tr>
    </thead>
    <tbody>
	@php
		$i = 1;
		$total = 0;
		foreach($orders as $order){
	@endphp
        <tr>
			@php
				echo "<td>$i</td>";
				if($order->payment_status == 0)
					echo "<td>#E-$order->item_id</td>";
				else
					echo "<td>#$order->item_id</td>";
				
				echo "<td>$order->po_id</td>";
				echo "<td>$order->customer_name</td>";
				echo "<td>$order->customer_email</td>";
				echo "<td>$order->customer_phone_number</td>";
				echo "<td>$order->customer_company_name	</td>";
				$date = date('d-m-Y',strtotime($order->order->created_at));
				echo "<td>$date</td>";
				if(!empty($order->agent_name))
					echo "<td>$order->agent_name</td>";
				else
					echo "<td></td>";
				
				if(!empty($order->designer_name))
					echo "<td>$order->designer_name</td>";
				else
					echo "<td></td>";
				
				if(!empty($order->vendor_name))
					echo "<td>$order->vendor_name</td>";
				else
					echo "<td></td>";
				
				if($order->art_work_status != 0)
					echo "<td>".config('constants.art_work_status.'.$order->art_work_status)."</td>";
				else
					echo "<td></td>";
				
				if($order->vendor_status != 0)
					echo "<td>".config('constants.vendor_status.'.$order->vendor_status)."</td>";
				else
					echo "<td></td>";
				
				if($order->customer_status != 0)
					echo "<td>".config('constants.customer_status.'.$order->customer_status)."</td>";
				else
					echo "<td></td>";
				
				echo "<td>$".$order->qty_discount."</td>";
				echo "<td>".config('constants.Shipping_option.'.$order->order->shipping_option)."</td>";
				echo "<td>$".$order->order->shipping_fee."</td>";
				echo "<td>$".$order->total."</td>";
				echo "<td>".$order->order->payment_method."</td>";
				echo "<td>".$order->order->payment_id."</td>";
						
				$str = 'Name: '.$order->billing_fname.' '.$order->billing_lname.'<br/>';
				$str .= 'Address: '.$order->billing_add1.'<br>'.$order->billing_add2.'<br/>';
				$str .= 'Zipcode: '.$order->billing_zipcode.'<br/>';
				$str .= 'City: '.$order->billing_city.'<br/>';
				$str .= 'State: '.$order->billing_state.'<br/>';
				$str .= 'Country: '.$order->billing_country;
				
				echo "<td>".$str."</td>";
						
				$str = 'Name: '.$order->shipping_fname.' '.$order->shipping_lname.'<br/>';
				$str .= 'Address: '.$order->shipping_add1.'<br/>'.$order->shipping_add2.'<br/>';
				$str .= 'Zipcode: '.$order->shipping_zipcode.'<br/>';
				$str .= 'City: '.$order->shipping_city.'<br/>';
				$str .= 'State: '.$order->shipping_state.'<br/>';
				$str .= 'Country: '.$order->shipping_country;
				
				echo "<td>".$str."</td>";
				
				if(!empty($order->tracking_id))
					echo "<td>$order->tracking_id</td>";
				else
					echo "<td></td>";
				
				echo "<td>".$order->product_name."</td>";
			@endphp
        </tr>
    @php
		$total += $order->total;
		$i++;
		}
	@endphp
    </tbody>
</table>	
	