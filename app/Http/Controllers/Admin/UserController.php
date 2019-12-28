<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use DB;
use Excel;
use App\User;
use App\UserAddress;
use App\OrderAddress;
use App\Notes;
use App\Events;
use App\State;
use App\Helpers\UserImport;


class UserController extends Controller
{
    public function login(Request $request){
		
		if(isset(\Auth::user()->id)){
			return redirect('/admin/dashboard');
		}
		
		if($request->isMethod('post')) { 
			$data =$request->all();  
			//pr($data);die;
			$credentials = [
					'email' => $data['Username'],
					'password' => $data['Password']
			];
			
			if(Auth::attempt($credentials)){
				if(Auth::user()->status == 0){
					Auth::logout();
					\Session::flash('error', 'You dont have access.');
					return \Redirect::to('/admin/login');
				}
				 \Session::flash('success', 'you have successfully login.');
				 $action = '/admin/dashboard';
				 if(Auth::user()->role_id == 2){
					//$action = '/admin/order/lists';
				 }
				 
				 if(array_key_exists('remember_me',$data)){
					return \Redirect::to($action)->withCookie(cookie('admin_remember', serialize($data), 86400 * 30));
				 }else{
					 $cookie = Cookie::forget('admin_remember');
					 return \Redirect::to($action)->withCookie($cookie);
				 }
			}else{
				\Session::flash('error', 'Email and password incorrect.');
				return \Redirect::to('/admin/login');
			}
		}else{
			/* $user = User::where('id',1)->first();
			Auth::login($user);
			pr(Auth::user());
			die; */
		}
		return view('Admin/users/login',['remember'=>unserialize(Cookie::get('admin_remember'))]);
    }
	
	public function add(Request $request){
		$pageTitle = "Customer Add";
		$states = State::where('status',1)->pluck('stateName','stateCode')->all();
		if($request->isMethod('post')){
			$data = $request->all();
			if(!array_key_exists('pay_by_invoice',$data)){
				$data['pay_by_invoice'] = 0;
			}
			if(!array_key_exists('tax_exempt',$data)){
				$data['tax_exempt'] = 0;
			}
			if(!array_key_exists('same_as_billing',$data)){
				$data['same_as_billing'] = 0;
			}
			//pr($data);die;
			$validArr = [
                'fname' => 'required|string|max:255',
				'lname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:users',
				'password' => 'required|string|min:6|confirmed',
				//'phone_number' => 'required|min:10|numeric',
				//'phone_number' => 'required|string',
				//'company_name' => 'required|string|max:255',
				//'billing_fname' => 'required|string|max:255',
				//'billing_lname' => 'required|string|max:255',
				//'billing_address1' => 'required',
				//'billing_address2' => 'required',
				//'billing_zipcode' => 'required',
				//'billing_city' => 'required',
				//'billing_state' => 'required',
				//'billing_country' => 'required',				
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$user = new User();
				$user->fname = $data['fname'];
				$user->lname = $data['lname'];
				$user->email = $data['email'];
				$user->phone_number = $data['phone_number'];
				$user->company_name = $data['company_name'];
				if($data['company_name']=="")
				{
					$user->company_name = $user->fname.' '.$user->lname; 
				}
				$user->pay_by_invoice = $data['pay_by_invoice'];
				$user->tax_exempt = $data['tax_exempt'];
				$user->status = '1';
				$user->password = bcrypt($data['password']);
				
				if($user->save()){
					if($data['billing_city'] !="" )
					{
						$user_address = new UserAddress();
						$user_address->user_id = $user->id;
						$user_address->type = 1;
						$user_address->company_name = $data['billing_company_name'];
						$user_address->phone_number = $data['billing_phone_number'];
						$user_address->fname = $data['billing_fname'];
						$user_address->lname = $data['billing_lname'];
						$user_address->add1 = $data['billing_address1'];
						$user_address->add2 = $data['billing_address2'];
						$user_address->zipcode = $data['billing_zipcode'];
						$user_address->city = $data['billing_city'];
						$user_address->state = $data['billing_state'];
						$user_address->country = $data['billing_country'];
						$user_address->save();
					}
							
					if($data['same_as_billing'] == 0){
						foreach($data['shipping_address'] as $val){
							if($val['city'] != "")
							{
								$user_address = new UserAddress();
								$user_address->user_id = $user->id;
								$user_address->type = 2;
								$user_address->address_name = $val['address_name'];
								$user_address->company_name = $val['company_name'];
								$user_address->phone_number = $val['phone_number'];
								$user_address->fname = $val['fname'];
								$user_address->lname = $val['lname'];
								$user_address->add1 = $val['add1'];
								$user_address->add2 = $val['add2'];
								$user_address->ship_in_care = $val['ship_in_care'];
								$user_address->zipcode = $val['zipcode'];
								$user_address->city = $val['city'];
								$user_address->state = $val['state'];
								$user_address->country = $val['country'];
								$user_address->save();
							}	
						}
					}else{

						if($data['billing_city'] !="" )
						{
							$user_address = new UserAddress();
							$user_address->user_id = $user->id;
							$user_address->type = 2;
							$user_address->company_name = $val['billing_company_name'];
							$user_address->phone_number = $val['billing_phone_number'];
							$user_address->fname = $data['billing_fname'];
							$user_address->lname = $data['billing_lname'];
							$user_address->add1 = $data['billing_address1'];
							$user_address->add2 = $data['billing_address2'];
							$user_address->zipcode = $data['billing_zipcode'];
							$user_address->city = $data['billing_city'];
							$user_address->state = $data['billing_state'];
							$user_address->country = $data['billing_country'];
							$user_address->save();
						}	
					}
					$params = array(
									'slug'=>'admin_user_register',
									'to'=>$data['email'],
									'params'=>array(
												'{{name}}'=>$data['fname'].' '.$data['lname'],
												'{{EMAIL}}'=>$data['email'],
												'{{PASSWORD}}'=>$data['password'],
												'{{SITE_URL}}'=>config('constants.SITE_URL'),
												'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
												'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
												'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
												)
									);
					parent::sendMail($params);
					\Session::flash('success', 'Customer added complete.');
					return redirect('/admin/users/lists');
				}
				else{
					\Session::flash('error', 'Customer not added.');
					return redirect('/admin/users/add');
				}
			}
			else{
				\Session::flash('error', 'Customer not added.');
				return redirect('/admin/users/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/users/add',compact('pageTitle','states'));
	}
	
	public function lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = "Customer List";
		$data = $request->all();
		/*if(!$request->isMethod('post') and !isset($data['page'])){
			session()->forget('users');
		}*/

		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('users');
		}
		
		$condition['role_id'] = 3; 
		$orcondition = array();
		
		//$limit = config('constants.ADMIN_PAGE_LIMIT');
		$limit = 50;
				
		$db=User::where($condition);
		
		if($request->isMethod('post')){
			if(isset($data['company_name']) and !empty($data['company_name'])){
				session(['users.company_name' => $data['company_name']]);
			}else{
				session()->forget('users.company_name');
			}
			if(isset($data['fname']) and !empty($data['fname'])){
				session(['users.fname' => $data['fname']]);
			}else{
				session()->forget('users.fname');
			}
			if(isset($data['lname']) and !empty($data['lname'])){
				session(['users.lname' => $data['lname']]);
			}else{
				session()->forget('users.lname');
			}
			
			if(isset($data['email']) and !empty($data['email'])){
				session(['users.email' => $data['email']]);
			}else{
				session()->forget('users.email');
			}
			
			if(isset($data['status']) and $data['status'] != ''){
				session(['users.status' => $data['status']]);
			}else{
				session()->forget('users.status');
			}
		}
		
		if (session()->has('users')) {
			if (session()->has('users.company_name')) {
				$company_name = session()->get('users.company_name');
				$db->where('company_name','like','%'.$company_name.'%');
				/* $db->where(function ($q) use($orcondition,$request,$name) {

					$q->orWhere('fname','like','%'.$name.'%');
					$q->orWhere('lname','like','%'.$name.'%');
					$q->orWhere('company_name','like','%'.$name.'%');
					$searchArr = explode(" ", $name);					
					foreach ($searchArr as $key => $value) {
						$q->orWhere('fname','like','%'.$value.'%');
						$q->orWhere('lname','like','%'.$value.'%');
						$q->orWhere('company_name','like','%'.$value.'%');
					}
				}); */
			}
			if (session()->has('users.fname')) {
				$fname = session()->get('users.fname');
				$db->where('fname','like','%'.$fname.'%');
			}
			if (session()->has('users.lname')) {
				$lname = session()->get('users.lname');
				$db->where('lname','like','%'.$lname.'%');
			}
			if (session()->has('users.email')) {
				$email = session()->get('users.email');
				$db->where('email','like','%'.$email.'%');
			}
			if (session()->has('users.status')) {
				$status = session()->get('users.status');
				$db->where('status',$status);
			}
		}
		if($field != null){
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		
		$users = $db->paginate($limit);
				
		return view('Admin/users/lists',compact('pageTitle','limit','users','data','field','sort'));
	}
	
	public function edit(Request $request,$id){
		$pageTitle = "Customer Edit";
		$states = State::where('status',1)->pluck('stateName','stateCode')->all();
		$user = User::with('user_add')->findOrFail($id);
		//pr($user->user_add);die;
		$address = array();
		$i= 1;
		$address['billing']['id'] = '';
		$address['billing']['company_name'] = '';
		$address['billing']['phone_number'] = '';
		$address['billing']['fname'] = '';
		$address['billing']['lname'] = '';
		$address['billing']['add1'] = '';
		$address['billing']['add2'] = '';
		$address['billing']['zipcode'] = '';
		$address['billing']['city'] = '';
		$address['billing']['state'] = '';
		$address['billing']['country'] = '';
		foreach($user->user_add as $val){
			if($val->type == 1){
				$address['billing']['id'] = $val['id'];
				$address['billing']['company_name'] = $val['company_name'];
				$address['billing']['phone_number'] = $val['phone_number'];
				$address['billing']['fname'] = $val['fname'];
				$address['billing']['lname'] = $val['lname'];
				$address['billing']['add1'] = $val['add1'];
				$address['billing']['add2'] = $val['add2'];
				$address['billing']['zipcode'] = $val['zipcode'];
				$address['billing']['city'] = $val['city'];
				$address['billing']['state'] = $val['state'];
				$address['billing']['country'] = $val['country'];
			}else{
				$address['shipping'][$i]['id'] = $val['id'];
				$address['shipping'][$i]['address_name'] = $val['address_name'];
				$address['shipping'][$i]['company_name'] = $val['company_name'];
				$address['shipping'][$i]['phone_number'] = $val['phone_number'];
				$address['shipping'][$i]['fname'] = $val['fname'];
				$address['shipping'][$i]['lname'] = $val['lname'];
				$address['shipping'][$i]['add1'] = $val['add1'];
				$address['shipping'][$i]['add2'] = $val['add2'];
				$address['shipping'][$i]['ship_in_care'] = $val['ship_in_care'];
				$address['shipping'][$i]['zipcode'] = $val['zipcode'];
				$address['shipping'][$i]['city'] = $val['city'];
				$address['shipping'][$i]['state'] = $val['state'];
				$address['shipping'][$i]['country'] = $val['country'];
			}
			$i++;
		}
		
		if($request->isMethod('post')){
			$data = $request->all();
			if(!array_key_exists('pay_by_invoice',$data)){
				$data['pay_by_invoice'] = 0;
			}
			if(!array_key_exists('tax_exempt',$data)){
				$data['tax_exempt'] = 0;
			}
			//pr($data);die;
			$validArr = [
                'fname' => 'required|string|max:255',
				'lname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:users,email,'.$id,
				//'phone_number' => 'required|min:10|numeric',
				//'phone_number' => 'required|string',
				//'company_name' => 'required|string|max:255',
				//'billing_fname' => 'required|string|max:255',
				//'billing_lname' => 'required|string|max:255',
				//'billing_address1' => 'required',
				//'billing_address2' => 'required',
				//'billing_zipcode' => 'required',
				//'billing_city' => 'required',
				//'billing_state' => 'required',
				//'billing_country' => 'required',				
            ];
			if(!empty($data['password'])){
				$validArr = ['password' => 'string|min:6|confirmed'];
			}
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$user->fname = $data['fname'];
				$user->lname = $data['lname'];
				$user->email = $data['email'];
				$user->phone_number = $data['phone_number'];
				$user->company_name = $data['company_name'];
				if($data['company_name']=="")
				{
					$user->company_name = $user->fname.' '.$user->lname; 
				}
				$user->pay_by_invoice = $data['pay_by_invoice'];
				$user->tax_exempt = $data['tax_exempt'];
				
				if(!empty($data['password'])){
					$user->password = bcrypt($data['password']);
				}
				
				if($user->save()){

					if(!empty($data['password'])){
						$params = array(
									'slug'=>'reset_password',
									'to'=>$data['email'],
									'params'=>array(
												'{{name}}'=>$data['fname'].' '.$data['lname'],
												'{{EMAIL}}'=>$data['email'],
												'{{NEW_PASSWORD}}'=>$data['password'],												
												)
									);
						
						//parent::sendMail($params);
					}

					$ids = array();
					if($data['billing_address_id'] != ''){
						$user_address = UserAddress::findOrFail($data['billing_address_id']);
					}
					else{
						$user_address = new UserAddress();
					}

					if($data['billing_city'] !="")
					{
						$user_address->user_id = $user->id;
						$user_address->type = 1;
						$user_address->company_name = $data['billing_company_name'];
						$user_address->phone_number = $data['billing_phone_number'];
						$user_address->fname = $data['billing_fname'];
						$user_address->lname = $data['billing_lname'];
						$user_address->add1 = $data['billing_address1'];
						$user_address->add2 = $data['billing_address2'];
						$user_address->zipcode = $data['billing_zipcode'];
						$user_address->city = $data['billing_city'];
						$user_address->state = $data['billing_state'];
						$user_address->country = $data['billing_country'];
						$user_address->save();
						$ids[] = $user_address->id;
						
						OrderAddress::where('billing_add_id',$user_address->id)->update([
							'billing_company_name' => $data['billing_company_name'],
							'billing_phone_number' => $data['billing_phone_number'],
							'billing_fname' => $data['billing_fname'],
							'billing_lname' => $data['billing_lname'],
							'billing_add1' => $data['billing_address1'],
							'billing_add2' => $data['billing_address2'],
							'billing_zipcode' => $data['billing_zipcode'],
							'billing_city' => $data['billing_city'],
							'billing_state' => $data['billing_state'],
							'billing_country' => $data['billing_country']
						]);
					}	
					
					foreach($data['shipping_address'] as $val){
						if(array_key_exists('id',$val)){
							$ids[] = $val['id'];
							$user_address = UserAddress::findOrFail($val['id']);
						}
						else{
							$user_address = new UserAddress();
						}
						if($val['city'] !="")
						{
							$user_address->user_id = $user->id;
							$user_address->type = 2;
							$user_address->address_name = $val['address_name'];
							$user_address->company_name = $val['company_name'];
							$user_address->phone_number = $val['phone_number'];
							$user_address->fname = $val['fname'];
							$user_address->lname = $val['lname'];
							$user_address->add1 = $val['add1'];
							$user_address->add2 = $val['add2'];
							$user_address->ship_in_care = $val['ship_in_care'];
							$user_address->zipcode = $val['zipcode'];
							$user_address->city = $val['city'];
							$user_address->state = $val['state'];
							$user_address->country = $val['country'];
							$user_address->save();
							$ids[] = $user_address->id;
						}	
						
					}
					
					if(!empty($ids)){
						UserAddress::where('user_id',$user->id)->whereNotIn('id',$ids)->delete();
					}
					
					\Session::flash('success', 'Customer changes saved.');
					return redirect('/admin/users/lists');
				}
				else{
					\Session::flash('error', 'Customer changes were not saved.');
					return redirect('/admin/users/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Customer changes were not saved.');
				return redirect('/admin/users/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/users/edit',compact('pageTitle','user','id','address','states'));
	}
	
	public function view($id){
		$pageTitle = "Customer View";
		
		$user=User::where('id', $id)->with('user_add')->first();
		
		return view('Admin/users/view',compact('pageTitle','user'));
	}
	
	public function action($id,$status){
		$users=User::where('id', $id)->update(['status'=>$status]);
		if($users){
			\Session::flash('success', 'User status updated successfully.');
			return \Redirect::to('/admin/users/lists');
		}
		else{
			\Session::flash('error', 'User status not updated successfully.');
			return \Redirect::to('/admin/users/lists');
		}
	}
	
	public function notes(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			
			if($data['type'] == 'list'){
				$list = Notes::where('user_id',$data['user_id'])->where('customer_id',$data['customer_id'])->get();
				$responce['status'] = 'success';
				if(!empty($list->toArray())){
					$responce['html'] = '<div class="box box-solid"><div class="box-body">';
					foreach($list as $val){
						$responce['html'] .= '<li class="note_li_'.$val->id.'">'.$val->note.'<span class="btn btn-xs btn-danger pull-right delete_note" data-id="'.$val->id.'"><i class="fa fa-trash"></i></span></li>';
					}
					$responce['html'] .= '</div></div>';
				}
			}
			if($data['type'] == 'add'){
				parse_str($data['data'], $data);
				$note = new Notes();
				$note->user_id = $data['note_user_id'];
				$note->customer_id = $data['note_customer_id'];
				$note->note = $data['note'];
				$note->save();
				
				$responce['status'] = 'success';
				$responce['html'] = '<li class="note_li_'.$note->id.'">'.htmlentities($data['note']).'<span class="btn btn-xs btn-danger pull-right delete_note" data-id="'.$note->id.'"><i class="fa fa-trash"></i></span></li>';
			}
		}
		return json_encode($responce);
	}
	
	public function events(Request $request){
		$responce['status'] = false;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			if($data['type'] == 'list'){
				$list = Events::where('user_id',$data['user_id'])->where('customer_id',$data['customer_id'])->get();
				$responce['status'] = 'success';
				if(!empty($list->toArray())){
					$responce['html'] = '<div class="box box-solid"><div class="box-body">';
					foreach($list as $val){
						$user_name = User::select('email', DB::raw("concat(fname, ' ', lname) as name"))->where('id',$val->user_id)->first();
						$customer_name = User::select('email', DB::raw("concat(fname, ' ', lname) as name"))->where('id',$val->customer_id)->first();
						
						$responce['html'] .= '<li class="event_li_'.$val->id.'"><i class="fa fa-calendar-plus-o"></i> '.date('d F Y',strtotime($val->date)).'<button type="button" class="btn btn-xs btn-danger pull-right event_delete" data-id="'.$val->id.'"><i class="fa fa-trash"></i></button><br/><b>Created By : </b>'.$user_name->name.'<br/><b>Customer Name : </b>'.$customer_name->name.'<br/><b>'.$val->title.'</b><br/>'.$val->message.'</li>';
					}
					$responce['html'] .= '</div></div>';
				}
			}
			if($data['type'] == 'add'){
				parse_str($data['data'], $data);
				
				$event = new Events();
				$event->user_id = $data['event_user_id'];
				$event->customer_id = $data['event_customer_id'];
				$event->date = $data['date'];
				$event->title = $data['title'];
				$event->message = $data['message'];
				$event->save();
				
				$responce['status'] = 'success';
				
				$user_detail = User::select('email', DB::raw("concat(fname, ' ', lname) as name"))->where('id',$data['event_user_id'])->first();
				
				$customer = User::select(DB::raw("concat(fname, ' ', lname) as name"))->where('id',$data['event_customer_id'])->first();
				
				$responce['html'] = '<li class="event_li_'.$event->id.'"><i class="fa fa-calendar-plus-o"></i> '.date('d F Y',strtotime($data['date'])).'<button type="button" class="btn btn-xs btn-danger pull-right event_delete" data-id="'.$event->id.'"><i class="fa fa-trash"></i></button><br/><b>Created By : </b>'.$user_detail->name.'<br/><b>Customer Name : </b>'.$customer->name.'<br/><b>'.htmlentities($data['title']).'</b><br/>'.$data['message'].'</li>';
				
				$params = array('slug'=>'new_customer_event_mail',
								'to'=>$user_detail->email,
								'params'=>array(
											'{{name}}'=>$user_detail->name,
											'{{customer_name}}'=>$customer->name,
											'{{event_date}}'=>date('d F Y',strtotime($data['date'])),
											'{{event_name}}'=>$data['title'],
											'{{event_message}}'=>$data['message'],
											'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
											));
				parent::sendMail($params);
			}
		}
		return json_encode($responce);
	}

	public function excel(Request $request) {

		if ($request->isMethod('post')) {
    		ini_set('max_execution_time', 0);
    		$product_codes = [];
			$insert_data = [];
			$final_data = [];
			$this->validate($request, [
	            'upload_excel' => 'mimes:xlsx,xls|required|max:10000' // max 10000kb
	        ]);

			$path = $request->file('upload_excel')->getRealPath();			
			Excel::import(new UserImport,$path);		
			\Session::flash('success', 'User Uploaded Successfully');
			return \Redirect::to('/admin/users/excel');
		}
		return view('/Admin/users/excel');
	}
	
}
