<?php
//pr($users);die;
?>
<table>
    <thead style="background-color:red;color:white;">
    <tr>
        <th>Sr.No</th>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Email</th>
		<th>Phone Number</th>
		<th>Company Name</th>
		<th>Pay By Invoice</th>
		<th>Billing Address</th>
		<th>Shipping Address</th>
		<th>Additional Shipping Address</th>
    </tr>
    </thead>
    <tbody>
	@php
		$i = 1;
		foreach($users as $user){
			//pr($user->shipping_add);
	@endphp
        <tr>
			@php
				echo "<td>$i</td>";
				echo "<td>$user->fname</td>";
				echo "<td>$user->lname</td>";
				echo "<td>$user->email</td>";
				echo "<td>$user->phone_number</td>";
				echo "<td>$user->company_name</td>";
				if($user->pay_by_invoice == 0)
					echo "<td>No</td>";
				else
					echo "<td>yes</td>";
				
				if(count($user->billing_add) > 0){
					$str = $user->billing_add->add1.','.$user->billing_add->add2.'<br/>';
					$str .= 'Zipcode: '.$user->billing_add->zipcode.'<br/>';
					$str .= 'City: '.$user->billing_add->city.'<br/>';
					$str .= 'State: '.$user->billing_add->state.'<br/>';
					$str .= 'Country: '.$user->billing_add->country;
					
					echo "<td>".$str."</td>";
				}else{
					echo "<td></td>";
				}
				
				if(count($user->shipping_add) > 0){
					foreach($user->shipping_add as $shipping){
						$str = $shipping->add1.','.$shipping->add2.'<br/>';
						$str .= 'Zipcode: '.$shipping->zipcode.'<br/>';
						$str .= 'City: '.$shipping->city.'<br/>';
						$str .= 'State: '.$shipping->state.'<br/>';
						$str .= 'Country: '.$shipping->country;
						
						echo "<td>".$str."</td>";
					}
				}else{
					echo "<td></td>";
				}
			@endphp
        </tr>
    @php
		$i++;
		}
	@endphp
    </tbody>
</table>	
	