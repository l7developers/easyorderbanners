<?php
namespace App\Helpers;

use \Ups;
use \Ups\Entity\Address;
use \Ups\SimpleAddressValidation;
use \Ups\Entity\Shipment;
use \Ups\Entity\Package;
use \Ups\Entity\ShipFrom;
use \Ups\Entity\UnitOfMeasurement;

use Exception;

class UPSShipping
{
    public static function validateAddress($zipcode,$stateCode=null,$city=null){
		//  Simple Address Validation //
		
		$address = new Address();
		$address->setPostalCode($zipcode);
		
		if($stateCode != null)
		$address->setStateProvinceCode($stateCode);
		
		if($city != null)
		$address->setCity($city);
		
		$address->setCountryCode('US');

		$av = new SimpleAddressValidation(config('constants.Ups_accessKey'), config('constants.Ups_userId'), config('constants.Ups_password'), config('constants.Ups_SandBox'));
		try {
			 $response = $av->validate($address);
			 //pr($response);
			 if(count($response) > 0){
				 return array('status'=>true,'msg'=>'Valid address');
			 }else{
				return array('status'=>true,'msg'=>'Valid address');
			 }
		} catch (Exception $e) {
			//var_dump($e);
			return array('status'=>false,'msg'=>$e->getMessage());
		}
     }
	 
	public static function RateCalculate($zipcode,$weight,$option){
		//  Rate Calculation //
		
		$rate = new \Ups\Rate(config('constants.Ups_accessKey'), config('constants.Ups_userId'), config('constants.Ups_password'), config('constants.Ups_SandBox'));

		try {
			$shipment = new Shipment();
			
			$address = new Address();
			$address->setPostalCode('91911');
			$address->setStateProvinceCode('CA');
			$address->setCity('Chula Vista');
			$address->setCountryCode('US');
			
			$shipFrom = new ShipFrom();
			$shipFrom->setAddress($address);
			

			$shipment->setShipFrom($shipFrom);

			$shipTo = $shipment->getShipTo();
			$shipTo->setCompanyName('Test Ship To');
			$shipToAddress = $shipTo->getAddress();
			$shipToAddress->setPostalCode($zipcode);
			
			// Set service
			$service = new \Ups\Entity\Service;
			$service->setCode($option);
			$service->setDescription($service->getName());
			$shipment->setService($service);
			
			if($weight > 150)
			{
				$package_cnt = ceil($weight/150);
				for($i=1;$i<=$package_cnt;$i++)
				{
					$package_weight = 150;
					if($i==$package_cnt)
					{
						$package_weight = $weight - (150 * ($i-1));
					}					
					$package = new Package();
					$package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_PACKAGE);
					$package->getPackageWeight()->setWeight($package_weight);
				
					// if you need this (depends of the shipper country)
					$weightUnit = new UnitOfMeasurement;
					$weightUnit->setCode(UnitOfMeasurement::UOM_LBS);
					$package->getPackageWeight()->setUnitOfMeasurement($weightUnit);
				
					$shipment->addPackage($package);
				}
			}
			else
			{
				$package = new Package();
				$package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_PACKAGE);
				$package->getPackageWeight()->setWeight($weight);
			
				// if you need this (depends of the shipper country)
				$weightUnit = new UnitOfMeasurement;
				$weightUnit->setCode(UnitOfMeasurement::UOM_LBS);
				$package->getPackageWeight()->setUnitOfMeasurement($weightUnit);
			
				$shipment->addPackage($package);
			}
			//pr($shipment);

			$rate_calculation = $rate->getRate($shipment);
			//pr($rate_calculation);
			//pr($rate_calculation->RatedShipment[0]->RatedPackage[0]->TotalCharges);
			$rate_res = $rate_calculation->RatedShipment[0]->TotalCharges;
			
			return array('status'=>true,'msg'=>'Rate Calculation Done.','weight'=>$weight,'res'=>$rate_res);
		} catch (Exception $e) {
			//pr($e->getMessage());
			//pr($e); 
			//var_dump($e);
			return array('status'=>false,'msg'=>$e->getMessage());
		}
     }

	public function shipping($address,$orderId,$item_id,$weight,$option){
		// Start shipment
		//pr($address['add1']);die;
		
		$name = $address['name'];
		$email = $address['email'];
		$phone_number = $address['phone_number'];
		$company_name = $address['company_name'];
		$add1 = $address['add1'];
		$add2 = $address['add2'];
		$zipcode = $address['zipcode'];
		$city = $address['city'];
		$state = $address['state'];
		$country = $address['country'];
		
		
		$shipment = new Shipment;

		// Set shipper
		$shipper = $shipment->getShipper();
		$shipper->setShipperNumber(config('constants.ShipperNumber'));
		$shipper->setName(config('constants.ShipperDetail.name'));
		$shipper->setAttentionName(config('constants.ShipperDetail.attentionName'));
		$shipperAddress = $shipper->getAddress();
		$shipperAddress->setAddressLine1(config('constants.ShipperDetail.address'));
		$shipperAddress->setPostalCode(config('constants.ShipperDetail.postalCode'));
		$shipperAddress->setCity(config('constants.ShipperDetail.city'));
		$shipperAddress->setStateProvinceCode(config('constants.ShipperDetail.provinceCode')); // required in US
		$shipperAddress->setCountryCode(config('constants.ShipperDetail.countryCode'));
		$shipper->setAddress($shipperAddress);
		$shipper->setEmailAddress(config('constants.ShipperDetail.email')); 
		$shipper->setPhoneNumber(config('constants.ShipperDetail.phone_number'));
		$shipment->setShipper($shipper);

		// To address
		$address = new Address();
		$address->setAddressLine1($add1);
		
		if(!empty($add2))
		$address->setAddressLine2($add2);
		
		$address->setPostalCode($zipcode);
		$address->setCity($city);
		$address->setStateProvinceCode($state);  // Required in US
		$address->setCountryCode($country);
		$shipTo = new \Ups\Entity\ShipTo();
		$shipTo->setAddress($address);
		
		if(!empty($company_name))
		$shipTo->setCompanyName($company_name);
		else
		$shipTo->setCompanyName($name);
		
		$shipTo->setAttentionName($name);
		$shipTo->setEmailAddress($email); 
		$shipTo->setPhoneNumber($phone_number);
		$shipment->setShipTo($shipTo);
		
		// Set service
		$service = new \Ups\Entity\Service;
		
		//$service->setCode(\Ups\Entity\Service::S_GROUND);
		$service->setCode($option);
		$service->setDescription($service->getName());
		$shipment->setService($service);
		
		// Set description
		$shipment->setDescription('Item ID #'.$item_id);
		
		// Add Package
		$package = new \Ups\Entity\Package();
		$package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_PACKAGE);
		$package->getPackageWeight()->setWeight($weight);
		$unit = new \Ups\Entity\UnitOfMeasurement;
		$unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_LBS);
		$package->getPackageWeight()->setUnitOfMeasurement($unit);

		// Add descriptions because it is a package
		$package->setDescription('Item ID #'.$item_id);

		// Add this package
		$shipment->addPackage($package);

		$order_id='EOB-'.$item_id;
		// Set Reference Number
		$referenceNumber = new \Ups\Entity\ReferenceNumber;
		$referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_PURCHASE_ORDER_NUMBER);
		$referenceNumber->setValue($order_id);

		// Set payment information
		$shipment->setPaymentInformation(new \Ups\Entity\PaymentInformation('prepaid', (object)array('AccountNumber' => config('constants.AccountNumber'))));

		// Get shipment info
		try {
			$api = new Ups\Shipping(config('constants.Ups_accessKey'), config('constants.Ups_userId'), config('constants.Ups_password'), config('constants.Ups_SandBox'));  

			$confirm = $api->confirm(\Ups\Shipping::REQ_NONVALIDATE, $shipment);
			if ($confirm) {
				$accept = $api->accept($confirm->ShipmentDigest);
				//pr($accept);die;
				$destinationPath = public_path('/uploads/orders/ShippingLabels/');
				$image_name = 'EOB-'.$orderId.'-'.$accept->PackageResults->TrackingNumber. ".gif";
				$label_file = $destinationPath.$image_name; 
				$base64_string = $accept->PackageResults->LabelImage->GraphicImage;
				$ifp = fopen($label_file, 'wb');
				fwrite($ifp, base64_decode($base64_string));
				fclose($ifp);
				//pr($accept);
				return array('status'=>'success','tracking_number'=>$accept->PackageResults->TrackingNumber,'image_name'=>$image_name);
			}
		} catch (\Exception $e) {
			//echo "<h2>Shipping Error</h2>";
			//pr($e);
			return array('status'=>'','msg'=>$e->getMessage());
		}
	}
}