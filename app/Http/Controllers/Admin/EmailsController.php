<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Email;


class EmailsController extends Controller
{
	//------------ Add Email function ------------//
	
	public function add(Request $request){
		$pageTitle = 'Email Add';
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'slug_name' => 'required',
				'subject' => 'required',
				'message' => 'required'
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$email = new Email();
				$email->slug = $data['slug_name'];
				$email->subject = $data['subject'];
				$email->message = $data['message'];
				$email->status = '1';
				if($email->save()){
					\Session::flash('success', 'Successfully Added the email');
					return \Redirect::to('/admin/emails/lists');
				}
				else{
					\Session::flash('error', 'Something went wrong, the email not added ,please try again.');
					return \Redirect::to('/admin/emails/lists');
				}
			}
			else{
				return redirect('/admin/emails/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/emails/add',compact('pageTitle'));
	}
	
	//-------------- List of all Emails ----------//
	public function lists(Request $request){
		$pageTitle = 'Emails List';
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		$emails=Email::orderBy('created_at','desc')->paginate($limit);
		return view('Admin/emails/lists',compact('pageTitle','limit','emails'));
	}
	
	//-------------- View A Email ----------//
	public function view(Request $request,$id){
		$pageTitle = 'Email View';
		
		$email=Email::findOrFail($id);
		
		return view('Admin/emails/view',compact('pageTitle','email'));
	}
	
	// ----------- Edit Email -----------//
	public function edit(Request $request,$id){
		$pageTitle = 'Content Edit';
		
		$email = Email::findOrFail($id);
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);
			$validArr = [
                'slug_name' => 'required',
				'subject' => 'required',
				'message' => 'required'
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()){
				$email->slug = $data['slug_name'];
				$email->subject = $data['subject'];
				$email->message = $data['message'];
				
				if($email->save()){
					\Session::flash('success', 'Email content changed successfull. ');
					return redirect('/admin/emails/lists/');
				}
				else{
					\Session::flash('error', 'Email content not changed successfull, Please try again. ');
				}
			}
			else{
				return redirect('/admin/emails/edit/'.$id)->withErrors($validation)->withInput();
			}			
		}
		
		return view('Admin/emails/edit',compact('pageTitle','email'));
	}
	
	//--------- Action perform on Email ////
	public function action($id,$status){
		$email=Email::where('id', $id)->update(['status'=>$status]);
		if($email){
			\Session::flash('success', 'Email status updated successfully.');
			return \Redirect::to('/admin/emails/lists');
		}
		else{
			\Session::flash('error', 'Email Action not perform properly,Please try again.');
			return \Redirect::to('/admin/emails/lists');
		}
	}
	
	public function test(Request $request){
		$pageTitle = 'Testing on Form';
		$email = new Email();
		if($request->isMethod('post')){
			$data = $request->all();
			pr($data);die;
		}
		return view('Admin/emails/test',compact('pageTitle','email'));
	}
}
