<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Orders;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function __construct(){
        
    }
	
	public function sendMail($params){
		$to = $params['to'];
		$bcc = '';
		$cc = '';
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

		/*********** Below Code For Set Global Hook Variable ***********/
		if(isset($params['params']['{{order_id}}']) && $params['params']['{{order_id}}'] >=1)
		{
			$order = Orders::where('id', $params['params']['{{order_id}}'])->with(['customer','agent'])->first();
			if(count($order->agent) > 0){
				$message_body = str_replace('{{AGENT_NAME}}',$order->agent->fname.' '.$order->agent->lname,$message_body);
				$message_body = str_replace('{{AGENT_MAIL}}',$order->agent->email,$message_body);
				$message_body = str_replace('{{AGENT_DIRECT}}',$order->agent->direct,$message_body);
			}
		}
		
		foreach($params['params'] as $key=>$val){
			if (strpos($message_body,$key) !== false) {
				$message_body = str_replace($key,$val,$message_body);
				$subject = str_replace($key,$val,$subject);
			}
		}		
		
		$message_body = str_replace('{{SITE_URL}}',config('constants.SITE_URL'),$message_body);
		$message_body = str_replace('{{SITE_LOGIN_URL}}',config('constants.SITE_URL').'/login',$message_body);
		$message_body = str_replace('{{SITE_NAME}}',config('constants.SITE_NAME'),$message_body);
		$message_body = str_replace('{{ADMIN_NAME}}',config('constants.ADMIN_NAME'),$message_body);
		$message_body = str_replace('{{ADMIN_MAIL}}',config('constants.ADMIN_MAIL'),$message_body);
		
		/*********** End Code For Set Global Hook Variable ***********/
		
		$message_body = '<div style="font-family:serif;">'.$message_body.'</div>';
		
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
				$message->attach($pdf, [
                        'as' => 'Order Receipt.pdf',
                        'mime' => 'application/pdf',
                    ]);
			}

			$message->setBody($message_body, 'text/html');
		});
	}
}
