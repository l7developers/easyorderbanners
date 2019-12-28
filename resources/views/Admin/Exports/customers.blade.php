<?php
//pr($users);die;
?>
<table>
    <thead style="background-color:red;color:white;">
    <tr>
        <th>Name</th>
		<th>Company</th>
		<th>Email</th>
		<th>Phone</th>
		<th>Street</th>
		<th>City</th>
		<th>State</th>
		<th>Zip</th>
		<th>Country</th>
		<th>Date</th>
		<th>Pay By Invoice</th>
		<th>Tax Exempt</th>
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
				echo "<td>$user->fname $user->fname</td>";
				echo "<td>$user->company_name</td>";
				echo "<td>$user->email</td>";
				echo "<td>$user->phone_number</td>";
				if(!empty($user->billing_add)){
					echo "<td>".$user->billing_add->add1."</td>";
					echo "<td>".$user->billing_add->city."</td>";
					echo "<td>".$user->billing_add->state."</td>";
					echo "<td>".$user->billing_add->zipcode."</td>";
					echo "<td>".$user->billing_add->country."</td>";
				}else{
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				}
				echo "<td>".date('F-d-Y',strtotime($user->created_at))."</td>";
				if($user->pay_by_invoice == 0)
					echo "<td>No</td>";
				else
					echo "<td>yes</td>";
				
				if($user->tax_exempt == 0)
					echo "<td>No</td>";
				else
					echo "<td>yes</td>";
			@endphp
        </tr>
    @php
		$i++;
		}
	@endphp
    </tbody>
</table>
<?php //die; ?>	
	