<?php
namespace App\Helpers;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use DB;
use App\Orders;
use App\Products;
use App\ProductVariant;
use App\ProductVariantValues;

class PaymentAuthorize
{
    public static function payment($loginId,$transactionId,$amount,$cardNumber,$cardCVV,$cardExpire,$billing_add,$shipping_add,$order_id){
		$res['status'] = '0'; 
		// Common setup for API credentials  
		$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();   
		$merchantAuthentication->setName($loginId);   
		$merchantAuthentication->setTransactionKey($transactionId);   
		$refId = 'ref' . time();

		// Create the payment data for a credit card
		$creditCard = new AnetAPI\CreditCardType();
		$creditCard->setCardNumber($cardNumber);  
		$creditCard->setExpirationDate($cardExpire);
		$creditCard->setCardCode($cardCVV);
		$paymentOne = new AnetAPI\PaymentType();
		$paymentOne->setCreditCard($creditCard);

		// Create the Bill To info
		$billto = new AnetAPI\CustomerAddressType();
		$billto->setFirstName($billing_add['fname']);
		$billto->setLastName($billing_add['lname']);
		//$billto->setCompany($billing_add['company']);
		$billto->setAddress($billing_add['address']);
		$billto->setCity($billing_add['city']);
		$billto->setState($billing_add['state']);
		$billto->setZip($billing_add['zipcode']);
		$billto->setCountry($billing_add['country']);
		$billto->setPhoneNumber($billing_add['phone_number']);
		$billto->setEmail(\Auth::user()->email);		

		// Create the ShipTo To info
		$shipto = new AnetAPI\CustomerAddressType();
		$shipto->setFirstName($shipping_add['fname']);
		$shipto->setLastName($shipping_add['lname']);
		//$shipto->setCompany($shipping_add['company']);
		$shipto->setAddress($shipping_add['address']);
		$shipto->setCity($shipping_add['city']);
		$shipto->setState($shipping_add['state']);
		$shipto->setZip($shipping_add['zipcode']);
		$shipto->setCountry($shipping_add['country']);
		
		// Create order information
		$order = new AnetAPI\OrderType();
		$order->setInvoiceNumber($order_id);
		//$order->setDescription("Order On EasyOrderBanner.");
		
		// Add Line Items		
		/* foreach(session()->get('cart') as $product){
			$product_detail = Products::findOrFail($product['product_id']);
			$description = '';
			if(!empty($product['variants'])){
				$description .= 'Varients: ';
				foreach($product['variants'] as $key=>$val){
					$variant_name = ProductVariant::findOrFail($key);
					$variant_option = ProductVariantValues::findOrFail($val);
					$description .= $variant_name->name.'->'.$variant_option->value.',';
				}
				$description = trim($description,',');
				$description .= ',';
			}
			if(!empty($product['object_data']['custom_option'])){
				$description .= 'Options : ';
				foreach($product['object_data']['custom_option'] as $key=>$val){
					if(array_key_exists('name',$val)){
						$value = explode('__',$val['value']);
						$description .= $val['name'].'->'.$value[0].',';
					}else{
						//$description .= 'Turnaround Time ->'.$val['value'].',';
					}
				}
				$description = trim($description,',');				
			}
			//echo $description;die;
			$lineItem = new AnetAPI\LineItemType();
			$lineItem->setItemId("#".$product['product_id']);
			$lineItem->setName(substr($product_detail->name,0,31));
			$lineItem->setDescription(substr($description,0,255));
			$lineItem->setQuantity($product['quantity']);
			$lineItem->setUnitPrice($product['total']);
			$lineItem->setTaxable(0); // 1 Yes 0 for no
			$lineItem_Array[] = $lineItem;
		} */
		
		// Create a transaction
		$transactionRequestType = new AnetAPI\TransactionRequestType();
		$transactionRequestType->setTransactionType("authCaptureTransaction");   
		$transactionRequestType->setAmount($amount);
		$transactionRequestType->setPoNumber('PO-'.$order_id);
		$transactionRequestType->setBillTo($billto);
		$transactionRequestType->setShipTo($shipto);
		//$transactionRequestType->setLineItems($lineItem_Array);
		$transactionRequestType->setPayment($paymentOne);
		$transactionRequestType->setOrder($order);
		
		try{
			$request = new AnetAPI\CreateTransactionRequest();
			$request->setMerchantAuthentication($merchantAuthentication);
			$request->setRefId( $refId);
			$request->setTransactionRequest($transactionRequestType);
			$controller = new AnetController\CreateTransactionController($request);

			if (\App::environment('production')) {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
            } else {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            }

			if ($response != null){
				$tresponse = $response->getTransactionResponse();
				//pr($tresponse);die;
				
				if (($tresponse != null) && ($tresponse->getResponseCode()=="1")){
					$res['status'] = 'success';
					$res['transaction']['code'] = $tresponse->getMessages()[0]->getCode();
					$res['transaction']['message'] = $tresponse->getMessages()[0]->getDescription().' Thank you for your payment.';
					$res['transaction']['auth_code'] = $tresponse->getAuthCode();
					$res['transaction']['transaction_id'] = $tresponse->getTransId();
				}else{
					$res['status'] = '0';
                    $res['transaction']['auth_code'] = !empty($tresponse) ? $tresponse->getAuthCode() : null;
                    $res['transaction']['transaction_id'] = !empty($tresponse) ? $tresponse->getAuthCode() : null;

					if($response->getMessages()->getMessage()[0]->getCode() !== null){
						$res['code'] = $response->getMessages()->getMessage()[0]->getCode();
					}else{
						$res['code'] = '';
					}
					
					if($response->getMessages()->getMessage()[0]->getText() !== null){
						$res['message'] = $response->getMessages()->getMessage()[0]->getText();
					}else{
						$res['message'] = '';
					}

					if(!empty($tresponse) and !empty($tresponse->getErrors()[0]) and $tresponse->getErrors()[0]->getErrorCode() !== null){
						$res['transaction']['code'] = $tresponse->getErrors()[0]->getErrorCode();
					}else{
						$res['transaction']['code'] = '';
					}
					
					if(!empty($tresponse) and !empty($tresponse->getErrors()[0]) and $tresponse->getErrors()[0]->getErrorText() !== null){
						$res['transaction']['message'] = $tresponse->getErrors()[0]->getErrorText();
					}else{
						$res['transaction']['message'] = '';
					}
				}
			}
		}catch (Exception $e) {
			//pr($e);die;
		}

		return $res;
    }
	
	public static function payment_link($loginId,$transactionId,$amount,$cardNumber,$cardCVV,$cardExpire,$order_id){
		
		$order_detail = Orders::where('id',$order_id)->with(['customer','orderProduct','orderProductOptions','orderAddress'])->first();
		//pr($order_detail);die;
		
		$res['status'] = '0'; 
		// Common setup for API credentials  
		$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();   
		$merchantAuthentication->setName($loginId);   
		$merchantAuthentication->setTransactionKey($transactionId);   
		$refId = 'ref' . time();

		// Create the payment data for a credit card
		$creditCard = new AnetAPI\CreditCardType();
		$creditCard->setCardNumber($cardNumber);  
		$creditCard->setExpirationDate($cardExpire);
		$creditCard->setCardCode($cardCVV);
		$paymentOne = new AnetAPI\PaymentType();
		$paymentOne->setCreditCard($creditCard);

		// Create the Bill To info
		$billto = new AnetAPI\CustomerAddressType();
		$billto->setFirstName($order_detail->orderAddress->billing_fname);
		$billto->setLastName($order_detail->orderAddress->billing_lname);
		//$billto->setCompany($order_detail->customer->company_name);
		$billto->setAddress($order_detail->orderAddress->billing_add1.','.$order_detail->orderAddress->billing_add2);
		$billto->setCity($order_detail->orderAddress->billing_city);
		$billto->setState($order_detail->orderAddress->billing_state);
		$billto->setZip($order_detail->orderAddress->billing_zipcode);
		$billto->setCountry($order_detail->orderAddress->billing_country);
		$billto->setPhoneNumber($order_detail->customer->phone_number);
		$billto->setEmail($order_detail->customer->email);		

		// Create the ShipTo To info
		$shipto = new AnetAPI\CustomerAddressType();
		$shipto->setFirstName($order_detail->orderAddress->shipping_fname);
		$shipto->setLastName($order_detail->orderAddress->shipping_lname);
		//$shipto->setCompany($order_detail->customer->company_name);
		$shipto->setAddress($order_detail->orderAddress->shipping_add1.','.$order_detail->orderAddress->shipping_add2);
		$shipto->setCity($order_detail->orderAddress->shipping_city);
		$shipto->setState($order_detail->orderAddress->shipping_state);
		$shipto->setZip($order_detail->orderAddress->shipping_zipcode);
		$shipto->setCountry($order_detail->orderAddress->shipping_country);
		
		// Create order information
		$order = new AnetAPI\OrderType();
		$order->setInvoiceNumber($order_id);
		//$order->setDescription("Order On EasyOrderBanner.");
		
		// Add Line Items		
		/* foreach($order_detail->orderProduct as $product){
			$description = '';
			$variant_str = '';
			$option_str = '';
			foreach($order_detail->orderProductOptions as $option){
				if($option->type == 2){
					$variant_str .= $option->custom_option_name.'->'.$option->value.',';
				}else{
					$option_str .= $option->custom_option_name.'->'.$option->value.',';
				}
			}
			$variant_str = trim($variant_str,',');
			$option_str = trim($option_str,',');
			
			if(!empty($variant_str)){
				$description .= 'Varients: '.$variant_str;
			}
			
			if(!empty($option_str)){
				$description .= 'Options : '.$option_str;
			}
			
			$lineItem = new AnetAPI\LineItemType();
			$lineItem->setItemId("#".$product->product_id);
			$lineItem->setName(substr($product->product->name,0,31));
			$lineItem->setDescription(substr($description,0,255));
			$lineItem->setQuantity($product->qty);
			$lineItem->setUnitPrice($product->price);
			$lineItem->setTaxable(0); // 1 Yes 0 for no
			$lineItem_Array[] = $lineItem;
		} */
		
		// Create a transaction
		$transactionRequestType = new AnetAPI\TransactionRequestType();
		$transactionRequestType->setTransactionType("authCaptureTransaction");   
		$transactionRequestType->setAmount($amount);
		$transactionRequestType->setPoNumber('PO-'.$order_id);
		$transactionRequestType->setBillTo($billto);
		$transactionRequestType->setShipTo($shipto);
		//$transactionRequestType->setLineItems($lineItem_Array);
		$transactionRequestType->setPayment($paymentOne);
		$transactionRequestType->setOrder($order);
		//pr($transactionRequestType);die();

		
		try{
			$request = new AnetAPI\CreateTransactionRequest();
			$request->setMerchantAuthentication($merchantAuthentication);
			$request->setRefId( $refId);
			$request->setTransactionRequest($transactionRequestType);
			$controller = new AnetController\CreateTransactionController($request);

            if (\App::environment('production')) {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
            } else {
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            }

			if ($response != null){
				$tresponse = $response->getTransactionResponse();
				//pr($tresponse);die;
				
				if (($tresponse != null) && ($tresponse->getResponseCode()=="1")){
					$res['status'] = 'success';
					$res['transaction']['code'] = $tresponse->getMessages()[0]->getCode();
					$res['transaction']['message'] = $tresponse->getMessages()[0]->getDescription().' Thank you for your payment.';
					$res['transaction']['auth_code'] = $tresponse->getAuthCode();
					$res['transaction']['transaction_id'] = $tresponse->getTransId();
				}else{
					$res['status'] = '0';
					if($response->getMessages()->getMessage()[0]->getCode() !== null){
						$res['code'] = $response->getMessages()->getMessage()[0]->getCode();
					}else{
						$res['code'] = '';
					}
					
					if($response->getMessages()->getMessage()[0]->getText() !== null){
						$res['message'] = $response->getMessages()->getMessage()[0]->getText();
					}else{
						$res['message'] = '';
					}
					
					if(!empty($tresponse) and $tresponse->getErrors()[0]->getErrorCode() !== null){
						$res['transaction']['code'] = $tresponse->getErrors()[0]->getErrorCode();
					}else{
						$res['transaction']['code'] = '';
					}
					
					if(!empty($tresponse) and $tresponse->getErrors()[0]->getErrorText() !== null){
						$res['transaction']['message'] = $tresponse->getErrors()[0]->getErrorText();
					}else{
						$res['transaction']['message'] = '';
					}
				}
			}
		}catch (Exception $e) {
			//pr($e);die;
		}
		return $res;
    }
}