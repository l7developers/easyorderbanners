<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Designers;


class DesignerController extends Controller
{
    public function add(Request $request){
		$pageTitle = "Designer Add";
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);
			$validArr = [
                'fname' => 'required|string|max:255',
				'lname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:designers',
				//'phone_number' => 'required|min:10|numeric',
				'tFlow' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$designer = new Designers();
				$designer->fname = $data['fname'];
				$designer->lname = $data['lname'];
				$designer->email = $data['email'];
				//$designer->phone_number = $data['phone_number'];
				$designer->tFlow = $data['tFlow'];
				$designer->extension = $data['extension'];
				$designer->direct = $data['direct'];
				$designer->mobile = $data['mobile'];
				$designer->status = '1';
				
				if($designer->save()){
					\Session::flash('success', 'Designer added complete.');
					return redirect('/admin/designers/lists');
				}
				else{
					\Session::flash('error', 'Designer not added.');
					return redirect('/admin/designers/add');
				}
			}
			else{
				\Session::flash('error', 'Designer not added.');
				return redirect('/admin/designers/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/designers/add',compact('pageTitle'));
	}
	
	public function lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = "Designers List";
		$data = $request->all();
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('designers');
		}
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		\DB::enableQueryLog();
		
		$db = Designers::whereIn('status',[1,0]);
		
		if($request->isMethod('post')){
			if(isset($data['fname']) and !empty($data['fname'])){
				session(['designers.fname' => $data['fname']]);
			}else{
				session()->forget('designers.fname');
			}
			if(isset($data['lname']) and !empty($data['lname'])){
				session(['designers.lname' => $data['lname']]);
			}else{
				session()->forget('designers.lname');
			}
			
			if(isset($data['email']) and !empty($data['email'])){
				session(['designers.email' => $data['email']]);
			}else{
				session()->forget('designers.email');
			}
			
			if(isset($data['status']) and $data['status'] != ''){
				session(['designers.status' => $data['status']]);
			}else{
				session()->forget('designers.status');
			}
		}
		
		if (session()->has('designers')) {
			/* if (session()->has('designers.name')) {
				$name = session()->get('designers.name');
				$db->where(function ($q) use($request,$name) {
					$q->orWhere('fname','like','%'.$name.'%');
					$q->orWhere('lname','like','%'.$name.'%');
				});
			}*/
			if (session()->has('designers.fname')) {
				$fname = session()->get('designers.fname');
				$db->where('fname','like','%'.$fname.'%');
			}
			if (session()->has('designers.lname')) {
				$lname = session()->get('designers.lname');
				$db->where('lname','like','%'.$lname.'%');
			}
			if (session()->has('designers.email')) {
				$email = session()->get('designers.email');
				$db->where('email','like','%'.$email.'%');
			}
			if (session()->has('designers.status')) {
				$status = session()->get('designers.status');
				$db->where('status',$status);
			}
		}
		if($field != null){
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		$designers = $db->paginate($limit);
		
		return view('Admin/designers/lists',compact('pageTitle','limit','designers','data','field','sort'));
	}
	
	public function edit(Request $request,$id){
		$pageTitle = "Designer Edit";
		
		$designer = Designers::findOrFail($id);
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);
			$validArr = [
                'fname' => 'required|string|max:255',
				'lname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:designers,email,'.$id,
				//'phone_number' => 'required|min:10|numeric',
				'tFlow' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$designer->fname = $data['fname'];
				$designer->lname = $data['lname'];
				$designer->email = $data['email'];
				//$designer->phone_number = $data['phone_number'];
				$designer->tFlow = $data['tFlow'];
				$designer->extension = $data['extension'];
				$designer->direct = $data['direct'];
				$designer->mobile = $data['mobile'];
				
				if($designer->save()){
					\Session::flash('success', 'Designer Edit.');
					return redirect('/admin/designers/lists');
				}
				else{
					\Session::flash('error', 'Designer not edit.');
					return redirect('/admin/designers/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Designer not Edit.');
				return redirect('/admin/designers/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/designers/edit',compact('pageTitle','designer','id'));
	}
	
	public function view($id){
		$pageTitle = "Designer View";
		$designer=Designers::where('id', $id)->get()->toArray();
		
		return view('Admin/designers/view',compact('pageTitle','designer'));
	}
	
	public function delete_designer($id){
		$Designers=Designers::where('id', $id)->delete();
		if($Designers){
			\Session::flash('success', 'Designer deleted successfully.');
			return \Redirect::to('/admin/designers/lists');
		}
		else{
			\Session::flash('error', 'Designer not deleted.');
			return \Redirect::to('/admin/designers/lists');
		}
	}
	
	public function action($id,$status){
		$Designers=Designers::where('id', $id)->update(['status'=>$status]);
		if($Designers){
			\Session::flash('success', 'Designer status updated successfully.');
			return \Redirect::to('/admin/designers/lists');
		}
		else{
			\Session::flash('error', 'Designer status not updated.');
			return \Redirect::to('/admin/designers/lists');
		}
	}
}
