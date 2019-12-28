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
echo curl_error($ch);
curl_close($ch);

$res = json_decode($response);

echo "<pre>response";
print_r($res);
echo "</pre>";