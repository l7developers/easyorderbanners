<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Testimonials;


class TestimonialsController extends Controller
{
    public function add(Request $request){
		$pageTitle = "Testimonial Add";
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'name' => 'required',
                'content' => 'required',
                //'designation_company' => 'required'
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$testimonial = new Testimonials();
				$testimonial->name = $data['name'];
				$testimonial->content = $data['content'];
				$testimonial->designation_company = $data['designation_company'];
				
				if($testimonial->save()){
					\Session::flash('success', 'Testimonial added successfully.');
					return redirect('/admin/testimonial/lists');
				}
				else{
					\Session::flash('error', 'Testimonial not added successfully.');
					return redirect('/admin/testimonial/add');
				}
			}
			else{
				\Session::flash('error', 'Testimonial not added successfully.');
				return redirect('/admin/testimonial/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/testimonial/add',compact('pageTitle'));
	}
	
	public function lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = "Testimonials List";
		$data = $request->all();
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('testimonial');
		}
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		$db = Testimonials::whereIn('status',[1,0]);
		
		if($request->isMethod('post')){
			if(isset($data['name']) and !empty($data['name'])){
				session(['testimonial.name' => $data['name']]);
			}else{
				session()->forget('testimonial.name');
			}
			
			if(isset($data['designation_company']) and !empty($data['designation_company'])){
				session(['testimonial.designation_company' => $data['designation_company']]);
			}else{
				session()->forget('testimonial.designation_company');
			}
			
			if(isset($data['status']) and $data['status'] != ''){
				session(['testimonial.status' => $data['status']]);
			}else{
				session()->forget('testimonial.status');
			}
		}
		
		if (session()->has('testimonial')) {
			if (session()->has('testimonial.name')) {
				$name = session()->get('testimonial.name');
				$db->where(function ($q) use($request,$name) {
					$q->orWhere('name','like','%'.$name.'%');
				});
			}
			if (session()->has('testimonial.designation_company')) {
				$designation_company = session()->get('testimonial.designation_company');
				$db->where('designation_company','like','%'.$designation_company.'%');
			}
			if (session()->has('testimonial.status')) {
				$status = session()->get('testimonial.status');
				$db->where('status',$status);
			}
		}
		if($field != null){
			if($field == 'designation'){
				$field = 'designation_company';
			}
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		$testimonials = $db->paginate($limit);
		
		return view('Admin/testimonial/lists',compact('pageTitle','limit','testimonials','data','field','sort'));
	}
	
	public function edit(Request $request,$id){
		$pageTitle = "Testimonial Edit";
		
		$testimonial = Testimonials::findOrFail($id);
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'name' => 'required',
                'content' => 'required',
                //'designation_company' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$testimonial->name = $data['name'];
				$testimonial->content = $data['content'];
				$testimonial->designation_company = $data['designation_company'];
				
				if($testimonial->save()){
					\Session::flash('success', 'Testimonial edited successfully.');
					return redirect('/admin/testimonial/lists');
				}
				else{
					\Session::flash('error', 'Testimonial not edited successfully.');
					return redirect('/admin/testimonial/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Testimonial not edited successfully.');
				return redirect('/admin/testimonial/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/testimonial/edit',compact('pageTitle','testimonial','id'));
	}
	
	public function view($id){
		$pageTitle = "Testimonial View";
		$testimonial = Testimonials::where('id', $id)->get()->toArray();
		
		return view('Admin/testimonial/view',compact('pageTitle','testimonial'));
	}
	
	public function testimonialDelete($id){
		$testimonial = Testimonials::where('id', $id)->delete();
		if($testimonial){
			\Session::flash('success', 'Testimonial deleted successfully.');
			return \Redirect::to('/admin/testimonial/lists');
		}
		else{
			\Session::flash('error', 'Testimonial not deleted.');
			return \Redirect::to('/admin/testimonial/lists');
		}
	}
	
	public function action($id,$status){
		$testimonial=Testimonials::where('id', $id)->update(['status'=>$status]);
		if($testimonial){
			\Session::flash('success', 'Testimonial status updated successfully.');
			return \Redirect::to('/admin/testimonial/lists');
		}
		else{
			\Session::flash('error', 'Testimonial status not updated.');
			return \Redirect::to('/admin/testimonial/lists');
		}
	}
}
