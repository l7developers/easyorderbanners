<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\StaticPage;
use App\Testimonials;


class StaticpagesController extends Controller
{
    // Add Static pages function //
	
	public function add(Request $request){
		$pageTitle = 'Content Add';
		$all_testimonials = Testimonials::where('status',1)->get();
		foreach($all_testimonials as $value){
			$testimonials[$value->id] = $value->designation_company.'-( '.substr(strip_tags($value->content), 0, 50).' )';
		}
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'slug' => 'required|unique:static_pages|unique:products|unique:categories',
				'page_type' => 'required',
				'title' => 'required',
				'body' => 'required'
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$page = new StaticPage();
				$page->slug = $data['slug'];
				$page->page_type = $data['page_type'];
				$page->testimonials = $data['testimonials'];
				$page->title = $data['title'];
				$page->body = $data['body'];
				$page->meta_title = $data['meta_title'];
				$page->meta_tag = $data['meta_tag'];
				$page->meta_description = $data['meta_description'];
				$page->status = '1';
				
				if($page->save()){
					\Session::flash('success', 'Successfully Added the page');
					return \Redirect::to('/admin/staticpages/lists');
				}
				else{
					\Session::flash('error', 'Something went wrong, the page not added ,please try again.');
					return \Redirect::to('/admin/staticpages/lists');
				}
			}
			else{
				return redirect('/admin/staticpages/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/staticpages/add',compact('pageTitle','testimonials'));
	}
	
	// List of all staticpages ////////
	public function lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = 'Content List';
		$data = $request->all();
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('pages');
		}
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		\DB::enableQueryLog();
		
		$db = StaticPage::whereIn('status',[1,0]);
		
		if($request->isMethod('post')){
			if(isset($data['slug']) and !empty($data['slug'])){
				session(['pages.slug' => $data['slug']]);
			}else{
				session()->forget('pages.slug');
			}
			
			if(isset($data['title']) and !empty($data['title'])){
				session(['pages.title' => $data['title']]);
			}else{
				session()->forget('pages.title');
			}
			
			if(isset($data['status']) and $data['status'] != ''){
				session(['pages.status' => $data['status']]);
			}else{
				session()->forget('pages.status');
			}
		}
		
		if (session()->has('pages')) {
			if (session()->has('pages.slug')) {
				$slug = session()->get('pages.slug');
				$db->where(function ($q) use($request,$slug) {
					$q->orWhere('slug','like','%'.$slug.'%');
				});
			}
			if (session()->has('pages.title')) {
				$title = session()->get('pages.title');
				$db->where('title','like','%'.$title.'%');
			}
			if (session()->has('pages.status')) {
				$status = session()->get('pages.status');
				$db->where('status',$status);
			}
		}
		if($field != null){
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		$pages = $db->paginate($limit);
		
		return view('Admin/staticpages/lists',compact('pageTitle','limit','pages','field','sort'));
	}
	
	//-------------- View A StaticPage ----------//
	public function view(Request $request,$id){
		$pageTitle = 'Content View';
		
		$page=StaticPage::findOrFail($id);
		
		return view('Admin/staticpages/view',compact('pageTitle','page'));
	}
	
	// Edit Static Pages ////
	public function edit(Request $request,$id){
		$pageTitle = 'Content Edit';
		$all_testimonials = Testimonials::where('status',1)->get();
		foreach($all_testimonials as $value){
			$testimonials[$value->id] = $value->designation_company.'-( '.substr(strip_tags($value->content), 0, 50).' )';
		}
		
		$page = StaticPage::findOrFail($id);
		if($request->isMethod('post')){
			$data = $request->all();
			
			$validArr = [
                'slug' => 'required|unique:static_pages,slug,'.$id.'|unique:products,slug,'.$id.'|unique:categories,slug,'.$id,
				'page_type' => 'required',
				'title' => 'required',
				'body' => 'required'
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()){
				$page->slug = $data['slug'];
				$page->page_type = $data['page_type'];
				$page->testimonials = $data['testimonials'];
				$page->title = $data['title'];
				$page->body = $data['body'];
				$page->meta_title = $data['meta_title'];
				$page->meta_tag = $data['meta_tag'];
				$page->meta_description = $data['meta_description'];
				
				if($page->save()){
					\Session::flash('success', 'Static Page content changed successfull. ');
					return redirect('/admin/staticpages/lists/');
				}
				else{
					\Session::flash('error', 'Static Page content not changed successfull, Please try again. ');
				}
			}
			else{
				return redirect('/admin/staticpages/edit/'.$id)->withErrors($validation)->withInput();
			}			
		}
		$page=StaticPage::where('id', $id)->get()->toArray();
		
		return view('Admin/staticpages/edit',compact('pageTitle','page','testimonials'));
	}
	
	// Action perform on static pages ////
	public function action($id,$status){
		
		$page=StaticPage::where('id', $id)->update(['status'=>$status]);
		if($page){
			\Session::flash('success', 'Page status updated successfully.');
			return \Redirect::to('/admin/staticpages/lists');
		}
		else{
			\Session::flash('error', 'Page Action not perform properly,Please try again.');
			return \Redirect::to('/admin/staticpages/lists');
		}
	}
}
