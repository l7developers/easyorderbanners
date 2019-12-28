<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DB;
use App\User;
use App\Category;
use Image;

class CategoriesController extends Controller
{
    //--------------- Add Category ---------------
	
	public function add(Request $request){
		$pageTitle = "Category Add";
		
		$categoriesData = Category::where('parent_id', 0)->where('status', 1)->with('child')->orderBy('name','ASC')->get();
		
		foreach($categoriesData as $val){
			$categories[$val->id] = $val->name;
			if(count($val->child) > 0){
				foreach($val->child as $child){
					$categories[$child->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child->name;	
				}
			}
		}
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:static_pages|unique:products|unique:categories',
                //'image' => 'required',
                'excerpt' => 'required',
                'description' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$str = htmlentities(mb_convert_encoding($data['description'], "HTML-ENTITIES", "UTF-8"), ENT_QUOTES);
			
				$category = new Category();
				if(!empty($data['parent_id']) and $data['parent_id'] != ''){
					$category->parent_id = $data['parent_id'];
				}
				
				$destinationPath = public_path('/uploads/category/');
				if ($request->hasFile('cat_image')) {
					$fileName = $data['cat_image']->getClientOriginalName();
					$file = request()->file('cat_image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() . '.' . $fileNameExt;					
				}
				
				$category->name = $data['name'];
				$category->slug = $data['slug'];
				$category->meta_title = $data['meta_title'];
				$category->meta_tag = $data['meta_tag'];
				$category->meta_description = $data['meta_description'];
				$category->excerpt = $data['excerpt'];
				$category->description = $data['description'];
				$category->image_title = $data['image_title'];
				
				$destinationPath = public_path('/uploads/category/');
				if ($request->hasFile('image')) {
					$fileName = $data['image']->getClientOriginalName();
					$file = request()->file('image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() . '.' . $fileNameExt;
					
					$category->image = $newName;
					$file->move($destinationPath, $newName);
					
					$img = Image::make(public_path('/uploads/category/'.$newName));
					$img->resize(385, 200);
					$img->save(public_path('/uploads/category/'.$newName));
				}
				
				if($category->save()){
					\Session::flash('success', 'Category added complete.');
					return redirect('/admin/category/lists');
				}
				else{
					\Session::flash('error', 'Category not added.');
					return redirect('/admin/category/add');
				}
			}
			else{
				\Session::flash('error', 'Category not added.');
				return redirect('/admin/category/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/category/add',compact('pageTitle','categories'));
	}
	
	//--------------- List Category ---------------
	
	public function lists(Request $request,$field='name',$sort='ASC'){
		$pageTitle = "Category List";
		$data = $request->all();
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('category');
		}
		
		if($field == 'created'){
			$field = 'created_at';
		}
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		//$limit = 50;
		
		$db=Category::where('parent_id',0)->whereIn('status',array(1,0));
		
		if($request->isMethod('post')){
			if(isset($data['name']) and !empty($data['name'])){
				session(['category.name' => $data['name']]);
			}else{
				session()->forget('category.name');
			}
		}
		
		if (session()->has('category')) {
			if (session()->has('category.name')) {
				$name = session()->get('category.name');
				$db->where('name','like','%'.$name.'%');
			}
		}
		
		$db->with(['child_list' => function ($query) use ($field,$sort){
							if($field != null){
								$query->orderBy($field,$sort);
							}
						}]);

		if($field != null){
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		$categories = $db->paginate($limit);
		//pr($categories->toArray());die;
		return view('Admin/category/lists',compact('pageTitle','limit','categories','data','field','sort'));
	}
	
	//--------------- Edit Category ---------------
	
	public function edit(Request $request,$id){
		$pageTitle = "Category Edit";
		
		$categoriesData = Category::where('parent_id', 0)->where('status', 1)->with('child')->orderBy('name','ASC')->get();
		
		foreach($categoriesData as $val){
			$categories[$val->id] = $val->name;
			if(count($val->child) > 0){
				foreach($val->child as $child){
					$categories[$child->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child->name;	
				}
			}
		}
		$category = Category::findOrFail($id);
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
						'name' => 'required|string|max:255',
						'slug' => 'required|string|max:255|unique:static_pages,slug,'.$id.'|unique:products,slug,'.$id.'|unique:categories,slug,'.$id,
						'excerpt' => 'required',
						'description' => 'required',
					];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				/*$str = htmlentities(mb_convert_encoding($data['description'], "HTML-ENTITIES", "UTF-8"), ENT_QUOTES);			*/
				if(!empty($data['parent_id']) and $data['parent_id'] != ''){
					$category->parent_id = $data['parent_id'];
				}
				$category->name = $data['name'];
				$category->excerpt = $data['excerpt'];
				$category->description = $data['description'];
				$category->slug = $data['slug'];
				$category->meta_title = $data['meta_title'];
				$category->meta_tag = $data['meta_tag'];
				$category->meta_description = $data['meta_description'];				
				$category->image_title = $data['image_title'];
				
				$destinationPath = public_path('/uploads/category/');
				$count = 1002;
				if ($request->hasFile('image')) {
					$fileName = $data['image']->getClientOriginalName();
					$file = request()->file('image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() . $count.'.' . $fileNameExt;					
					$catImage = public_path('/uploads/category/'.$category->image);
					if(file_exists($catImage)) {
						//unlink($catImage);
					}
					$file->move($destinationPath, $newName);
					
					$img = Image::make(public_path('/uploads/category/'.$newName));
					$img->resize(385, 200);
					$img->save(public_path('/uploads/category/'.$newName));
					
					$category->image = $newName;
				}
				
				if($category->save()){
					\Session::flash('success', 'Category Edit.');
					return redirect('/admin/category/lists');
				}
				else{
					\Session::flash('error', 'Category not save, please correct below errors.');
					return redirect('/admin/category/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Category not Edit.');
				return redirect('/admin/category/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		//pr($category);die;
		return view('Admin/category/edit',compact('pageTitle','category','id','categories'));
	}
	
	public function view($id){
		$pageTitle = "Category View";
		$category=Category::where('id', $id)->get()->toArray();
		return view('Admin/category/view',compact('pageTitle','category'));
	}
	
	//--------------- Action on Category ---------------
	
	public function action($id,$status,$extra_field = null){
		$category=Category::where('id', $id)->update(['status'=>$status]);
		if($category){
			\Session::flash('success', 'Category status updated successfully.');
		}
		else{
			\Session::flash('error', 'Category status not updated successfully.');
		}
		
		if(null !== $extra_field){
			return \Redirect::to('/admin/category/'.$extra_field.'/lists');
		}else{
			return \Redirect::to('/admin/category/lists');
		}
	}
}
