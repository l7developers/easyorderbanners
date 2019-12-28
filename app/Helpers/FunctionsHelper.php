<?php
namespace App\Helpers;

use App\Http\Controllers\Controller;
use App\User;
use App\Orders;
use App\Products;
use App\OrderProducts;
use Exception;

/**
 * Class FunctionsHelper
 * @package App\Helpers
 */
class FunctionsHelper
{
    /**
     * @param $order_id
     * @param null $type
     */
    public static function sendOrderReceipt($order_id, $type = null)
    {
		$order = Orders::with(['customer','orderProduct','orderProductOptions','orderAddress'])->findOrFail($order_id);
		//pr($order);

        $customer_po='';
        if (!empty($order->customer_po)) {
            $customer_po = '<b>Customer PO </b>#'.$order->customer_po.'</span><br/>';
        }

		$str = '<table cellpadding="3" width="100%" border="0">
                    <tr>
                        <td style="border-right:none">
                            <img src="'.url('public/img/front/logo.png').'"/>
                        </td>
                        <td colspan="2" style="border-left:none;text-align: right;"> 
                            <span>
                                <b>Order ID: </b>#'.$order->id.'</span><br/>'.$customer_po.'<b>Date: </b>'.date('m-d-Y',strtotime($order->created_at)).'</td></tr>';
		
		$str .= '<tr><td width="33%"><strong>Billing Address:</strong><br/>';

		if($order->orderAddress->billing_company_name !="")
			$str .=$order->orderAddress->billing_company_name.'<br/>';
		
		if($order->orderAddress->billing_fname != '')
			$str .=$order->orderAddress->billing_fname.' '.$order->orderAddress->billing_lname.'<br/>';	

		if($order->orderAddress->billing_add1 != '' and $order->orderAddress->billing_add2 != '')
			$str .=$order->orderAddress->billing_add1.'<br>'.$order->orderAddress->billing_add2.'<br>';
		elseif($order->orderAddress->billing_add1 != '')
			$str .=$order->orderAddress->billing_add1.'<br>';	
		
		$str .= $order->orderAddress->billing_city.', '.$order->orderAddress->billing_state.' '.$order->orderAddress->billing_zipcode.'<br/>';
		
		$str .= $order->orderAddress->billing_country;

		$str .='</td><td width="33%" style="vertical-align:top"><strong>Ship To:</strong><br/>';

		if($order->multiple_shipping == 0)
		{	
			if($order->orderAddress->shipping_company_name !="")
				$str .=$order->orderAddress->shipping_company_name.'<br/>';
			
			if($order->orderAddress->shipping_fname != '')
				$str .=$order->orderAddress->shipping_fname.' '.$order->orderAddress->shipping_lname.'<br/>';

            if($order->shipping_ship_in_care != '')
                $str .='<strong>Care of: </strong>'.$order->shipping_ship_in_care.'<br/>';

			if($order->orderAddress->shipping_add1 != '' and $order->orderAddress->shipping_add2 != '')
				$str .=$order->orderAddress->shipping_add1.'<br/>'.$order->orderAddress->shipping_add2.'<br>';
			elseif($order->orderAddress->shipping_add1 != '')
				$str .=$order->orderAddress->shipping_add1.'<br>';

			$str .=$order->orderAddress->shipping_city.', '.$order->orderAddress->shipping_state.' '.$order->orderAddress->shipping_zipcode.'<br/>';
			
			$str .= $order->orderAddress->shipping_country;

            if($order->orderAddress->shipping_phone_number !="")
                $str .= '<br>' . $order->orderAddress->shipping_phone_number;
		}
		else
		{
			$str .=$order->customer->fname.' '.$order->customer->lname.'<br/>
			<span style="color:red">Split Shipment-See Below</span>';
		}	

		$str .='</td><td width="33%" valign="top">';

		if (!empty($order->payment_status)) {
            $paymentStatus = \App\Helpers\PaymentHelper::getPaymentStatus($order->payment_status);
            $str .= "
                <strong>Payment Status: </strong>{$paymentStatus}<br/>                
            ";
        }

        if (!empty($order->payment_method)) {
            $paymentMethod = \App\Helpers\PaymentHelper::getPaymentMethod($order->payment_method);
            $str .= "
                <strong>Payment Type: </strong>{$paymentMethod}<br/>
            ";
        }

        if (!empty($order->payment_id)) {
            $str .= "
                <strong>Payment ID: </strong>{$order->payment_id}<br/>
            ";
        }

		$str .='</td></tr></table>';

		$str .= '<table class="table table-condensed" border="1" width="100%"><tr><th>Product Name</th><th>Description</th><th>Quantity</th><th>Total</th>';

		if($order->multiple_shipping != 0)
			$str .= '<th>Ship To Address</th>';		

		$str .= '</tr>';
		foreach($order->orderProduct as $product){
			
			if($product->product_name !="" )
			{
				$str .= '<td>'.$product->product_name.'</td>';
			}	
			else
			{
				$str .= '<tr><td>'.$product->product->name.'</td>';
			}
			
			$str .= '<td><ul>';

			if ($product->product_id == 271) {
                $str .= '<li>'.$product->description.'<br/></li>';
            } else {
                foreach($order->orderProductOptions as $options){
                    if($product->id == $options->order_product_id){
                        if($options->custom_option_name == 'Width' or $options->custom_option_name == 'Height'){
                            $str .= '<li>'.$options->custom_option_name.'(ft)'.':'.$options->value.'<br/></li>';
                        }else{
                            $str .= '<li>'.$options->custom_option_name.':'.$options->value.'<br/></li>';
                        }
                    }
                }
            }

			$str .= '</ul></td><td>'.$product->qty.'</td>';
			$str .= '<td>$'.$product->total.'</td>';
			
			if($order->multiple_shipping != 0)
			{
				$str .= '<td>';
				if($product->shipping_company_name !="")
					$str .=$product->shipping_company_name.'<br/>';
				
				if($product->shipping_fname != '')
					$str .=$product->shipping_fname.' '.$product->shipping_lname.'<br/>';

                if($product->shipping_ship_in_care != '')
                    $str .='<strong>Care of: </strong>'.$product->shipping_ship_in_care.'<br/>';
				
				if($product->shipping_add1 != '' and $product->shipping_add2 != '')
					$str.=$product->shipping_add1.'<br/>'.$product->shipping_add2.'<br>';
				elseif($product->shipping_add1 != '')
					$str .=$product->shipping_add1.'<br>';					
				
				$str .=$product->shipping_city.', '.$product->shipping_state.' '.$product->shipping_zipcode . ' ' . $product->shipping_country;

                if($product->shipping_phone_number != '')
                    $str .= '<br/>' . $product->shipping_phone_number;

				$str .'</td>';
			}			

			$str .='</tr>'; 
		}
		$str .= '<tr><td colspan="2"></td><td>Sub Total : </td><td colspan="2">$'.$order->sub_total.'</td></tr>';
		if($order->discount > 0){
			$str .= '<tr><td colspan="2"></td><td>Discount : </td><td colspan="2">$'.$order->discount.'</td></tr>';
		}
		if($order->shipping_fee > 0){
			$str .= '<tr><td colspan="2"></td><td>Shipping : </td><td colspan="2">$'.$order->shipping_fee.'</td></tr>';
		}
		if($order->sales_tax > 0){
			$str .= '<tr><td colspan="2"></td><td>Sales Tax : </td><td colspan="2">$'.$order->sales_tax.'</td></tr>';
		}
		$str .= '<tr><td colspan="2"></td><td>Total : </td><td colspan="2">$'.$order->total.'</td></tr></table>';		
				
		// Create Pdf code below //
		
		$url = url("invoice_receipt/".$order_id);
		
		$file_name = 'EasyOrderBanners_Order_#'.$order_id.'_Receipt.pdf';
		$file_path = "public/pdf/front/order_receipt/".$file_name;
				
		$exe = config('constants.phantomjs_path');
		$output = exec("$exe --ssl-protocol=any --ignore-ssl-errors=yes pages.js  $url $file_path 2>&1");
		
		// Create pdf code end //
		
		// Send mail with attechment function code below //		
		if($type=='estimate')	
		{	
			$bcc[] = config('constants.store_email');		
			$url = url('order-payment/'.$order->id);
			$params = array(
							'slug'=>'order_payment_link_to_user',
							'to'=>$order->customer->email,
							'bcc'=>$bcc,						
							'pdf'=>$file_path,
							'params'=>array(
										'{{name}}'=>$order->customer->fname.' '.$order->customer->lname,
										'{{url}}'=>$url,
										'{{details_rows}}'=>$str,
										'{{ADMIN_MAIL}}'=>'<a href="mailto:'.config('constants.ADMIN_MAIL').'">'.config('constants.ADMIN_MAIL').'</a>',
										'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
										'{{payment_link}}'=>'<a href="'.$url.'">Click Here</a>',
										'{{payment_url}}'=>$url										
										)
							);
			$obj = new Controller();
			$obj->sendMail($params);
		}
		else
		{
			$bcc[] = config('constants.store_email');
			$params = array(
							'slug'=>'order_receipt',
							'to'=>$order->customer->email,						
							'bcc'=>$bcc,						
							'pdf'=>$file_path,
							'params'=>array(
										'{{order_id}}'=>$order_id,
										'{{name}}'=>$order->customer->fname.' '.$order->customer->lname,
										'{{site_name}}'=>config('constants.SITE_NAME'),
										'{{details_rows}}'=>$str,
										'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
										'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
										)
							);
			$obj = new Controller();
			$obj->sendMail($params);
		}
    }

    /**
     * @param $value
     * @return float
     */
    public static function reverseNumberFormat($value)
    {
        return floatval(preg_replace('/[^\d\.\-]/', '', $value));
    }
}