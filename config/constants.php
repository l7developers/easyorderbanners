<?php
$pageURL = @$_SERVER["SERVER_NAME"];
$siteFoler = dirname(dirname(@$_SERVER['SCRIPT_NAME']));
$siteUrl = $pageURL.$siteFoler.'/';
$siteUrl = str_replace('\\','/',$siteUrl);
$siteUrl = str_replace('//','/',$siteUrl);

$siteUrl = @$_SERVER['APP_URL'];

$custom_category_id = 53;
$custom_product_id = 271;
$phantomjs_path ='/var/www/html/easyorderbanners/phantomjs/bin/phantomjs';
$phantomjs2_path ='/home/easyordertt/easyorderbanners.com/phantomjs/bin/ver2/phantomjs';
$pagesJS2 = '/home/easyordertt/easyorderbanners.com/pages2.js';
if($_SERVER['SERVER_NAME'] == '192.168.1.77')
{
    $phantomjs_path ='phantomjs';
    $custom_category_id = 36;
    $custom_product_id = 27;
}

return [
	'SITE_URL' =>$siteUrl,
	'SITE_NAME' => 'Easy Order Banners',
	'site_phone_number' => '(800) 920-9527',
	'ADMIN_NAME' => 'Doug Reed',
	'ADMIN_MAIL' => 'info@easyorderbanners.com',	
	'ARTWORK_FILE_MAIL' => 'stephen@easyorderbanners.com',		
	'ADMIN_DASHBOARD_LIMIT' => 50,
	'ADMIN_PAGE_LIMIT' => 10,
	'FRONT_PAGE_LIMIT' => 21,
	'ADMIN_SINCE' => 'Member since Nov. 2012',	
	'CUSTOM_PRODUCT_ID' => $custom_product_id,	
	'CUSTOM_CATEGORY_ID' => $custom_category_id,
	'phantomjs_path' => env('PHANTOMJS_PATH', $phantomjs_path),
	'store_name' => 'Easy Order Banners',
	'store_phone_number' => '(800) 920-9527',
	'store_email' => 'info@easyorderbanners.com',		
	'Tflow_baseUri'=>'http://108.61.143.179:9016',
	'Tflow_clientId'=>'8WRdQmA2xDmdMlLF',
	'Tflow_clientSecret'=>'tEB18MJSgrNCGQf2mxQJzHOiZLcsYoJ2',
	'aws_key' => env('AWS_KEY', 'AKIAIO7JHTMKZZ5GL5TQ'),
	'aws_secret' => env('AWS_SECRET', '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'),
    's3_bucket_name' => env('S3_BUCKET_NAME', 'easyorderbanners-prod'),
	'Authorize_loginId' => env('AUTHORIZE_LOGINID', '4nNwX9562'),
	'Authorize_transactionId' => env('AUTHORIZE_TRANSID', '26z8S2aCwR5J75Z5'),
	'google_captch_site_key' => '6Ldxt60UAAAAAIIi4r89xHsaqbP7jbpRR8uU5hcs',
	'google_captch_secret_key' => '6Ldxt60UAAAAANvNw4jRkGh0FRiWiYX6bZHOeHTD',
	'Ups_Production' => false,
	'Ups_SandBox' => true,
	'Ups_accessKey' => '1D4317B93E6EC14D',
	'Ups_userId' => 'dreed139',
	'Ups_password' => '4maui11',
	'ShipperNumber' => '2R691F',
	'AccountNumber' => '2R691F',
	'sales_tax' => 6,
	'ShipperDetail' => [
						'name' => 'Easy Order Banners',
						'attentionName' => 'Easy Order Banners',
						'address' => '146 Orchard Lane',
						'postalCode' => '91911',
						'city' => 'Chula Vista',
						'provinceCode' => 'CA',
						'countryCode' => 'US',
						'email' => 'billboard@comcast.net',
						'phone_number' => '800-920-9527',
						],
	'Shipping_option' => [
							'03' => 'Ground Shipping',
							'12' => '3 Day Shipping',
							'02' => '2 Day Shipping',
							'13' => 'Overnight Shipping (air saver)',
							//'14' => 'Overnight AM',
						],
	'Order_status'=>[
						//0 => 'Cart',
						1 => 'Submited',
						2 => 'Paid',
						3 => 'Awaiting File Upload',
						4 => 'Preflight',
						5 => 'Proof sent to customer',
						6 => 'Approved by customer',
						7 => 'Rejected by customer',
						8 => 'On Hold',
						9 => 'Sent to Vendor',
						10 => "Vendor's proof approved",
						11 => 'In production',
						12 => 'Shipped',
						13 => 'Completed',
					],
	'art_work_status' => [
							0 => 'Select Status',
							1 => 'Awaiting File Upload',
							2 => 'Files Received / Preflight',
							//3 => 'Proof Sent to Customer',
							4 => 'Rejected by Customer',
							5 => 'On Hold',
							6 => 'Approved by Customer',
						],
	'art_work_status_color' => [
							0 => '#767676 ',
							1 => '#FFC107',
							2 => '#2196F3',
							3 => '#00c0ef',
							4 => '#FF5722',
							5 => '#FF9800 ',
							6 => '#8bc34a ',
						],
	'customer_status' => [
							0 => 'Select Status',
							1 => 'Order Submitted',
							2 => 'Awaiting File Upload',
							3 => 'Proofing',
							4 => 'In Production',
							5 => 'Completed'
						],
	'customer_status_color' => [
							0 => '#767676 ',
							1 => '#FFC107',
							2 => '#dd4b39',
							3 => '#00c0ef',
							4 => '#009688',
							5 => '#8bc34a '
						],
							
	'payment_status' => [
							//0 => 'Estimate',
							//1 => 'Accepted',
							2 => 'Payment Received ',
							3 => 'Payment Pending ',
							4 => 'Declined',
							//5 => 'Required',
							//6 => 'Canceled',
							7 => 'Pay by Invoice',
						],
	'payment_status_color' => [
							0 => '#767676 ',
							1 => '#009688',
							2 => '#8bc34a',
							3 => '#FFC107',
							4 => '#FF5722',
							5 => '#FF9800 ',
							6 => '#001f3f ',
						],
	'vendor_status' => [
							//1 => 'Assigned',
							2 => 'PO Sent',
							//3 => 'Pending Proof',
							4 => 'In Production',
							5 => 'Completed',
						],
	'vendor_status_color' => [
							0 => '#767676 ',
							1 => '#2196F3',
							2 => '#00c0ef',
							3 => '#FFC107',
							4 => '#009688',
							5 => '#8bc34a '
						],
	'terms' => [
				''=>'Select terms',				
				1=>'Due on Receipt',
				2=>'Net 20 Days',
				3=>'Net 30 Days',
				4=>'Net 45 Days',
				5=>'Net 60 Days',				
			],					
];
