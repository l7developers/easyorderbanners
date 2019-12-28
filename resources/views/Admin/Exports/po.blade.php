<table>
    <thead style="background-color:red;color:white;">
    <tr>
        <th>Sr.No</th>
        <th>PO No</th>
		<th>Payee</th>
		<th>Purchase Order Date</th>
		<th>Due Date</th>
		<th>Terms</th>
		<th>Billing Address Line 1</th>
		<th>Billing Address Line 2</th>
		<th>Billing Address Line 3</th>
		<th>Billing Address City</th>
		<th>Billing Address Postal Code</th>
		<th>Billing Address Country</th>
		<th>Billing Address State</th>
		<th>Shipping Address Line 1</th>
		<th>Shipping Address Line 2</th>
		<th>Shipping Address Line 3</th>
		<th>Shipping Address City</th>
		<th>Shipping Address Postal Code</th>
		<th>Shipping Address Country</th>
		<th>Shipping Address State</th>
		<th>Expense Account</th>
		<th>Expense Customer</th>
		<th>Line Item</th>
		<th>Line Item Description</th>
		<th>Line Item Quantity</th>
		<th>Line Item Rate</th>
		<th>Line Item Amount</th>
		<th>Line Item Customer</th>
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
				echo "<td>$order->po_id</td>";
				echo "<td>$order->vendor_name</td>";
				echo "<td>".date('m-d-Y',strtotime($order->order_created))."</td>";
				echo "<td>$order->due_date</td>";
				
				if($order->terms == 1 or $order->terms == '1'){
					echo "<td>Net 20 Days</td>";
				}else if($order->terms == 2 or $order->terms == '2'){
					echo "<td>Due on Receipt</td>";
				}else{
					echo "<td>$order->new_terms</td>";
				}
				echo "<td>".htmlentities($order->vendor_address)."</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td>$order->shipping_add1</td>";
				echo "<td>$order->shipping_add2</td>";
				echo "<td></td>";
				echo "<td>$order->shipping_city</td>";
				echo "<td>$order->shipping_zipcode</td>";
				echo "<td>$order->shipping_country</td>";
				echo "<td>$order->shipping_state</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td>$order->product_name</td>";
				echo "<td></td>";
				echo "<td>$order->qty</td>";
				echo "<td>$$order->gross_total</td>";
				echo "<td>$$order->total</td>";
				echo "<td>$order->customer_name</td>";
			@endphp
        </tr>
    @php
		$total += $order->total;
		$i++;
		}
	@endphp
    </tbody>
</table>	
	