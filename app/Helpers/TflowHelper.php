<?php
namespace App\Helpers;

use App\Orders;
use App\Products;
use App\OrderProducts;
use App\OrderPoDetails;
use Exception;
use App\Helpers\Tflow\Client as ApiClient;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use DB;

class TflowHelper
{
    /**
     * @param $order_id
     * @return array
     */
    public static function uploadToTflow($order_id)
    {
		$res = [];
		$res['errorMsg'] = '';
		
    	$order = Orders::where('id', $order_id)
            ->with(['customer','orderProduct','orderProductOptions','orderAddresses','files'])
            ->first();
		
		$apiClient = new ApiClient(config('constants.Tflow_baseUri'), config('constants.Tflow_clientId'), config('constants.Tflow_clientSecret'));

		if($order->tflow_order_id=="") {

            if (\App::environment('production')) {
                $orderName = 'Order ';
            } else {
                $orderName = 'Order TESTING';
            }

			try {
				$orderParams = [
				    'name' => $orderName.$order->id,
		        	'planned_number_of_jobs' =>count($order->orderProduct),
		        	'description' =>$order->customer->fname.' '.$order->customer->lname.', '.$order->customer->company_name,
		        	'ship_date' => (new \DateTime())->add(new \DateInterval('P3D'))->format(\DateTime::ISO8601),
		        	'client_id' => 1,                                            // will be replaced with actual value
		        	'product_id' => 1,
		        	'assignments' =>array('instance_user_ids'=>[101]),
		        	'tflows' => [],
		        	'props' => [
		            	'rep_name' => '',		            	
		        	],
	    		];

	    		$orderData = $apiClient->createOrder($orderParams);
	    		$order->tflow_order_id = $orderData['id'];
				$order->save();
			}
			catch(Exception $e){
				$errorMsg =  $e->getMessage();
                \Log::error($errorMsg);
				$res['errorMsg'] .= $errorMsg."\n";
			} 			
		}

        $credentials = new Credentials(config('constants.aws_key'), config('constants.aws_secret'));
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'credentials' => $credentials
        ]);

        $s3->registerStreamWrapper();

		$files = $order->files;
		foreach($files as $file){
			foreach($order->orderProduct as $orderProduct){
				if($orderProduct->id == $file->order_product_id  && $orderProduct->tflow_job_id=="") {

					try{
						$width = '';
						$height = '';
						$option_str = '';
						foreach($order->orderProductOptions as $option){
							if($file->order_product_id == $option->order_product_id){
								if($option->custom_option_name == 'Width'){
									$width = $option->value;
									$width = ft2Cm($width);
								}
								if($option->custom_option_name == 'Height'){
									$height = $option->value;
									$height = ft2Cm($height);
								}

								if($option->custom_option_field_group == 'finishing'){
									$option_str .= $option->custom_option_name.':'.$option->value.';';
								}
							}
						}
						
						$address_str = '';
						$address_str .= $orderProduct->shipping_add1;
						if(!empty($orderProduct->shipping_add2)){
							$address_str .= ', '.$orderProduct->shipping_ad2;
						}
						$address_str .= ','.$orderProduct->shipping_city.' '.$orderProduct->shipping_state.' '.$orderProduct->shipping_zipcode;

                        if (\App::environment('production')) {
                            $jobName = 'Job ';
                        } else {
                            $jobName = 'Job TESTING';
                        }

						$jobParams = [
							'name' => $jobName.$orderProduct->item_id.' For Order '.$order->id,
							'order_id' => $order->tflow_order_id,
							'description'=> $orderProduct->product->name,
							'notes'=>$orderProduct->comments,
							'priority' => 0,
							'product_id' => 1,
							'attach_proofs' => false,
							'ship_date' => (new \DateTime())->add(new \DateInterval('P3D'))->format(\DateTime::ISO8601),
							'assignments' => array('instance_user_ids'=>[101]),        
							'props'=>array(
								"print_width"=> $width,
								"print_height"=> $height,
								"sales_rep"=> "1",
								"quantity"=> $orderProduct->qty,
								"shipping_address"=> $address_str,
								"phone_contact"=> $order->customer->phone_number,
								"email_contact"=> $order->customer->email,     
								"finishings"=> $option_str,     
								"actual_print_size"=> $width."\"*".$height."\""    	
							) 
						];

						$jobData = $apiClient->createJob($jobParams);
						\Log::info('#### Create job: ' . $jobData['id']);

						$orderProduct->tflow_job_id = $jobData['id'];
						$orderProduct->save();
						
						// Check Order Po Details are Created or Not //
						if($orderProduct->po_id != ''){
							OrderPoDetails::where(['order_id'=>$order_id,'order_product_id'=>$orderProduct->id])->update(['tflow_job_id'=>$jobData['id']]);
						}

                        $fileStream = sprintf('s3://%s/%s', config('constants.s3_bucket_name'), $file->s3_key);
						$dt = $apiClient->uploadArtwork($jobData['id'], $fileStream);

                        $orderProduct = OrderProducts::where('id', $orderProduct->id)
                            ->first();

                        $orderProduct->art_work_status = 2;
                        $orderProduct->art_work_date = date('m-d-Y');
                        $orderProduct->save();
					}
					catch(Exception $e){
						$errorMsg =  $e->getMessage();
						\Log::error($errorMsg);
						$res['errorMsg'] .= $errorMsg."\n";
					}  		
					
				}
			}
		}		
		
		if(empty($res['errorMsg'])){
			$res['status'] = true;
		}else{
			$res['status'] = false;
		}
		
		return $res;
    }
    
}