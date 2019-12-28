<?php

header('Content-Type: text/html; charset=utf-8');
ini_set("default_charset", "UTF-8");
mb_internal_encoding("UTF-8");
 ?>
<table>
    <thead style="background-color:red;color:white;">
    <tr>
        <th>InvoiceNo</th>
        <th>Customer</th>
		<th>Invoice Date</th>
		<th>Terms</th>
		<th>Item</th>
		<th>Item Description</th>
		<th>Item Quantity</th>
		<th>Item Rate</th>
		<th>Item Amount</th>
		<th>Taxable</th>
		<th>Tax Rate</th>
		<th>Shipping Address</th>
		<th>Ship via</th>
		<th>Tracking No</th>
		<th>Shipping Charge</th>
    </tr>
    </thead>
    <tbody>
	@php
		$i = 1;
		$lastOrderId = 0;
		$total = 0;
		foreach($orders as $order){
	@endphp
        <tr>
			@php
				echo "<td>".$order->order_id."</td>";
				echo "<td>".$order->customer_name."</td>";
				
				$date = date('d / F / Y',strtotime(@$order->order->created_at));
				echo "<td>".$date."</td>";
				
				$terms = config('constants.terms');
				$term_name = '';
				if($order->order->terms == 3){
					$term_name = $order->order->new_terms;
				}
				else{
					if(!empty($order->order->terms) and array_key_exists($order->order->terms,$terms)){
						$term_name = $terms[$order->terms];
					}
				}
				
				echo "<td>".$term_name."</td>";
				
				if($order->product_id == config('constants.CUSTOM_PRODUCT_ID')){
					echo "<td>".$order->product_name."</td>";
					echo "<td>".$order->description."</td>";
				}else{
					echo "<td>".$order->productName."</td>";
					$str = "";
						if(!empty($order->orderProductOptions)){
							foreach($order->orderProductOptions as $options){
								if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height')
									$str .= $options->custom_option_name." (ft) :". $options->value.",";
								else
									$str .= $options->custom_option_name." : ".htmlentities($options->value).",";
							}
						}
					echo "<td>".trim($str,',')."</td>";
				}
				
				echo "<td>".$order->qty."</td>";
				echo "<td>$".$order->price."</td>";
				echo "<td>$".$order->gross_total."</td>";
				
				if($order->shipping_state == 'PA'){
					echo "<td>Y</td>";
					if($lastOrderId != $order->order->id)
						echo "<td>".$order->order->sales_tax."</td>";
					else
						echo "<td></td>";
				}else{
					echo "<td>N</td>";
					echo "<td></td>";
				}
				
				$str = $order->shipping_fname.' '.$order->shipping_lname.',';
				$str .= $order->shipping_add1.', ';
				
				if(!empty($order->shipping_add2))
					$str .=$order->shipping_add2.', ';
					
				$str .= $order->shipping_city.',';
				$str .= $order->shipping_state.' ';
				$str .= $order->shipping_zipcode;
				
				echo "<td>".$str."</td>";
				echo "<td>".config('constants.Shipping_option.'. $order->order->shipping_option)."</td>";
				
				if(!empty($order->order->tracking_id))
					echo "<td>".$order->order->tracking_id."</td>";
				else
					echo "<td></td>";
				
				if($order->order->multiple_shipping == 1 || $order->order->multiple_shipping == '1')
					echo "<td>$".$order->product_shipping."</td>";
				else
					echo "<td>$".$order->order->shipping_fee."</td>";
			@endphp
        </tr>
    @php
		$total += $order->total;
		$i++;
		$lastOrderId = $order->order->id;
		}
	@endphp
    </tbody>
</table>	
<?php //die;?>
	