<?php
//pr($orders);die;
$main_array = array();
$main_array['sales']['total_sale'] = 0;
$main_array['sales']['no_order'] = 0;
$main_array['sales']['no_item'] = 0;
$main_array['sales']['total_shipping'] = 0;
$main_array['sales_by_product'] = array();

foreach($orders as $order){
	$main_array['sales']['total_sale'] += $order->total;
	$main_array['sales']['total_shipping'] += $order->shipping_fee;
	$main_array['sales']['no_order']++;
	if(count($order->orderProduct) > 0){
		$main_array['sales']['no_item']++;
		foreach($order->orderProduct as $product){
			//pr($product->product->name);
			if(array_key_exists($product->product->name,$main_array['sales_by_product'])){
				$main_array['sales_by_product'][$product->product->name] ++;
			}else{
				$main_array['sales_by_product'][$product->product->name] = 1;
			}
		}
	}
}
$avg = $main_array['sales']['total_sale']/$number_of_days;
$avg = number_format($avg,2,'.','');
//pr($main_array);die;
?>
<table>
    <thead style="background-color:red;color:white;">
    <tr>
        <th></th>
        <th colspan="3">Sales Report From {{$start_date}} To {{$end_date}}</th>
    </tr>
    </thead>
    <tbody>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>Total Sales :</td>
			<td>${{$main_array['sales']['total_sale']}}</td>
		</tr>
		<tr>
			<td></td>
			<td>Average Daily Sales :</td>
			<td>${{$avg}}</td>
		</tr>
		<tr>
			<td></td>
			<td>Number of Orders Placed :</td>
			<td>{{$main_array['sales']['no_order']}}</td>
		</tr>
		<tr>
			<td></td>
			<td>Number of Items Purchased :</td>
			<td>{{$main_array['sales']['no_item']}}</td>
		</tr>
		<tr>
			<td></td>
			<td>Total Shipping :</td>
			<td>${{$main_array['sales']['total_shipping']}}</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th></th>
			<th colspan="3">Sales by Product </th>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Sr.No</td>
			<td>Product Name</td>
			<td>Total Sold</td>
		</tr>
		
	@php
		$i = 1;
		foreach($main_array['sales_by_product'] as $key=>$val){
	@endphp
        <tr>
            <td>{{ $i }}</td>            
            <td>{{ $key }}</td>
            <td>{{ $val }}</td>
        </tr>
    @php
		$i++;
		}
	@endphp
    </tbody>
</table>	
	