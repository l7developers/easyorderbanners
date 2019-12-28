<table>
    <thead style="background-color:red;color:white;">
    <tr>
        <th>Sr.No</th>
        <th>Type</th>
		<th>Number</th>
		<th>Date</th>
		<th>Account</th>
		<th>Amount</th>
		<th>Item</th>
		<th>Payment Type</th>
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
				echo "<td>Payment</td>";
				echo "<td>#$order->item_id</td>";
				
				$date = date('d-m-Y',strtotime(@$order->order->created_at));
				echo "<td>$date</td>";
				
				echo "<td>Undepositied Funds </td>";
				echo "<td>$".$order->total."</td>";
				echo "<td>".$order->product_name."</td>";
				
				$methord = '';
				if(@$order->order->payment_method == 'pay by invoice'){
					$methord = 'Invoice';
				}
				if(@$order->order->payment_method == 'authorized'){
					$methord = 'Credit Card';
				}
				if(@$order->order->payment_method == 'paypal'){
					$methord = 'Paypay';
				}
				
				echo "<td>".$methord."</td>";
			@endphp
        </tr>
    @php
		$total += $order->total;
		$i++;
		}
	@endphp
    </tbody>
</table>	
	