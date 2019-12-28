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
    'order_id' => '2498',
    'client_id' => '101',
    'name' => 'TEST JOb 2 (Ignore Plz)',
    'product_id'   =>'11',
    'assignments'   =>'21',
    'description'   =>'9OZ Vinayl Banner',
    'notes'   =>'user comment',
    'ship_date'=>'2018-11-30T23:59:59+07',
    'props'=>array(
    	"print_width"=> 15,
        "print_height"=> 20,
        "sales_rep"=> "1",
        "quantity"=> 1,
        "shipping_address"=> "61856 Farrell Loop\nLake Ezekiel, KS 05244",
        "phone_contact"=> "7503790693",
        "email_contact"=> "test@gmail.com"    	
    ) 
];

$ch = curl_init('http://108.61.143.179:9016/api/v2/job/create');
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