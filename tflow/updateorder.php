<?php
// set post fields
$post = [
    'grant_type' => 'client_credentials',
    'client_id' => '8WRdQmA2xDmdMlLF',
    'client_secret'   =>'tEB18MJSgrNCGQf2mxQJzHOiZLcsYoJ2',
];

$ch = curl_init('http://108.61.143.179:9016/oauth/access_token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
$response = curl_exec($ch);
curl_close($ch);

$res = json_decode($response);

echo "<pre>";
print_r($res);
echo "</pre>";

$token = $res->access_token;



$post = [
    'client_id' => '101',
    'name' => 'TEST ORDER (Ignore Plz)',
    'product_id'   =>'11',
    'assignments'   =>'21',
    'external_id'   =>'1001', // id for order in our system
    'planned_number_of_jobs'   =>'2',
    'description'   =>'EOB User, easyorderbanner',
    'props'=>array(
    	'rep_name'=> 'agent 2',    	
    )   	    
];

$ch = curl_init('http://108.61.143.179:9016//api/v2/order/2498/update');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$token));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
$response = curl_exec($ch);
curl_close($ch);

$res = json_decode($response);

echo "<pre>";
print_r($res);
echo "</pre>";

if(isset($res->error))
{
	foreach($res->messages as $msg)
	{
		foreach($msg as $value)
		{
			echo "<br/>".$value;
		}
	}
}