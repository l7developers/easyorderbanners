<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\User;
use App\HomePages;
use App\CustomerLogos;
use App\HomeImages;
use App\Products;
use Image;
use DB;


class HomePageController extends Controller
{
    // Add Top Bule section Content function //
	public function top_blue(Request $request){
		$pageTitle = "Add Top Bule Section Content";
		
		$obj = HomePages::where('type','topblue')->first();
		if(count($obj) < 1){
			$obj = new HomePages();
		}
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'title' => 'required',
                'description' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				
				$obj->type = 'topblue';
				$obj->title = $data['title'];
				$obj->description = $data['description'];
				
				if($obj->save()){
					\Session::flash('success', 'Content added successfully.');
					return redirect('/admin/home/top-blue')->withErrors($validation)->withInput();
				}else{
					\Session::flash('error', 'Content not added, please try again.');
					return redirect('/admin/home/top-blue')->withErrors($validation)->withInput();
				}
			}else{
				\Session::flash('error', 'Content not added, please try again.');
				return redirect('/admin/home/top-blue')->withErrors($validation)->withInput();
			}
		}
		
		return view('Admin/home_page/top_blue',compact('pageTitle','obj'));
	}
	
	// Add Customers Logo function //
	public function customers_logos_add(Request $request){
		$pageTitle = "Add Customers logo";
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'title' => 'required|string|max:255',
                'image' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
				$obj = new CustomerLogos();
				$obj->title = $data['title'];
				$obj->link = $data['link'];
				
				$destinationPath = public_path('/uploads/home/customers');
				if ($request->hasFile('image')) {
					$fileName = $data['image']->getClientOriginalName();
					$file = request()->file('image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() . '.' . $fileNameExt;
					
					$obj->image = $newName;
					$file->move($destinationPath, $newName);
					
					//$img = Image::make(public_path('/uploads/home/customers/'.$newName));
					//$img->resize(150, 120);
					//$img->save(public_path('/uploads/home/customers/'.$newName));
				}
				
				if($obj->save()){
					\Session::flash('success', 'Customer Logo added successfully.');
					return redirect('/admin/home/customers-logo-list');
				}else{
					\Session::flash('error', 'Customer Logo not added, please try again.');
					return redirect('/admin/home/customers-logo-add')->withErrors($validation)->withInput();
				}
			}else{
				\Session::flash('error', 'Customer Logo not added, please try again.');
				return redirect('/admin/home/customers-logo-add')->withErrors($validation)->withInput();
			}
		}
		
		return view('Admin/home_page/customers_logo_add',compact('pageTitle'));
	}
	
	// Edit Customers Logo function //
	public function customers_logos_edit(Request $request,$id){
		$pageTitle = "Edit Customers logo";
		$obj = CustomerLogos::findOrFail($id);
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'title' => 'required|string|max:255',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
				$obj->title = $data['title'];
				$obj->link = $data['link'];
				
				$destinationPath = public_path('/uploads/home/customers');
				if ($request->hasFile('image')) {
					$fileName = $data['image']->getClientOriginalName();
					$file = request()->file('image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() . '.' . $fileNameExt;
					
					if(!empty($obj->image))
						unlink('public/uploads/home/customers/'.$obj->image);
					
					$obj->image = $newName;
					$file->move($destinationPath, $newName);
				}
				
				if($obj->save()){
					\Session::flash('success', 'Customer Logo edited successfully.');
					return redirect('/admin/home/customers-logo-list');
				}else{
					\Session::flash('error', 'Customer Logo not edited, please try again.');
					return redirect('/admin/home/customers-logo-edit/'.$id)->withErrors($validation)->withInput();
				}
			}else{
				\Session::flash('error', 'Customer Logo not edited, please try again.');
				return redirect('/admin/home/customers-logo-edit/'.$id)->withErrors($validation)->withInput();
			}
		}
		
		return view('Admin/home_page/customers_logo_edit',compact('pageTitle','obj'));
	}
	
	public function customers_logos_list(){
		$pageTitle = 'Customer Logo List';
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		$db = CustomerLogos::whereIn('status',[1,0]);
		$db->orderBy('created_at','desc');
		$logos = $db->paginate($limit);
		return view('Admin/home_page/customers_logo_list',compact('pageTitle','logos','limit'));
	}
	
	// Action perform on Customer Logo ////
	public function customer_logo_action($id,$status){
		
		$logo=CustomerLogos::where('id', $id)->update(['status'=>$status]);
		if($logo){
			\Session::flash('success', 'Customer Logo status updated successfully.');
			return \Redirect::to('/admin/home/customers-logo-list');
		}
		else{
			\Session::flash('error', 'Customer Logo status not updated, Please try again.');
			return \Redirect::to('/admin/home/customers-logo-list');
		}
	}

	public function images(Request $request){
		$pageTitle = 'Product Carousel-1';
		
		$obj = HomePages::where('type','images')->first();
		if(count($obj) < 1){
			$obj = new HomePages();
		}
		$images = unserialize($obj->info);
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'heading' => 'required',
                'description' => 'required',
                'images.*.rollover_text' => 'required',
                'images.*.rollover_button_link' => 'required',
                'images.*.rollover_button_text' => 'required',
            ];
			
			$message = [
				'images.*.rollover_text.required' => 'The rollover text',
				'images.*.rollover_button_link.required' => 'The rollover button link',
				'images.*.rollover_button_text.required' => 'The rollover button text',
			];
			
			$validation = Validator::make($data, $validArr);
			if($validation->passes()){
				$obj->type = 'images';
				$obj->title = $data['heading'];
				$obj->description = $data['description'];
				$info = array();
				$file_count = 1;
				$destinationPath = public_path('/uploads/home/images/');
				foreach($data['images'] as $key=>$val){
					if ($request->hasFile('images.'.$key.'.image')) {
						$fileName = $data['images'][$key]['image']->getClientOriginalName();
						$file = request()->file('images.'.$key.'.image');
						$fileNameArr = explode('.', $fileName);
						$fileNameExt = end($fileNameArr);
						$newName = date('His').rand() . time().$file_count . '.' . $fileNameExt;
						
						unlink('public/uploads/home/images/'.$images[$key]['image']);
						
						$data['images'][$key]['image'] = $newName;
						$file->move($destinationPath, $newName);
						
						$width = 1;
						$height = 1;
						if($key == 1){
							$width = 157;
							$height = 309;
						}else if($key == 2){
							$width = 310;
							$height = 305;
						}
						else if($key == 3){
							$width = 369;
							$height = 158;
						}
						else if($key == 4){
							$width = 181;
							$height = 158;
						}
						else if($key == 5){
							$width = 320;
							$height = 160;
						}
						else if($key == 6){
							$width = 137;
							$height = 140;
						}
						else if($key == 7){
							$width = 413;
							$height = 140;
						}
						
						$img = Image::make(public_path('/uploads/home/images/'.$newName));
						$img->resize($width, $height);
						$img->save(public_path('/uploads/home/images/'.$newName));
					}else{
						$data['images'][$key]['image'] = $images[$key]['image'];
					}
					$file_count++;
				}
				//pr($data);
				$obj->info = serialize($data['images']);
				if($obj->save()){
					\Session::flash('success', 'Home page images content added successfully.');
					return redirect('/admin/home/images');
				}die;
			}else{
				\Session::flash('error', 'Content not added, please try again.');
				return redirect('/admin/home/images')->withErrors($validation)->withInput();
			}
		}
		//pr($images);
		return view('Admin/home_page/images',compact('pageTitle','obj','images'));
	}
	
	public function carousel1(Request $request){
		$pageTitle = 'Carousel-1';
		
		$obj = HomePages::where('type','carousel1')->with('images')->first();
		if(count($obj) < 1){
			$obj = new HomePages();
		}
		
		$sub_obj = HomePages::where('type','sub_carousel1')->first();
		if(count($sub_obj) < 1){
			$sub_obj = new HomePages();
		}
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'title' => 'required',
                'description' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
				$obj->type = 'carousel1';
				$obj->title = $data['title'];
				$obj->description = $data['description'];
				if($obj->save()){
					if(array_key_exists('carousel1_images',$data)){
						foreach($data['carousel1_images'] as $key=>$val){
							DB::table('homeimages')->where('id',$key)->update(['weight'=>$val['weight'],'title'=>$val['title']]);
						}
					}
					
					$destinationPath = public_path('/uploads/home/carousel1/');
					$file_count = 1;
					if ($request->hasFile('images')) {
						$filess = $request->file('images');
						foreach ($filess as $key => $file) {
							$filename = $file->getClientOriginalName();
							$extension = $file->getClientOriginalExtension();
							$picture = date('His').time().$file_count.".". $extension;
							
							$homeImages = DB::table('homeimages');
							$data_array['homepage_id'] = $obj->id;
							$data_array['weight'] = $data['carousel_images_weight'][$key];
							$data_array['title'] = $data['carousel_images_title'][$key];
							$data_array['name'] = $picture;
							$data_array['status'] = 1;
							$data_array['created_at'] = Carbon::now();
							$data_array['updated_at'] = Carbon::now();
							if($homeImages->insertGetId($data_array)){
								$file->move($destinationPath, $picture);
								
								$img = Image::make(public_path('/uploads/home/carousel1/'.$picture));
								$img->resize(545, 545);
								$img->save(public_path('/uploads/home/carousel1/'.$picture));
							}
							$file_count++;
						}
					}
					
					$sub_obj->type = 'sub_carousel1';
					$sub_obj->title = $data['sub_title'];
					$sub_obj->description = $data['sub_description'];
					
					if ($request->hasFile('sub_image')) {
						$fileName = $data['sub_image']->getClientOriginalName();
						$file = request()->file('sub_image');
						$fileNameArr = explode('.', $fileName);
						$fileNameExt = end($fileNameArr);
						$newName = date('His').rand() . time() . '.' . $fileNameExt;
						
						if(!empty($sub_obj->img))
							unlink('public/uploads/home/carousel1/'.$sub_obj->img);
						
						$sub_obj->img = $newName;
						$file->move($destinationPath, $newName);
						
						$img = Image::make(public_path('/uploads/home/carousel1/'.$newName));
						$img->resize(345, 260);
						$img->save(public_path('/uploads/home/carousel1/'.$newName));
					}
					$sub_obj->save();
					\Session::flash('success', 'Carousel-1 Updated Successfully.');
					return redirect('/admin/home/carousel1');
				}
			}else{
				\Session::flash('error', 'Content not added, please try again.');
				return redirect('/admin/home/carousel1')->withErrors($validation)->withInput();
			}
		}
		//pr($obj);
		return view('Admin/home_page/carousel1',compact('pageTitle','obj','sub_obj'));
	}
	
	public function carousel2(Request $request){
		$pageTitle = 'Carousel-2';
		$products = Products::where('status',1)->pluck('name','id')->all();
		
		$obj = HomePages::where('type','carousel2')->first();
		if(count($obj) < 1){
			$obj = new HomePages();
		}
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'title' => 'required',
                'description' => 'required',
                'products' => 'required|array|min:1',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
				$obj->type = 'carousel2';
				$obj->title = $data['title'];
				$obj->description = $data['description'];
				$obj->product_id = implode(',',$data['products']);
				if($obj->save()){
					\Session::flash('success', 'Carousel-2 Updated Successfully.');
					return redirect('/admin/home/carousel2');
				}
			}else{
				\Session::flash('error', 'Content not added, please try again.');
				return redirect('/admin/home/carousel2')->withErrors($validation)->withInput();
			}
		}
		return view('Admin/home_page/carousel2',compact('pageTitle','obj','products'));
	}
	
	public function carousel3(Request $request){
		$pageTitle = 'Carousel-3';
		
		$obj = HomePages::where('type','carousel3')->with('images')->first();
		if(count($obj) < 1){
			$obj = new HomePages();
		}
		
		$sub_obj = HomePages::where('type','sub_carousel3')->first();
		if(count($sub_obj) < 1){
			$sub_obj = new HomePages();
		}
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'title' => 'required',
                'description' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
				$obj->type = 'carousel3';
				$obj->title = $data['title'];
				$obj->description = $data['description'];
				if($obj->save()){
					if(array_key_exists('carousel3_images',$data)){
						foreach($data['carousel3_images'] as $key=>$val){
							DB::table('homeimages')->where('id',$key)->update(['weight'=>$val['weight'],'title'=>$val['title']]);
						}
					}
					
					$destinationPath = public_path('/uploads/home/carousel3/');
					$file_count = 1;
					if ($request->hasFile('images')) {
						$filess = $request->file('images');
						foreach ($filess as $key => $file) {
							$filename = $file->getClientOriginalName();
							$extension = $file->getClientOriginalExtension();
							$picture = date('His').time().$file_count.".". $extension;
							
							$homeImages = DB::table('homeimages');
							$data_array['homepage_id'] = $obj->id;
							$data_array['weight'] = $data['carousel_images_weight'][$key];
							$data_array['title'] = $data['carousel_images_title'][$key];
							$data_array['name'] = $picture;
							$data_array['status'] = 1;
							$data_array['created_at'] = Carbon::now();
							$data_array['updated_at'] = Carbon::now();
							if($homeImages->insertGetId($data_array)){
								$file->move($destinationPath, $picture);
								
								$img = Image::make(public_path('/uploads/home/carousel3/'.$picture));
								$img->resize(545, 545);
								$img->save(public_path('/uploads/home/carousel3/'.$picture));
							}
							$file_count++;
						}
					}
					
					$sub_obj->type = 'sub_carousel3';
					$sub_obj->title = $data['sub_title'];
					$sub_obj->description = $data['sub_description'];
					
					if ($request->hasFile('sub_image')) {
						$fileName = $data['sub_image']->getClientOriginalName();
						$file = request()->file('sub_image');
						$fileNameArr = explode('.', $fileName);
						$fileNameExt = end($fileNameArr);
						$newName = date('His').rand() . time() . '.' . $fileNameExt;
						
						if(!empty($sub_obj->img))
							unlink('public/uploads/home/carousel3/'.$sub_obj->img);
						
						$sub_obj->img = $newName;
						$file->move($destinationPath, $newName);
						
						$img = Image::make(public_path('/uploads/home/carousel3/'.$newName));
						$img->resize(345, 260);
						$img->save(public_path('/uploads/home/carousel3/'.$newName));
					}
					$sub_obj->save();
					\Session::flash('success', 'Product Carousel-3 Updated Successfully.');
					return redirect('/admin/home/carousel3');
				}
			}else{
				\Session::flash('error', 'Content not updated, please try again.');
				return redirect('/admin/home/carousel3')->withErrors($validation)->withInput();
			}
		}
		//pr($obj);
		return view('Admin/home_page/carousel3',compact('pageTitle','obj','sub_obj'));
	}
	
	public function carousel4(Request $request){
		$pageTitle = 'Carousel-4';
		$products = Products::where('status',1)->pluck('name','id')->all();
		
		$obj = HomePages::where('type','carousel4')->first();
		if(count($obj) < 1){
			$obj = new HomePages();
		}
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'title' => 'required',
                'description' => 'required',
                'products' => 'required',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
				$obj->type = 'carousel4';
				$obj->title = $data['title'];
				$obj->description = $data['description'];
				$obj->product_id = implode(',',$data['products']);
				if($obj->save()){
					\Session::flash('success', 'Carousel-4 Updated Successfully.');
					return redirect('/admin/home/carousel4');
				}
			}else{
				\Session::flash('error', 'Content not added, please try again.');
				return redirect('/admin/home/carousel4')->withErrors($validation)->withInput();
			}
		}
		return view('Admin/home_page/carousel4',compact('pageTitle','obj','products'));
	}
	
}
