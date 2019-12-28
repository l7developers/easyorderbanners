<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Reviews;


class ReviewsController extends Controller
{
    public function lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = "Reviews List";
		$data = $request->all();
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('testimonial');
		}
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		$db = Reviews::whereIn('status',[1,0])->with('user','product');		
		$db->orderBy('created_at','desc');
		
		$reviews = $db->paginate($limit);		
		
		return view('Admin/review/lists',compact('pageTitle','limit','reviews','data','field','sort'));
	}

	public function edit(Request $request,$id){
		$pageTitle = "Review Edit";
		
		$reviews = Reviews::findOrFail($id);
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'rating' => 'required',
                'comment' => 'required',                
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$reviews->rating = $data['rating'];
				$reviews->comment = $data['comment'];				
				
				if($reviews->save()){
					\Session::flash('success', 'Review edited successfully.');
					return redirect('/admin/reviews/lists');
				}
				else{
					\Session::flash('error', 'Review not edited successfully.');
					return redirect('/admin/reviews/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Review not edited successfully.');
				return redirect('/admin/reviews/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/review/edit',compact('pageTitle','reviews','id'));
	}	
	
	public function reviewDelete($id){
		//$review = Reviews::where('id', $id)->delete();
		$review = Reviews::where('id', $id)->update(['status'=>2]);
		if($review){
			\Session::flash('success', 'Review deleted successfully.');
			return \Redirect::to('/admin/reviews/lists');
		}
		else{
			\Session::flash('error', 'Review not deleted.');
			return \Redirect::to('/admin/reviews/lists');
		}
	}
	
	public function action($id,$status){
		$review=Reviews::where('id', $id)->update(['status'=>$status]);
		if($review){
			\Session::flash('success', 'Reviews status updated successfully.');
			return \Redirect::to('/admin/reviews/lists');
		}
		else{
			\Session::flash('error', 'Reviews status not updated.');
			return \Redirect::to('/admin/reviews/lists');
		}
	}
}
