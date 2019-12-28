<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\User;
use App\Designers;
use App\Vendors;
use App\Orders;
use App\OrderProducts;
use App\Events;
use App\Messages;
use App\State;
use Hash;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct(){
        
    }
	
	public function index(Request $request,$field='date',$sort='DESC'){
		// Below code for fatch order produts //

		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$designers = Designers::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('status', 1)->pluck('name','id')->all();
		
		$vendors = Vendors::select('id', DB::raw("concat(fname, ' ', lname) as name"),'company_name')->where('status', 1)->get();
		$temp = array();
		foreach($vendors as $vendor){
			if($vendor->company_name !=""){
				$temp[$vendor->id] = $vendor->company_name;
			}else{
				$temp[$vendor->id] = $vendor->name;
			}
		}
		$vendors = $temp;
		
		$limit = config('constants.ADMIN_DASHBOARD_LIMIT');
		
		$extra_admin = '';
		if($field == 'order'){
			$field = 'order_id';
			$extra_admin = 'order';
		}
		if($field == 'po'){
			$field = 'order_id';
			$extra_admin = 'po';
		}
		if($field == 'customer-status')
			$field = 'customer_status';
		
		if($field == 'artwork-status')
			$field = 'art_work_status';
		
		if($field == 'vendor-status')
			$field = 'vendor_status';
		
		if($field == 'payment-status')
			$field = 'payment_status';
		
		if($field == 'date')
			$field = 'created_at';
		
		if($field == 'due-date')
			$field = 'due_date';
		
		$db = Orders::select('orders.*',DB::raw("concat(user.fname, ' ', user.lname) as customer_name"),'user.email as customer_email','user.phone_number as customer_phone_number','user.company_name as customer_company_name',DB::raw("concat(agent.fname, ' ', agent.lname) as agent_name"),DB::raw("concat(designer.fname, ' ', designer.lname) as designer_name"))
            ->with('OrderLineItems');
		
		$db->leftJoin('users as user', 'orders.user_id', '=', 'user.id');
		$db->leftJoin('users as agent', 'orders.agent_id', '=', 'agent.id');
		$db->leftJoin('designers as designer', 'orders.designer_id', '=', 'designer.id');
		
		
		if(\Auth::user()->role_id == 1) {
			$messages = DB::table('messages')->select('messages.*',DB::raw("concat(user.fname, ' ', user.lname) as sender_name"))->leftJoin('users as user', 'messages.from_id', '=', 'user.id')->where('from_id','!=',1)->limit(10)->orderBy('created_at','DESC')->oldest()->get();
			
			$start_date = date('Y-m-d', strtotime('-7 days'));
			$graph_orders = Orders::where("created_at",">", $start_date)->get();
			
			$db1 = Events::select('events.*',DB::raw("concat(user.fname, ' ', user.lname) as user_name"),DB::raw("concat(customer.fname, ' ', customer.lname) as customer_name"))->where('events.status',1);
			$db1->leftJoin('users as user', 'events.user_id', '=', 'user.id');
			$db1->leftJoin('users as customer', 'events.customer_id', '=', 'customer.id');
			$events = $db1->get();
		}else{
			$db->where('orders.agent_id',\Auth::user()->id);
			
			$messages = DB::table('messages')->select('messages.*',DB::raw("concat(user.fname, ' ', user.lname) as sender_name"))->leftJoin('users as user', 'messages.from_id', '=', 'user.id')->where('from_id','=',1)->where('to_id','=',\Auth::user()->id)->limit(10)->orderBy('created_at','DESC')->oldest()->get();
				
			$start_date = date('Y-m-d', strtotime('-7 days'));
			$graph_orders = Orders::where('agent_id','=',\Auth::user()->id)->where("created_at",">", $start_date)->get();
			
			$db1 = Events::where('user_id',\Auth::user()->id)->select('events.*',DB::raw("concat(user.fname, ' ', user.lname) as user_name"),DB::raw("concat(customer.fname, ' ', customer.lname) as customer_name"))->where('events.status',1);
			$db1->leftJoin('users as user', 'events.user_id', '=', 'user.id');
			$db1->leftJoin('users as customer', 'events.customer_id', '=', 'customer.id');
			$events = $db1->get();
		}
		
		$db->where('orders.status','>=',1);
		$db->where('orders.customer_status','>=',1);
		
		if($field == 'name'){
			$db->orderBy('user.fname',$sort);
		}else{
			$db->orderBy('orders.'.$field,$sort);
		}
		$orders = $db->paginate($limit);

		$sales_graph = array();
		$order_graph = array();
		$order_graph[date('d-M')] = 0;
		$sales_graph[date('Y-m')] = 0;
		
		for ($i = 1; $i < 7; $i++) {
			$order_graph[date('d-M', strtotime("-$i day"))] = 0;
			$sales_graph[date('d-M', strtotime("-$i day"))] = 0;
		}
		
		$order_graph = array_reverse($order_graph);
		$sales_graph = array_reverse($sales_graph);
		
		foreach($graph_orders as $order){
			$key = date('d-M',strtotime($order->created_at));
			if(array_key_exists($key,$order_graph)){
				$order_graph[$key] = $order_graph[$key]+1;
			}
			if(array_key_exists($key,$sales_graph)){
				$sales_graph[$key] = $sales_graph[$key]+$order->total;
			}
		}

		return view('Admin/home/index',compact('events','messages','order_graph','sales_graph','last_orders_product','limit','orders','agents','designers','vendors','field','sort','extra_admin'));
	}
	
	public function chat_message(Request $request){
		$messages = DB::table('messages')->select('messages.*',DB::raw("concat(user.fname, ' ', user.lname) as sender_name"))->leftJoin('users as user', 'messages.from_id', '=', 'user.id')->where('from_id',\Auth::user()->id)->orWhere('to_id',\Auth::user()->id)->limit(10)->oldest()->get();
		
		return view('Admin/home/chat',compact('messages'));
	}
	
    public function myaccount(Request $request){
		$user = User::findOrFail(\Auth::user()->id);
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);
			$validArr = [
                'fname' => 'required|string|max:255',
				'lname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|',
				'phone_number' => 'required|min:10|numeric'
            ];
			
			$validArr_password = [
                'OldPassword' => 'required',
				'NewPassword' => 'required|confirmed|min:6|max:50|different:OldPassword',
				'NewPassword_confirmation' => 'required'
            ];
			
			if(isset($data['email'])){
				$validation = Validator::make($data, $validArr);
				if ($validation->passes()) { 
					$user->fname = $data['fname'];
					$user->lname = $data['lname'];
					$user->email = $data['email'];
					$user->phone_number = $data['phone_number'];
					
					if($user->save()){
						\Session::flash('success', 'Profile changed successfull. ');
					}
					else{
						\Session::flash('error', 'Profile not changed successfull, Please try again. ');
					}
				}
				else{
					return redirect('/admin/profile')->withErrors($validation)->withInput();
				}
			}
			
			
		}
		$data=User::where('id', \Auth::user()->id)->get()->toArray();
		
		return view('Admin/home/myaccount',compact('data'));
    }
	
	public function changepassword(Request $request){
		$user = User::findOrFail(\Auth::user()->id);
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr_password = [
                'OldPassword' => 'required',
				'NewPassword' => 'required|confirmed|min:6|max:50|different:OldPassword',
				'NewPassword_confirmation' => 'required|min:6|max:50'
            ];
			
			if(isset($data['OldPassword'])){
				$validation = Validator::make($data, $validArr_password);
				if ($validation->passes()){
					if (Hash::check($data['OldPassword'], $user->password)) {
						
						$user->password = bcrypt($data['NewPassword']);
						
						if($user->save()){
							\Session::flash('success', 'Password changed successfull, Please login again. ');
						}
						else{
							\Session::flash('error', 'Password not changed, Please try again. ');
						}
					}
					else{
						\Session::flash('error', 'old password not matched ,please enter correct old password. ');
					}
				}
				else{
					return redirect('/admin/ChangePassword')->withErrors($validation)->withInput();
				}
			}
		}
		
		$data=User::where('id', \Auth::user()->id)->get()->toArray();
		return view('Admin/home/changepassword',compact('data'));
	}
	
	public function events(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
		
			parse_str($data['data'], $data);
			$event = Events::findOrFail($data['event_id']);
			$event->date = $data['date'];
			$event->title = $data['title'];
			$event->message = $data['message'];
			$event->save();
			$responce['status'] = 'success';		
		}
		return json_encode($responce);
	}
	
	public function agent_events(){
		if(\Auth::user()->role_id == 1){
			$db = Events::select('events.*',DB::raw("concat(user.fname, ' ', user.lname) as user_name"),DB::raw("concat(customer.fname, ' ', customer.lname) as customer_name"))->where('events.status',1);
			$db->leftJoin('users as user', 'events.user_id', '=', 'user.id');
			$db->leftJoin('users as customer', 'events.customer_id', '=', 'customer.id');
			$events = $db->get();
		}else{
			$db = Events::where('user_id',\Auth::user()->id)->select('events.*',DB::raw("concat(user.fname, ' ', user.lname) as user_name"),DB::raw("concat(customer.fname, ' ', customer.lname) as customer_name"))->where('events.status',1);
			$db->leftJoin('users as user', 'events.user_id', '=', 'user.id');
			$db->leftJoin('users as customer', 'events.customer_id', '=', 'customer.id');
			$events = $db->get();
		}
		
		return view('Admin/home/events',compact('events'));
	}
	
	public function messages(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			
			if($data['type'] == 'list'){
				$list = Messages::where('from_id',$data['agent_id'])->orWhere('to_id',$data['agent_id'])->with(['from_detail','to_detail'])->limit(10)->get();
				
				$responce['status'] = 'success';
				if(!empty($list->toArray())){
					foreach($list as $val){
						$class = 'pull-left';
						if($val->from_id == \Auth::user()->id){
							$class = 'pull-right right';
						}
						$responce['html'] .= '<div class="direct-chat-msg '.$class.'" style="width:40%;"><div class="direct-chat-info clearfix"><span class="direct-chat-name pull-left">'.$val->from_detail->fname.' '.$val->from_detail->lname.'</span><span class="direct-chat-timestamp pull-right">'.date('d F H:i A',strtotime($val->date)).'</span></div><div class="direct-chat-text">'.htmlentities($val->message).'</div></div><div class="clearfix"></div>';
					}
				}
			}
			if($data['type'] == 'add'){
				$message = new Messages();
				$to_id = 1 ;
				if(array_key_exists('to_id',$data)){
					$to_id = $data['to_id'];
				}
				$message->from_id = $data['from_id'];
				$message->to_id = $to_id;
				$message->date = date('Y-m-d H:i:s');
				$message->message = $data['message'];
				$message->save();
				
				$responce['status'] = 'success';
				$responce['html'] .= '<div class="direct-chat-msg pull-right right"><div class="direct-chat-info clearfix"><span class="direct-chat-name pull-left">'.\Auth::user()->fname.' '.\Auth::user()->lname.'</span><span class="direct-chat-timestamp pull-right">('.date('d F H:i A',strtotime($message->date)).')</span></div><div class="direct-chat-text">'.htmlentities($message->message).'</div></div>';
				
				$mail_detail = DB::table('users')->select('users.email', DB::raw("concat(users.fname, ' ', users.lname) as name"))->where('id',$to_id)->first();
				
				$email = $mail_detail->email;
				$name = $mail_detail->name;
								
				$params = array('slug'=>'new_chat_message',
								'to'=>$email,
								'params'=>array(
											'{{name}}'=>$name,
											'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
											));
				parent::sendMail($params);
			}
		}
		return json_encode($responce);
	}
}
