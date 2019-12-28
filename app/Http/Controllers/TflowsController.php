<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Products;
use App\Orders;
use App\OrderProducts;
use DB;

class TflowsController extends Controller
{
    public function __construct()
    {
        
    }

    public function job_approve(){

    	$post = $_POST;    	

		\DB::table('tflowapi')->insert(
    		['job_id' => @$post['job_id'],'event_name' => @$post['event_name'],'data' => json_encode($post),'date'=>date('Y-m-d H:i:s')]
		);

        $data = $_POST;
        //pr($data);die();

        if($data['event_name']=='job.reject' || $data['event_name']=='job.reject_from_on_hold')
        {
            $orderProduct = OrderProducts::where('tflow_job_id',$data['job_id'])->first();
            if($orderProduct)
            {
                $orderProduct->art_work_status = 4;            
                $orderProduct->save();

                //$this->sendJobStatusMail($orderProduct,4);
            }    
        }

        if($data['event_name']=='job.place_on_hold' || $data['event_name']=='job.undo_accept' || $data['event_name']=='job.undo_approve')
        {
            $orderProduct = OrderProducts::where('tflow_job_id',$data['job_id'])->first();
            if($orderProduct)
            {
                $orderProduct->art_work_status = 5;            
                $orderProduct->save();

                if($data['event_name']=='job.undo_accept' || $data['event_name']=='job.undo_approve')
                {
                	//$this->sendJobStatusMail($orderProduct,5);
            	}
            }
        }

        if($data['event_name']=='job.approve' || $data['event_name']=='job.accept')
        {
            $orderProduct = OrderProducts::where('tflow_job_id',$data['job_id'])->first();
            if($orderProduct)
            {
                $orderProduct->art_work_status = 6;            
                $orderProduct->save();
				
				// Below Code for check all order products are approved or not //
				
				/* $check_status = DB::select('SELECT count(*) as art_work_status , (SELECT COUNT(*) FROM `order_products` WHERE `order_id` = '.$orderProduct->order_id.') as total_product FROM `order_products` WHERE `order_id` = '.$orderProduct->order_id.' AND art_work_status =6');
				if($check_status[0]->art_work_status == $check_status[0]->total_product){
					Orders::where('id',$orderProduct->order_id)->update(['customer_status'=>4]);
				} */
				
				$all_artwork = 0;
				$order = Orders::where('id', $orderProduct['order_id'])->with(['orderProduct'])->first();	
				foreach($order->orderProduct as $product){
					if($product->product->no_artwork_required == 1){
						$all_artwork++;
					}
					else if($product->art_work_status == 6){					
						$all_artwork++;
					}
				}

				if(count($order->orderProduct) == $all_artwork)
				{
					Orders::where('id',$orderProduct->order_id)->update(['customer_status'=>4]);
					$this->sendOrderStatusMail($orderProduct->order_id,4);
				}

                //$this->sendJobStatusMail($orderProduct,6);
            }
        }        

		return 'Tflow Api execuated';
    }

    /**
    send mail to customer after change status on tflow for job
    @params $orderProductId id of the order product table
    @params $statusCode status code for artwork
    @return boolen
    */

    public function sendJobStatusMail($orderProductId="",$statusCode=""){
		$detail =  OrderProducts::select('order_products.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email')->where('order_products.id', $orderProductId->id)->with('product')->leftJoin('orders as order', 'order_products.order_id', '=', 'order.id')->leftJoin('users as user', 'order.user_id', '=', 'user.id')->first();
		
		$str = '<ul>';
		$str .= '<li><b>Product Quantity : </b>'.$detail->qty.'</li>';
		$str .= '<li><b>Product Name : </b>'.$detail->product->name.'</li>';
		$str .= '</ul>';
		
		$status_value = config('constants.art_work_status.'.$statusCode);
		
		$params = array('slug'=>'order_status_changed',
					'to'=>$detail->email,
					'params'=>array(
								'{{name}}'=>$detail->customer_name,
								'{{status}}'=>$status_value,
								'{{detail}}'=>$str,
								'{{store_name}}'=>config('constants.store_name'),
								'{{store_phone_number}}'=>config('constants.store_phone_number'),
								'{{store_email}}'=>config('constants.store_email'),
								));
		parent::sendMail($params);
    }

    public function sendOrderStatusMail($order_id,$status=4)
	{	
		$detail_products = OrderProducts::select('order_products.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email')->where('order_id', $order_id)->with('product')->leftJoin('orders as order', 'order_products.order_id', '=', 'order.id')->leftJoin('users as user', 'order.user_id', '=', 'user.id')->get();
		
		$str .= '<table border="1">';
		$str .= '<tr><th>Sr.No</th><th>Quantity</th><th>Product Name</th></tr>';
		$i = 1;
		$customer_name='';
		$email='';
		$status='In Production';
		foreach($detail_products as $product){
			$customer_name = $product->customer_name;
			$email = $product->email;
			$str .= '<tr>';
			$str .= '<td>'.$i.'</td>';
			$str .= '<td>'.$product->qty.'</td>';
			$str .= '<td>'.$product->product->name.'</td>';
			$str .= '</tr>';
			$i++;
		}
		$str .= '</table>';
		
		$email_slug = 'order_status_changed_in_production';		
				
		$params = array(
			'slug'=>$email_slug,
			'to'=>$email,
			'params'=>array(
						'{{order_id}}'=>$order_id,
						'{{name}}'=>$customer_name,
						'{{status}}'=>$status,
						'{{detail}}'=>$str,
						'{{store_name}}'=>config('constants.store_name'),
						'{{store_phone_number}}'=>config('constants.store_phone_number'),
						'{{store_email}}'=>config('constants.store_email'),
			)
		);
		parent::sendMail($params);
	}
    
}
