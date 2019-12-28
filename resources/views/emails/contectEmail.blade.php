<!doctype html>
<html lang="en-US">
<head>
    <meta charset="text/html">
</head>
<body> 
	<h1>Hi {{config('constants.ADMIN_NAME')}}</h1>
	<p>New Contact form request is genrated</p>
	<p>Name : <?=$data['name']?> </p>
	<p>Email : <?=$data['email']?> </p>
	<p>Subject : <?=$data['subject']?> </p>
	<p>Message : <?=$data['message']?> </p>
	<?php
	/* echo "<pre>";
	print_r($data);
	echo "<pre>";die; */
	?>
</body>
</html>