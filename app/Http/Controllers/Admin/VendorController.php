<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Vendors;


class VendorController extends Controller
{
    public function add(Request $request){
		$pageTitle = "Vendor Add";
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);
			
			$messages  = [
				'company_name.required' => 'Please enter any one from company name or first name.',
				'fname.required' => 'Please enter any one from company name or first name.'
			];
			
			$validation = Validator::make($data,[],$messages);
			
			$validation->sometimes(['company_name'], 'required', function($input){
				return $input->fname == '';
			});
			
			$validation->sometimes(['fname'], 'required', function($input){
				return $input->company_name == '';
			});
			
			if ($validation->passes()) { 
				$vendor = new Vendors();
				$vendor->company_name = $data['company_name'];
				$vendor->company_address = $data['company_address'];
				$vendor->fname = $data['fname'];
				$vendor->lname = $data['lname'];
				$vendor->email = $data['email'];
				$vendor->phone_number = $data['phone_number'];
				$vendor->phone_extension = $data['phone_extension'];
				$vendor->terms = $data['terms'];
				if($data['terms'] == 3){
					$vendor->new_terms = $data['new_terms'];
				}
				$vendor->status = '1';
				
				if($vendor->save()){
					\Session::flash('success', 'Vendor added complete.');
					return redirect('/admin/vendors/lists');
				}
				else{
					\Session::flash('error', 'Vendor not added.');
					return redirect('/admin/vendors/add');
				}
			}
			else{
				\Session::flash('error', 'Vendor not added.');
				return redirect('/admin/vendors/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/vendors/add',compact('pageTitle'));
	}
	
	public function lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = "Vendors List";
		$data = $request->all();
		
		/* if(!$request->isMethod('post') and !isset($data['page'])){
			session()->forget('vendors');
		} */
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('vendors');
		}
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		\DB::enableQueryLog();
		
		$db = Vendors::whereIn('status',[1,0]);
		
		if($request->isMethod('post')){
			if(isset($data['company_name']) and !empty($data['company_name'])){
				session(['vendors.company_name' => $data['company_name']]);
			}else{
				session()->forget('vendors.company_name');
			}
			if(isset($data['fname']) and !empty($data['fname'])){
				session(['vendors.fname' => $data['fname']]);
			}else{
				session()->forget('vendors.fname');
			}
			if(isset($data['lname']) and !empty($data['lname'])){
				session(['vendors.lname' => $data['lname']]);
			}else{
				session()->forget('vendors.lname');
			}
			if(isset($data['email']) and !empty($data['email'])){
				session(['vendors.email' => $data['email']]);
			}
			if(isset($data['status']) and $data['status'] != ''){
				session(['vendors.status' => $data['status']]);
			}
		}
		
		if (session()->has('vendors')) {
			/*if (session()->has('vendors.name')) {
				$name = session()->get('vendors.name');
				$db->where(function ($q) use($request,$name) {
					$q->orWhere('fname','like','%'.$name.'%');
					$q->orWhere('lname','like','%'.$name.'%');
				});
			}*/
			if (session()->has('vendors.company_name')) {
				$company_name = session()->get('vendors.company_name');
				$db->where('company_name','like','%'.$company_name.'%');
			}
			if (session()->has('vendors.fname')) {
				$fname = session()->get('vendors.fname');
				$db->where('fname','like','%'.$fname.'%');
			}
			if (session()->has('vendors.lname')) {
				$lname = session()->get('vendors.lname');
				$db->where('lname','like','%'.$lname.'%');
			}
			if (session()->has('vendors.email')) {
				$email = session()->get('vendors.email');
				$db->where('email','like','%'.$email.'%');
			}
			if (session()->has('vendors.status')) {
				$status = session()->get('vendors.status');
				$db->where('status',$status);
			}
		}		
		if($field != null){
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		
		$vendors = $db->paginate($limit);
		
		return view('Admin/vendors/lists',compact('pageTitle','limit','vendors','data','field','sort'));
	}
	
	public function edit(Request $request,$id){
		$pageTitle = "Vendor Edit";
		
		$vendor = Vendors::findOrFail($id);
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);
			
			$messages  = [
				'company_name.required' => 'Please enter any one from company name or first name.',
				'fname.required' => 'Please enter any one from company name or first name.'
			];
			
			$validation = Validator::make($data,[],$messages);
			
			$validation->sometimes(['company_name'], 'required', function($input){
				return $input->fname == '';
			});
			
			$validation->sometimes(['fname'], 'required', function($input){
				return $input->company_name == '';
			});
			
			if ($validation->passes()){
				$vendor->company_name = $data['company_name'];
				$vendor->company_address = $data['company_address'];
				$vendor->fname = $data['fname'];
				$vendor->lname = $data['lname'];
				$vendor->email = $data['email'];
				$vendor->phone_number = $data['phone_number'];
				$vendor->phone_extension = $data['phone_extension'];
				$vendor->terms = $data['terms'];
				if($data['terms'] == 3){
					$vendor->new_terms = $data['new_terms'];
				}else{
					$vendor->new_terms = '';
				}
				
				if($vendor->save()){
					\Session::flash('success', 'Vendor Edit.');
					return redirect('/admin/vendors/lists');
				}
				else{
					\Session::flash('error', 'Vendor not edit.');
					return redirect('/admin/vendors/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Vendor not Edit.');
				return redirect('/admin/vendors/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/vendors/edit',compact('pageTitle','vendor','id'));
	}
	
	public function view($id){
		$pageTitle = "Vendor View";
		$vendor=Vendors::where('id', $id)->get()->toArray();
		
		return view('Admin/vendors/view',compact('pageTitle','vendor'));
	}
	
	public function delete_vendor($id){
		$Vendors=Vendors::where('id', $id)->delete();
		if($Vendors){
			\Session::flash('success', 'Vendor deleted successfully.');
			return \Redirect::to('/admin/vendors/lists');
		}
		else{
			\Session::flash('error', 'Vendor not deleted.');
			return \Redirect::to('/admin/vendors/lists');
		}
	}
	
	public function action($id,$status){
		$Vendors=Vendors::where('id', $id)->update(['status'=>$status]);
		if($Vendors){
			\Session::flash('success', 'Vendors status updated successfully.');
			return \Redirect::to('/admin/vendors/lists');
		}
		else{
			\Session::flash('error', 'Vendors status not updated.');
			return \Redirect::to('/admin/vendors/lists');
		}
	}
}
