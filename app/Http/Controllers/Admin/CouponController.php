<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;
use App\Coupons;
use App\User;
use App\Products;


class CouponController extends Controller
{
    public function add(Request $request){
		$pageTitle = "Coupon Add";
		$users_list = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',3)->where('status', 1)->pluck('name','id')->all();
		
		$products_list = Products::select('id', 'name')->where('status', 1)->pluck('name','id')->all();
		
		if($request->isMethod('post')){
			$data = $request->all();
			if(!array_key_exists('single_time',$data)){
				$data['single_time'] = 0;
			}
			//pr($data);die;
			$validArr = [
                'title' => 'required',
                'code' => 'required',
                'type' => 'required',
                'expiry_date' => 'required',
                'users_type' => 'required',
            ];
			
			if($data['type'] == 1){
				$validArr['amount'] = 'required|numeric|min:1';
				$type_str = 'amount';
				$type_value = $data['amount'];
			}
			else if($data['type'] == 2){
				$validArr['percent'] = 'required|numeric|between:0,100';
				$type_str = 'percent';
				$type_value = $data['percent'];
			}
			else if($data['type'] == 3){
				$type_str = 'free_shipping';
				$type_value = 0;
			}
			
			if($data['users_type'] == 2){
				$validArr['users'] = 'required|array|min:1';
			}
			
			if($data['product_type'] == 2){
				$validArr['products'] = 'required|array|min:1';
			}
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
				$coupon = new Coupons();
				$coupon->title = $data['title'];
				$coupon->code = $data['code'];
				$coupon->type = $type_str;
				$coupon->type_value = $type_value;
				$coupon->min_cart = $data['min_cart'];
				$coupon->max_discount = $data['max_discount'];
				$coupon->expire_date = Carbon::createFromFormat('m-d-Y', $data['expiry_date'])->format('Y-m-d');
				$coupon->single_time = $data['single_time'];
				if($data['users_type'] == 2){
					$coupon->users = implode(',',$data['users']);
				}
				if($data['product_type'] == 2){
					$coupon->products = implode(',',$data['products']);
				}
				
				if($coupon->save()){
					\Session::flash('success', 'Coupon added complete.');
					return redirect('/admin/coupon/lists');
				}
				else{
					\Session::flash('error', 'Coupon not added.');
					return redirect('/admin/coupon/add');
				}
			}
			else{
				\Session::flash('error', 'Coupon not added.');
				return redirect('/admin/coupon/add')->withErrors($validation)->withInput();
        	}
		}
		//pr($products_list);die;
		return view('Admin/coupon/add',compact('pageTitle','users_list','products_list'));
	}
	
	public function lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = "Coupon List";
		$data = $request->all();
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('coupon');
		}
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		
		$db = Coupons::whereIn('status',[1,0]);
		
		if($request->isMethod('post')){
			if(isset($data['title']) and !empty($data['title'])){
				session(['coupon.title' => $data['title']]);
			}else{
				session()->forget('coupon.title');
			}
			
			if(isset($data['code']) and !empty($data['code'])){
				session(['coupon.code' => $data['code']]);
			}else{
				session()->forget('coupon.code');
			}
			
			if(isset($data['status']) and $data['status'] != ''){
				session(['coupon.status' => $data['status']]);
			}else{
				session()->forget('coupon.status');
			}
		}
		
		if (session()->has('coupon')) {
			if (session()->has('coupon.title')) {
				$title = session()->get('coupon.title');
				$db->where(function ($q) use($request,$title) {
					$q->orWhere('title','like','%'.$title.'%');
				});
			}
			if (session()->has('coupon.code')) {
				$code = session()->get('coupon.code');
				$db->where('code','like','%'.$code.'%');
			}
			if (session()->has('coupon.status')) {
				$status = session()->get('coupon.status');
				$db->where('status',$status);
			}
		}
		
		if($field != null){
			if($field == 'value'){
				$field = 'type_value';
			}
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		$coupons = $db->paginate($limit);
		return view('Admin/coupon/lists',compact('pageTitle','limit','coupons','data','field','sort'));
	}
	
	public function edit(Request $request,$id){
		$pageTitle = "Coupon Edit";
		$users_list = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',3)->where('status', 1)->pluck('name','id')->all();
		
		$products_list = Products::select('id', 'name')->where('status', 1)->pluck('name','id')->all();
		
		$coupon = Coupons::findOrFail($id);
		if($request->isMethod('post')){
			$data = $request->all();
			if(!array_key_exists('single_time',$data)){
				$data['single_time'] = 0;
			}
			//pr($data);
			$validArr = [
                'title' => 'required',
                'code' => 'required',
                'type' => 'required',
                'expiry_date' => 'required',
            ];
			
			if($data['type'] == 1){
				$validArr['amount'] = 'required|numeric|min:1';
				$type_str = 'amount';
				$type_value = $data['amount'];
			}
			else if($data['type'] == 2){
				$validArr['percent'] = 'required|numeric|between:0,100';
				$type_str = 'percent';
				$type_value = $data['percent'];
			}
			else if($data['type'] == 3){
				$type_str = 'free_shipping';
				$type_value = 0;
			}
			
			if($data['users_type'] == 2){
				$validArr['users'] = 'required|array|min:1';
			}
			
			if($data['product_type'] == 2){
				$validArr['products'] = 'required|array|min:1';
			}

			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$coupon->title = $data['title'];
				$coupon->code = $data['code'];
				$coupon->type = $type_str;
				$coupon->type_value = $type_value;
				$coupon->min_cart = $data['min_cart'];
				$coupon->max_discount = $data['max_discount'];
				$coupon->expire_date = Carbon::createFromFormat('Y-m-d', $data['expiry_date'])->format('Y-m-d');
				$coupon->single_time = $data['single_time'];
				if($data['users_type'] == 2){
					$coupon->users = implode(',',$data['users']);
				}else{
					$coupon->users = null;
				}
				
				if($data['product_type'] == 2){
					$coupon->products = implode(',',$data['products']);
				}else{
					$coupon->products = null;
				}
				
				if($coupon->save()){
					\Session::flash('success', 'Coupon Edit.');
					return redirect('/admin/coupon/lists');
				}
				else{
					\Session::flash('error', 'Coupon not edit.');
					return redirect('/admin/coupon/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Coupon not Edit.');
				return redirect('/admin/coupon/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/coupon/edit',compact('pageTitle','coupon','id','users_list','products_list'));
	}
	
	public function action($id,$status){
		if($status == 'delete'){
			$coupon=Coupons::where('id', $id)->delete();
			if($coupon){
				\Session::flash('success', 'Coupon deleted successfully.');
				return \Redirect::to('/admin/coupon/lists');
			}
			else{
				\Session::flash('error', 'Coupon not deleted.');
				return \Redirect::to('/admin/coupon/lists');
			}
		}else{
			$coupon=Coupons::where('id', $id)->update(['status'=>$status]);
			if($coupon){
				\Session::flash('success', 'Coupon status updated successfully.');
				return \Redirect::to('/admin/coupon/lists');
			}
			else{
				\Session::flash('error', 'Coupon status not updated.');
				return \Redirect::to('/admin/coupon/lists');
			}
		}
	}
	
	public function mail_send(Request $request){
		$res ['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$detail = Coupons::findOrFail($data['id']);
			if(empty($detail->users)){
				$users = User::where('role_id',3)->where('status', 1)->get();
			}else{
				$ids_array = explode(',',$detail->users);
				$users = User::where('role_id',3)->whereIn('id',$ids_array)->where('status', 1)->get();
			}
			foreach($users as $user){
				$params = array(
								'slug'=>'coupon_code_mail',
								'to'=>$user->email,
								'params'=>array(
											'{{name}}'=>$user->fname.' '.$user->lname,
											'{{coupon_code}}'=>$detail->code,
											'{{SITE_URL}}'=>config('constants.SITE_URL'),
											'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
											'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
											)
								);
				parent::sendMail($params);
			}
			Coupons::where('id',$detail->id)->update(['mail_status'=>1]);
			$res ['status'] = 'success';
		}
		return json_encode($res);
	}
}
