<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Sliders;


class SlidersController extends Controller
{
    public function add(Request $request){
		$pageTitle = "Slider Add";
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'image' => 'required',
                'content' => 'required',
                'content_direction' => 'required'
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$slider = new Sliders();
				
				$destinationPath = public_path('/uploads/slider/');
				if ($request->hasFile('image')) {
					$fileName = $data['image']->getClientOriginalName();
					$file = request()->file('image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() . '.' . $fileNameExt;
					$file->move($destinationPath, $newName);
				}
				
				$slider->image = $newName;
				$slider->content = $data['content'];
				$slider->content_direction = $data['content_direction'];
				
				if($slider->save()){
					\Session::flash('success', 'Slider added successfully.');
					return redirect('/admin/slider/lists');
				}
				else{
					\Session::flash('error', 'Slider not added successfully.');
					return redirect('/admin/slider/add');
				}
			}
			else{
				\Session::flash('error', 'Slider not added successfully.');
				return redirect('/admin/slider/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/slider/add',compact('pageTitle'));
	}
	
	public function lists(Request $request){
		$pageTitle = "Sliders List";
		$data = $request->all();
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		$db = Sliders::whereIn('status',[1,0]);
				
		$db->orderBy('created_at','desc');
		
		$sliders = $db->paginate($limit);
		
		return view('Admin/slider/lists',compact('pageTitle','limit','sliders','data'));
	}
	
	public function edit(Request $request,$id){
		$pageTitle = "Slider Edit";
		
		$slider = Sliders::findOrFail($id);
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);
			$validArr = [
                'content' => 'required',
                'content_direction' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				
				$destinationPath = public_path('/uploads/slider/');
				if ($request->hasFile('image')) {
					$fileName = $data['image']->getClientOriginalName();
					$file = request()->file('image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() . '.' . $fileNameExt;
					if(@getimagesize(url('public/uploads/slider/'.$slider->image))){
						unlink(public_path('/uploads/slider/'.$slider->image));
					}
					$file->move($destinationPath, $newName);
					$slider->image = $newName;
				}
				
				$slider->content = $data['content'];
				$slider->content_direction = $data['content_direction'];
				
				if($slider->save()){
					\Session::flash('success', 'Slider edited successfully.');
					return redirect('/admin/slider/lists');
				}
				else{
					\Session::flash('error', 'Slider not edited successfully.');
					return redirect('/admin/slider/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Slider not edited successfully.');
				return redirect('/admin/slider/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/slider/edit',compact('pageTitle','slider','id'));
	}
	
	public function view($id){
		$pageTitle = "Slider View";
		$slider=Sliders::where('id', $id)->get()->toArray();
		
		return view('Admin/slider/view',compact('pageTitle','slider'));
	}
	
	public function sliderDelete($id){
		$slider=Sliders::where('id', $id)->delete();
		if($slider){
			\Session::flash('success', 'Slider deleted successfully.');
			return \Redirect::to('/admin/slider/lists');
		}
		else{
			\Session::flash('error', 'Slider not deleted.');
			return \Redirect::to('/admin/slider/lists');
		}
	}
	
	public function action($id,$status){
		$slider=Sliders::where('id', $id)->update(['status'=>$status]);
		if($slider){
			\Session::flash('success', 'Slider status updated successfully.');
			return \Redirect::to('/admin/slider/lists');
		}
		else{
			\Session::flash('error', 'Slider status not updated.');
			return \Redirect::to('/admin/slider/lists');
		}
	}
}
