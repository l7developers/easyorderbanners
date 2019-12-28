<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Emails;
use App\Orders;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function sendMail($params){
		$to = $params['to'];
		$cc = '';
		$bcc = '';
		$pdf = '';
		if(isset($params['cc']) && $params['cc']!=""){
			$cc = $params['cc'];
		}
		if(isset($params['bcc']) && $params['bcc']!=""){
			$bcc = $params['bcc'];
		}
		
		if(isset($params['pdf']) && $params['pdf']!=""){
			$pdf = $params['pdf'];
		}

		$data = DB::table('emails')->where('slug', $params['slug'])->get()->toArray();
		
		$message_body = $data[0]->message;
		$subject = $data[0]->subject;
		
		foreach($params['params'] as $key=>$val){
			if (strpos($message_body,$key) !== false) {
				$message_body = str_replace($key,$val,$message_body);				
			}

			if (strpos($subject,$key) !== false) {				
				$subject = str_replace($key,$val,$subject);
			}
		}
		
		/*********** Below Code For Set Global Hook Variable ***********/
		if(isset($params['params']['{{order_id}}']) && $params['params']['{{order_id}}'] >=1)
		{
			$order = Orders::where('id', $params['params']['{{order_id}}'])->with(['customer','agent'])->first();
			
			if(count($order->agent) > 0){
				$message_body = str_ireplace('{{AGENT_NAME}}',$order->agent->fname.' '.$order->agent->lname,$message_body);
				$message_body = str_ireplace('{{AGENT_MAIL}}',$order->agent->email,$message_body);
				$message_body = str_ireplace('{{AGENT_DIRECT}}',$order->agent->direct,$message_body);
			}
		}
		
		$message_body = str_ireplace('{{SITE_URL}}',config('constants.SITE_URL'),$message_body);
		$message_body = str_ireplace('{{SITE_LOGIN_URL}}',config('constants.SITE_URL').'/login',$message_body);
		$message_body = str_ireplace('{{SITE_NAME}}',config('constants.SITE_NAME'),$message_body);
		$message_body = str_ireplace('{{ADMIN_NAME}}',config('constants.ADMIN_NAME'),$message_body);
		$message_body = str_ireplace('{{ADMIN_MAIL}}',config('constants.ADMIN_MAIL'),$message_body);
		$message_body = str_ireplace('{{store_name}}',config('constants.store_name'),$message_body);
		$message_body = str_ireplace('{{store_phone_number}}',config('constants.store_phone_number'),$message_body);
		$message_body = str_ireplace('{{store_email}}',config('constants.store_email'),$message_body);
		
		/*********** End Code For Set Global Hook Variable ***********/

		$message_body = '<div style="font-family: serif;">'.$message_body.'</div>'; 
		
		\Mail::send([], [], function ($message) use ($to,$cc,$bcc,$pdf,$data,$subject,$message_body) {
			$message->from(config('constants.ADMIN_MAIL'),config('constants.SITE_NAME') );
			$message->to($to)->subject($subject);
			if($cc!="")
			{
				$message->cc($cc);
			}
			if($bcc!="")
			{
				$message->bcc($bcc);
			}
			
			if($pdf!="")
			{
				$new_name = str_replace("public/pdf/front/order_receipt/", "", $pdf);
				$message->attach($pdf, [
                        'as' => $new_name,
                        'mime' => 'application/pdf',
                    ]);
			}

			$message->setBody($message_body, 'text/html');
		});
	}
}
