<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DB;
use App\User;
use App\Menus;
use App\StaticPage;
use App\Category;
use App\Products;

class MenusController extends Controller
{
    //--------------- Add Menu ---------------
	
	public function add(Request $request){
		$pageTitle = "Menu Add";
		
		//$asd = Menus::where('parent_id', 22)->max('weight');
		//pr($asd);die;
		
		$menus = Menus::select('id', 'name')->where('parent_id', 0)->where('status', 1)->pluck('name','id')->all();
		
		$pages = StaticPage::where('status', 1)->orderBy('title','ASC')->pluck('title','id')->all();
		
		$categoriesData = Category::where('parent_id', 0)->where('status', 1)->with('child')->orderBy('name','ASC')->get();
		
		foreach($categoriesData as $val){
			$categories[$val->id] = $val->name;
			if(count($val->child) > 0){
				foreach($val->child as $child){
					$categories[$child->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child->name;

					if(count($child->child) > 0){
						foreach($child->child as $child2){
							$categories[$child2->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child2->name;	
						}
					}	
				}
			}
		}
		
		$products = Products::where('status', 1)->pluck('name','id')->all();
		
		if($request->isMethod('post')){
			$data = $request->all();
			if(empty($data['menu_parent'])){
				$data['menu_parent'] = 0;
			}			
			//pr($data);die;
			$validArr = [
                'name' => 'required|string|max:255',
                'type' => 'required'
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$destinationPath = public_path('/uploads/menu/');
				
				$menu = new Menus();
				$menu->parent_id = $data['menu_parent'];
				$menu->type = $data['type'];				
				$menu->name = $data['name'];
				
				$weight = Menus::where('parent_id', $data['menu_parent'])->max('weight');
				$menu->weight = $weight + 1;
				
				if($data['type'] == 'page'){
					$menu->page_id = $data['page'];
				}
				else if($data['type'] == 'product'){
					$menu->product_id = $data['product'];
				}
				else if($data['type'] == 'category'){
					$menu->category_id = $data['category'];
				}
				else if($data['type'] == 'static'){
					$menu->static = $data['static'];
				}
				
				if ($request->hasFile('menu_image')) {
					$fileName = $data['menu_image']->getClientOriginalName();
					$file = request()->file('menu_image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() . '.' . $fileNameExt;
					
					$file->move($destinationPath, $newName);
					
					$menu->image_name = $newName;
				}
				
				if($menu->save()){
					\Session::flash('success', 'Menu added complete.');
					return redirect('/admin/menu/lists');
				}
				else{
					\Session::flash('error', 'Menu not added.');
					return redirect('/admin/menu/add');
				}
			}
			else{
				\Session::flash('error', 'Menu not added.');
				return redirect('/admin/menu/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/menus/add',compact('pageTitle','menus','pages','categories','products'));
	}
	
	//--------------- List Menus ---------------
	
	public function lists(Request $request){
		$pageTitle = "Menu List";		
		$menulist = Menus::with('menu')->where('parent_id',0)->orderBy('weight','ASC')->get();	
		//qLog();	
		return view('Admin/menus/lists',compact('pageTitle','menulist'));
	}
	
	//--------------- Edit Menu ---------------
	
	public function edit(Request $request,$id){
		$pageTitle = "Menu Edit";
		
		$menus = Menus::select('id', 'name')->where('status', 1)->pluck('name','id')->all();
		
		$pages = StaticPage::where('status', 1)->pluck('title','id')->all();
		
		$categoriesData = Category::where('parent_id', 0)->where('status', 1)->with('child')->orderBy('name','ASC')->get();
		foreach($categoriesData as $val){
			$categories[$val->id] = $val->name;
			if(count($val->child) > 0){
				foreach($val->child as $child){
					$categories[$child->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child->name;	

					if(count($child->child) > 0){
						foreach($child->child as $child2){
							$categories[$child2->id] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--'.$child2->name;	
						}
					}
				}
			}
		}
		
		$products = Products::where('status', 1)->pluck('name','id')->all();	
		
		$menu = Menus::findOrFail($id);
		if($request->isMethod('post')){
			$data = $request->all();
			if(empty($data['menu_parent'])){
				$data['menu_parent'] = 0;
			}
			//pr($data);die;
			$validArr = [
                'name' => 'required|string|max:255',
                'type' => 'required'
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$destinationPath = public_path('/uploads/menu/');
				
				$menu->parent_id = $data['menu_parent'];
				$menu->type = $data['type'];
				$menu->name = $data['name'];
				
				if($data['type'] == 'page'){
					$menu->page_id = $data['page'];
					$menu->product_id = null;
					$menu->category_id = null;
					$menu->static = null;
				}
				else if($data['type'] == 'product'){
					$menu->product_id = $data['product'];
					$menu->page_id = null;
					$menu->category_id = null;
					$menu->static = null;
				}
				else if($data['type'] == 'category'){
					$menu->category_id = $data['category'];
					$menu->product_id = null;
					$menu->page_id = null;
					$menu->static = null;
				}
				else if($data['type'] == 'static'){
					$menu->static = $data['static'];
					$menu->product_id = null;
					$menu->category_id = null;
					$menu->page_id = null;
				}
				
				if ($request->hasFile('new_menu_image')) {
					$fileName = $data['new_menu_image']->getClientOriginalName();
					$file = request()->file('new_menu_image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() . '.' . $fileNameExt;
					
					$oldImage = public_path('/uploads/menu/'.$menu->image_name);
					if(!empty($menu->image_name) and file_exists($oldImage)) {
						unlink($oldImage);
					}
					
					$file->move($destinationPath, $newName);
					$menu->image_name = $newName;
				}
				if($menu->save()){
					\Session::flash('success', 'Menu Edit.');
					return redirect('/admin/menu/lists');
				}
				else{
					\Session::flash('error', 'Menu not edit.');
					return redirect('/admin/menu/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Menu not Edit.');
				return redirect('/admin/menu/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/menus/edit',compact('pageTitle','menus','pages','categories','products','id','menu'));
	}
	
	
	public function delete($id){
		$menu=Menus::where('id', $id)->orWhere('parent_id',$id)->delete();
		if($menu){
			\Session::flash('success', 'Menu deleted successfully.');
			return \Redirect::to('/admin/menu/lists');
		}
		else{
			\Session::flash('error', 'Menu not deleted successfully.');
			return \Redirect::to('/admin/menu/lists');
		}
		
	}
	
	public function sorting(Request $request){
		$responce['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all()['data'];
			//pr($data);die();
			foreach($data as $key => $val){
				$id= $val['id'];
				Menus::where('id', $id)->update(['weight'=>$key,'parent_id'=>0]);	
				foreach((array)@$val['children'] as $key2=>$val2){
					$subid= $val2['id'];
					Menus::where('id', $subid)->update(['weight'=>$key2,'parent_id'=>$id]);	
				}
				
			}
			$responce['status'] = 'success';
		}
		return $responce;
	}
}
