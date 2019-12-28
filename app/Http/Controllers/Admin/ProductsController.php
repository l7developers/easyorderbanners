<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Facades\Image;
use Carbon\Carbon;

use DB;
use Image;
use Input;
use App\User;
use App\Category;
use App\Products;
use App\ProductsImages;
use App\ProductOptions;
use App\CustomOptions;
use App\ProductTabs;
use App\ProductVariant;
use App\ProductVariantValues;
use App\ProductVariantPrice;
use App\ProductShipping;
use App\ProductPrice;

class ProductsController extends Controller
{
    // Add Static pages function //
	
	public function add(Request $request){
		$pageTitle = 'Product Add'; 
		//$categories = Category::get();
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
		$options = CustomOptions::where('status',1)->pluck('name','id')->all();
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			if(!array_key_exists('no_artwork_required',$data)){
				$data['no_artwork_required'] = 0;
			}
			if(!array_key_exists('show_width_height',$data)){
				$data['show_width_height'] = 0;
			}
			if(!array_key_exists('double_side_print',$data)){
				$data['double_side_print'] = 0;
			}
			if(!array_key_exists('price_sqft_area',$data)){
				$data['price_sqft_area'] = 0;
			}
			if(!array_key_exists('variant',$data)){
				$data['variant'] = 0;
			}
			$validArr = [
				'name' => 'required|string|max:255',
				'slug' => 'required|unique:static_pages|unique:products|unique:categories',
                'category_id' => 'required',
                'product_detail' => 'required',
                'excerpt' => 'required',
                'short_description' => 'required',
                //'custom_option_id' => 'required|array|min:1',
                'image' => 'required',
                'product_image' => 'required',
                'turnaround_time' => 'required',
            ];
			$messages = [
				'category_id.required' => 'Category atlease select one',
				'image.required' => 'This image field is required.',
				'product_image.required' => 'Please select at least one.',
				'price_300.required' => 'This price field is required.',
				'price_500.required' => 'This price field is required.',
				'price_1000.required' => 'This price field is required.',
				'min_width.required' => 'This field is required.',
				'max_width.required' => 'This field is required.',
				'min_height.required' => 'This field is required.',
				'max_height.required' => 'This field is required.',
				'turnaround_time.required' => 'This field is required.',
				//'custom_option_id.required' => 'Custom option atlease select one',
			];
			
			$validation = Validator::make($data, $validArr,$messages);
			
			$validation->sometimes(['min_width','min_height'], 'required|numeric|min:1', function($input){
				return $input->show_width_height == 1;
			});
			
			if ($validation->passes()) {
				$products = new Products();
				
				$file_count = 1;
				$destinationPath = public_path('/uploads/product/');
				
				$products->cat_image_title = $data['cat_image_title'];
				if ($request->hasFile('cat_image')) {
					$fileName = $data['cat_image']->getClientOriginalName();
					$file = request()->file('cat_image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() .$file_count. '.' . $fileNameExt;
					
					$products->cat_image = $newName;
					$file->move($destinationPath, $newName);
					
					$img = Image::make(public_path('/uploads/product/'.$newName));
					$img->resize(385, 200);
					$img->save(public_path('/uploads/product/'.$newName));
					
					$file_count++;
				}
				
				$products->image_title = $data['image_title'];
				if ($request->hasFile('image')){
					$fileName = $data['image']->getClientOriginalName();
					$file = request()->file('image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() .$file_count. '.' . $fileNameExt;
					
					$products->image = $newName;
					$file->move($destinationPath, $newName);
					
					$img = Image::make(public_path('/uploads/product/'.$newName));
					$img->resize(345, 260);
					$img->save(public_path('/uploads/product/'.$newName));
					
					$file_count++;
				}
				
				$products->category_id = $data['category_id'];
				$products->name = $data['name'];
				$products->slug = $data['slug'];
				$products->description = $data['product_detail'];
				$products->art_file_preparations = $data['art_file'];
				$products->	design_templates = $data['design_template'];
				$products->excerpt = $data['excerpt'];
				$products->short_description = $data['short_description'];
				$products->no_artwork_required = $data['no_artwork_required'];
				$products->show_width_height = $data['show_width_height'];
				if($data['show_width_height'] == 1){
					$products->min_width = $data['min_width'];
					$products->max_width = $data['max_width'];
					$products->min_height = $data['min_height'];
					$products->max_height = $data['max_height'];
					$products->min_sqft = $data['min_sqft'];
				}
				$products->double_side_print = $data['double_side_print'];
				$products->shipping_weight = $data['shipping_weight'];
				$products->turnaround_time = $data['turnaround_time'];
				$products->meta_title = $data['meta_title'];
				$products->meta_tag = $data['meta_tag'];
				$products->meta_description = $data['meta_description'];
				
				if($products->save()){
					if (array_key_exists('custom',$data)) {
						foreach ($data['custom'] as $key => $val) {
							$TabObj = new ProductTabs();
							$TabObj->product_id = $products->id;
							$TabObj->title = $val['title'];
							$TabObj->body = $val['body'];
							$TabObj->save();
						}
					}
					if ($request->hasFile('product_image')) {
						$filess = $request->file('product_image');
						foreach ($filess as $key => $file) {
							$filename = $file->getClientOriginalName();
							$extension = $file->getClientOriginalExtension();
							$picture = date('His').time().$file_count."&$". $filename;
							
							$productsImages = DB::table('products_images');
							$data_array['product_id'] = $products->id;
							$data_array['type'] = 2;
							$data_array['weight'] = $data['product_images_weight'][$key];
							$data_array['name'] = $picture;
							$data_array['created_at'] = Carbon::now();
							$data_array['updated_at'] = Carbon::now();
							if($productsImages->insertGetId($data_array)){
								$file->move($destinationPath, $picture);
								
								$img = Image::make(public_path('/uploads/product/'.$picture));
								$img->resize(545, 545);
								$img->save(public_path('/uploads/product/'.$picture));								
							}
							$file_count++;
						}
					}
					\Session::flash('success', 'Product added complete.');
					return redirect('/admin/products/edit/'.$products->id);
				}
				else{
					\Session::flash('error', 'Product not added.');
					return redirect('/admin/products/add');
				}
			}
			else{
				\Session::flash('error', 'Product not added.');
				return redirect('/admin/products/add')->withErrors($validation)->withInput();
        	}
		}
		
		return view('Admin/products/add',compact('pageTitle','categories','options'));
	}
	
	// List of all products ////////
	public function lists(Request $request,$field='name',$sort='ASC'){
		$pageTitle = 'Products List'; 
		
		//$categories=Category::orderBy('name')->pluck('name', 'id')->all();
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
		
		$data = $request->all();
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('products');
		}
		
		if($field == 'created'){
			$field = 'created_at';
		}
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		\DB::enableQueryLog();
		
		$db=Products::whereIn('products.status',[1,0]);
		
		if($request->isMethod('post')){
			if(isset($data['name']) and !empty($data['name'])){
				session(['products.search' => $data['name']]);
			}else{
				session()->forget('products.search');
			}
			if(isset($data['category']) and !empty($data['category'])){
				session(['products.category' => $data['category']]);
			}else{
				session()->forget('products.category');
			}
		}
		
		if (session()->has('products')) {
			if (session()->has('products.search')) {
				$search = session()->get('products.search');
				$db->where(function ($q) use($request,$search) {
					$q->orWhere('products.name','like','%'.$search.'%');
					$q->orWhere('products.description','like','%'.$search.'%');
				});
			}
			if (session()->has('products.category')) {
				$category = session()->get('products.category');
				$db->where('products.category_id',$category);
			}
		}
		$db->leftJoin('categories as cat','cat.id','=','products.category_id');
		if($field == 'category'){
			$db->orderBy('cat.name',$sort);
		}else{
			$db->orderBy('products.'.$field,$sort);
		}
		
		$products=$db->with(['shipping'])->select('products.*','cat.name as cat_name')->paginate($limit);
		//pr($products->toArray());die;
		return view('Admin/products/lists',compact('pageTitle','products','limit','categories','field','sort'));
	}
	
	// View products ////////
	public function view(Request $request,$id){
		$pageTitle = 'Product View'; 
		$products=Products::where('id',$id)->with('Catgory','Images','Options','custom')->first();
		
		return view('Admin/products/view',compact('pageTitle','products','limit'));
	}
	
	// Edit Product ////
	public function edit(Request $request,$id){
		$pageTitle = 'Product Edit'; 
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
		
		$variant_values = ProductVariantValues::where('status',1)->pluck('value','id')->all();
		
		$options = CustomOptions::get()->where('status',1);
		\DB::enableQueryLog();
		$product = Products::where('id',$id)->with(['Catgory','Images','Options','product_prices','custom','variants','variantCombinantion'])->first();
		
		if($request->isMethod('post')){
			$data = $request->all();
			if(!array_key_exists('show_width_height',$data)){
				$data['show_width_height'] = 0;
			}
			if(!array_key_exists('price_sqft_area',$data)){
				$data['price_sqft_area'] = 0;
			}
			if(!array_key_exists('variant',$data)){
				$data['variant'] = 0;
			}
			//pr($data);die;
		}
		//qLog();
		//pr($product->toArray());die;
		//pr($variant_values);die;
		return view('Admin/products/edit',compact('pageTitle','categories','product','id','options','variant_values'));
	}
	
	// Edit Product Basic Informations //
	public function basic_edit(Request $request,$id){
		$product = Products::where('id',$id)->with(['Catgory','Images'])->first();
		
		if($request->isMethod('post')){
			$data = $request->all();
			if(!array_key_exists('no_artwork_required',$data)){
				$data['no_artwork_required'] = 0;
			}
			if(!array_key_exists('show_width_height',$data)){
				$data['show_width_height'] = 0;
			}
			if(!array_key_exists('double_side_print',$data)){
				$data['double_side_print'] = 0;
			}
			//pr($data);die;
			$validArr = [
				'name' => 'required|string|max:255',
				'slug' => 'required|string|max:255|unique:products,slug,'.$id.'|unique:static_pages,slug,'.$id.'|unique:categories,slug,'.$id,
				'category_id' => 'required',
                'product_detail' => 'required',
                'excerpt' => 'required',
                'short_description' => 'required',
                'turnaround_time' => 'required',
            ];
			$messages = [
				'category_id.required' => 'Category atlease select one',
				'product_image.required' => 'Please select at least one.',
				'min_width.required' => 'This field is required.',
				'max_width.required' => 'This field is required.',
				'min_height.required' => 'This field is required.',
				'max_height.required' => 'This field is required.',
				'turnaround_time.required' => 'This field is required.',
			];		
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
				$file_count = 1;
				$destinationPath = public_path('/uploads/product/');
				
				$product->cat_image_title = $data['cat_image_title'];
				if ($request->hasFile('cat_image')) {
					$fileName = $data['cat_image']->getClientOriginalName();
					$file = request()->file('cat_image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() .$file_count. '.' . $fileNameExt;
					
					$catImage = public_path('/uploads/product/'.$product->cat_image);
					if(file_exists($catImage) && !empty($product->cat_image)) {
						unlink($catImage);
					}
					$product->cat_image = $newName;
					$file->move($destinationPath, $newName);
					
					$img = Image::make(public_path('/uploads/product/'.$newName));
					$img->resize(385, 200);
					$img->save(public_path('/uploads/product/'.$newName));
					
					$file_count++;
				}
				
				$product->image_title = $data['image_title'];
				if ($request->hasFile('image')){
					$fileName = $data['image']->getClientOriginalName();
					$file = request()->file('image');
					$fileNameArr = explode('.', $fileName);
					$fileNameExt = end($fileNameArr);
					$newName = date('His').rand() . time() .$file_count. '.' . $fileNameExt;
					
					$productImage = public_path('/uploads/product/'.$product->image);
					if(file_exists($productImage) && !empty($product->image)) {
						unlink($productImage);
					}
					$product->image = $newName;
					$file->move($destinationPath, $newName);
					
					$img = Image::make(public_path('/uploads/product/'.$newName));
					$img->resize(345, 260);
					$img->save(public_path('/uploads/product/'.$newName));
					
					$file_count++;
				}
				
				$product->category_id = $data['category_id'];
				$product->name = $data['name'];
				$product->slug = $data['slug'];
				$product->description = $data['product_detail'];
				$product->art_file_preparations = $data['art_file'];
				$product->design_templates = $data['design_template'];
				$product->excerpt = $data['excerpt'];
				$product->short_description = $data['short_description'];
				$product->no_artwork_required = $data['no_artwork_required'];
				$product->show_width_height = $data['show_width_height'];
				if($data['show_width_height'] == 1){
					$product->min_width = $data['min_width'];
					$product->max_width = $data['max_width'];
					$product->min_height = $data['min_height'];
					$product->max_height = $data['max_height'];
					$product->min_sqft = $data['min_sqft'];
				}
				$product->double_side_print = $data['double_side_print'];
				$product->shipping_weight = $data['shipping_weight'];
				$product->turnaround_time = $data['turnaround_time'];
				$product->meta_title = $data['meta_title'];
				$product->meta_tag = $data['meta_tag'];
				$product->meta_description = $data['meta_description'];
				if($product->save()){
					$destinationPath = public_path('/uploads/product/');
					
					if (array_key_exists('extra_tab',$data) and count($data['extra_tab']) > 0) {
						foreach ($data['extra_tab'] as $key => $val) {
							$ExtraObj = ProductTabs::where('id',$key)->first();
							$ExtraObj->body = $val['body'];
							$ExtraObj->save();
						}
					}
					if (array_key_exists('custom',$data)) {
						foreach ($data['custom'] as $key => $val) {
							$TabObj = new ProductTabs();
							$TabObj->product_id = $product->id;
							$TabObj->title = $val['title'];
							$TabObj->body = $val['body'];
							$TabObj->save();
						}
					}
					if ($request->hasFile('product_image')) {
						$filess = $request->file('product_image');
						foreach ($filess as $key => $file) {
							$filename = $file->getClientOriginalName();
							$extension = $file->getClientOriginalExtension();
							$picture = date('His').time().$file_count."&$". $filename;
							
							$productsImages = DB::table('products_images');
							$data_array['product_id'] = $product->id;
							$data_array['type'] = 2;
							$data_array['weight'] = $data['new_product_images_weight'][$key];
							$data_array['name'] = $picture;
							$data_array['created_at'] = Carbon::now();
							$data_array['updated_at'] = Carbon::now();
							if($productsImages->insertGetId($data_array)){
								$file->move($destinationPath, $picture);
								
								$img = Image::make(public_path('/uploads/product/'.$picture));
								$img->resize(545, 545);
								$img->save(public_path('/uploads/product/'.$picture));
							}
							$file_count++;
						}
					}
					
					foreach((array)@$data['product_images_weight'] as $key=>$val){
						DB::table('products_images')->where('id',$key)->update(['weight'=>$val]);
					}
					
					\Session::flash('success', 'Product Updated successfully.');
					return redirect('/admin/products/edit/'.$id);
				}
				else{
					\Session::flash('error', 'Product not save, please correct below errors.');
					return redirect('/admin/products/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Product not save, please correct below errors.');
				return redirect('/admin/products/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		else{
			return redirect('/admin/products/lists');
		}
	}
	
	// Edit Product Price //
	public function price_edit(Request $request,$id){
		$pageTitle = 'Edit Product Price';
		$product = Products::where('id',$id)->first();
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$product->min_price =  $data['min_price'];
			
			$ids = array();
			if(array_key_exists('price_sqft_area',$data)){
				$i = 1;
				if(array_key_exists('old_prices',$data)){
					foreach($data['old_prices'] as $key=>$val){
						if($i == 1){
							$product->price_sqft_area = 1;
							$product->price = $val['price'];
							$product->save();	
						}
						
						$obj = ProductPrice::findOrFail($key);
						$obj->product_id = $id;
						$obj->price = $val['price'];
						$obj->min_area = $val['min_area'];
						$obj->max_area = $val['max_area'];
						$obj->save();
						$ids[] = $key;
						$i++;
					}
				}
				if(array_key_exists('prices',$data)){
					foreach($data['prices'] as $val){
						if($i == 1){
							$product->price_sqft_area = 1;
							$product->price = $val['price'];
							$product->save();	
						}
						$productPrice = new ProductPrice();
						$productPrice->product_id = $id;
						$productPrice->price = $val['price'];
						$productPrice->min_area = $val['min_area'];
						$productPrice->max_area = $val['max_area'];
						$productPrice->save();
						$ids[] = $productPrice->id;
						$i++;
					}
				}
			}else{
				$product->price_sqft_area = 0;
				$product->price = $data['price'];
				$product->save();
			}
			
			ProductPrice::where('product_id',$id)->whereNotIn('id',$ids)->delete();
			
			\Session::flash('success', 'Product Updated successfully.');
			return redirect('/admin/products/edit/'.$id);
		}else{
			return redirect('/admin/products/lists');
		}
	}
	
	// Edit Product Variants //
	public function edit_variants(Request $request,$id){
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			
			if(array_key_exists('variant',$data) and $data['variant'] == 1) {
				$variant_ids = array();
				$variant_values_ids = array();
				$variant_prices_ids = array();
				foreach ($data['options'] as $key => $val) {
					$variantObj = ProductVariant::where('id',$key)->where('product_id',$id)->first();
							
					if(!empty($variantObj)){
						$variantObj->name = urldecode($val['name']);
						$variantObj->save();
						$variant_ids[] = $variantObj->id;
					}else{
						$variantObj = new ProductVariant();
						$variantObj->product_id = $id;
						$variantObj->name = urldecode($val['name']);
						$variantObj->save();
						$variant_ids[] = $variantObj->id;
					}
					
					$varant_values = explode(',',$val['value']);
					foreach($varant_values as $k=>$v){
						$v = urldecode($v);
						$variantValueObj = ProductVariantValues::where('variant_id',$variantObj->id)->where('value',trim($v))->first();
								
						if(!empty($variantValueObj)){
							$variantValueObj->value = trim($v);
							$variantValueObj->save();
							$variant_values_ids[trim($v)] = $variantValueObj->id;
						}else{
							$variantValueObj = new ProductVariantValues();
							$variantValueObj->product_id = $id;
							$variantValueObj->variant_id = $variantObj->id;
							$variantValueObj->value = trim($v);
							$variantValueObj->save();
							$variant_values_ids[trim($v)] = $variantValueObj->id;
						}
					}
				}
				//pr($variant_values_ids);die;
				foreach ($data['variant_option_price'] as $key1 => $val1) {
					$temp_key1 = $key1;
					$key1 = urldecode($key1);
					if(is_array($val1) and array_key_exists('single',$val1)){
						foreach ($val1['single'] as $key2 => $val2) {
							$temp_key2 = $key2;
							$key2 = urldecode($key2);
							if($key2 == 'default'){
								$variantPriceObj = ProductVariantPrice::where('varient_id1',$variant_values_ids[trim($key1)])->where('product_id',$id)->first();
								if(!empty($variantPriceObj)){
									$variantPriceObj->price = $val2['price'];
									$variantPriceObj->shipping_weight = $data['variant_option_weight'][trim($temp_key1)]['weight'];
									$variantPriceObj->shipping_price = $data['variant_option_shipping'][trim($temp_key1)]['shipping'];
									$variantPriceObj->varient_id2 = null;
									$variantPriceObj->save();
									$variant_prices_ids[] = $variantPriceObj->id;
								}else{
									$variantPriceObj = new ProductVariantPrice();
									$variantPriceObj->product_id = $id;
									$variantPriceObj->varient_id1 = $variant_values_ids[trim($key1)];
									$variantPriceObj->varient_id2 = null;
									$variantPriceObj->price = $val2['price'];
									$variantPriceObj->shipping_weight = $data['variant_option_weight'][trim($temp_key1)]['weight'];
									$variantPriceObj->shipping_price = $data['variant_option_shipping'][trim($temp_key1)]['shipping'];
									$variantPriceObj->save();
									$variant_prices_ids[] = $variantPriceObj->id;
								}
							}else{
								$areas = explode('--',$key2);
								$variantPriceObj = ProductVariantPrice::where('varient_id1',$variant_values_ids[trim($key1)])->where('product_id',$id)->where('min_area',$areas[0])->where('max_area',$areas[1])->first();
								if(!empty($variantPriceObj)){
									$variantPriceObj->price = $val2['price'];
									$variantPriceObj->shipping_weight = $data['variant_option_weight'][trim($temp_key1)]['weight'];
									$variantPriceObj->shipping_price = $data['variant_option_shipping'][trim($temp_key1)]['shipping'];
									$variantPriceObj->varient_id2 = null;
									$variantPriceObj->save();
									$variant_prices_ids[] = $variantPriceObj->id;
								}else{
									$variantPriceObj = new ProductVariantPrice();
									$variantPriceObj->product_id = $id;
									$variantPriceObj->varient_id1 = $variant_values_ids[trim($key1)];
									$variantPriceObj->varient_id2 = null;
									$variantPriceObj->min_area = $areas[0];
									$variantPriceObj->max_area = $areas[1];
									$variantPriceObj->price = $val2['price'];
									$variantPriceObj->shipping_weight = $data['variant_option_weight'][trim($temp_key1)]['weight'];
									$variantPriceObj->shipping_price = $data['variant_option_shipping'][trim($temp_key1)]['shipping'];
									$variantPriceObj->save();
									$variant_prices_ids[] = $variantPriceObj->id;
								}
							}
						}
					}else{
						foreach ($val1 as $key2 => $val2) {
							$temp_key2 = $key2;
							$key2 = urldecode($key2);
							if(is_array($val2) and array_key_exists('default',$val2)){
								$variantPriceObj = ProductVariantPrice::where('varient_id1',$variant_values_ids[trim($key1)])->where('varient_id2',$variant_values_ids[trim($key2)])->where('product_id',$id)->first();
								
								if(!empty($variantPriceObj)){
									$variantPriceObj->price = $val2['default']['price'];
									$variantPriceObj->shipping_weight = $data['variant_option_weight'][trim($temp_key1).'--'.trim($temp_key2)]['weight'];
									$variantPriceObj->shipping_price = $data['variant_option_shipping'][trim($temp_key1).'--'.trim($temp_key2)]['shipping'];
									$variantPriceObj->save();
									$variant_prices_ids[] = $variantPriceObj->id;
								}else{
									$variantPriceObj = new ProductVariantPrice();
									$variantPriceObj->product_id = $id;
									$variantPriceObj->varient_id1 = $variant_values_ids[trim($key1)];
									$variantPriceObj->varient_id2 = $variant_values_ids[trim($key2)];
									$variantPriceObj->price = $val2['default']['price'];
									$variantPriceObj->shipping_weight = $data['variant_option_weight'][trim($temp_key1).'--'.trim($temp_key2)]['weight'];
									$variantPriceObj->shipping_price = $data['variant_option_shipping'][trim($temp_key1).'--'.trim($temp_key2)]['shipping'];
									$variantPriceObj->save();
									$variant_prices_ids[] = $variantPriceObj->id;
								}
							}else{
								foreach($val2 as $key3=>$val3){
									$areas = explode('--',$key3);
									$variantPriceObj = ProductVariantPrice::where('varient_id1',$variant_values_ids[trim($key1)])->where('varient_id2',$variant_values_ids[trim($key2)])->where('product_id',$id)->where('min_area',$areas[0])->where('max_area',$areas[1])->first();
									
									if(!empty($variantPriceObj)){
										$variantPriceObj->price = $val3['price'];
										$variantPriceObj->shipping_weight = $data['variant_option_weight'][trim($temp_key1).'--'.trim($temp_key2)]['weight'];
										$variantPriceObj->shipping_price = $data['variant_option_shipping'][trim($temp_key1).'--'.trim($temp_key2)]['shipping'];
										$variantPriceObj->save();
										$variant_prices_ids[] = $variantPriceObj->id;
									}else{
										$variantPriceObj = new ProductVariantPrice();
										$variantPriceObj->product_id = $id;
										$variantPriceObj->varient_id1 = $variant_values_ids[trim($key1)];
										$variantPriceObj->varient_id2 = $variant_values_ids[trim($key2)];
										$variantPriceObj->min_area = $areas[0];
										$variantPriceObj->max_area = $areas[1];
										$variantPriceObj->price = $val3['price'];
										$variantPriceObj->shipping_weight = $data['variant_option_weight'][trim($temp_key1).'--'.trim($temp_key2)]['weight'];
										$variantPriceObj->shipping_price = $data['variant_option_shipping'][trim($temp_key1).'--'.trim($temp_key2)]['shipping'];
										$variantPriceObj->save();
										$variant_prices_ids[] = $variantPriceObj->id;
									}
								}
							}
						}
					}
				}
				Products::where('id',$id)->update(['variant'=>1]);
				
				if(!empty($variant_ids)){
					ProductVariant::where('product_id',$id)->whereNotIn('id',$variant_ids)->delete();
				}
				
				if(!empty($variant_values_ids)){
					ProductVariantValues::where('product_id',$id)->whereNotIn('id',$variant_values_ids)->delete();
				}
				
				if(!empty($variant_prices_ids)){
					ProductVariantPrice::where('product_id',$id)->whereNotIn('id',$variant_prices_ids)->delete();
				}
			}else{
				Products::where('id',$id)->update(['variant'=>0]);
				ProductVariant::where('product_id',$id)->delete();
				ProductVariantValues::where('product_id',$id)->delete();
				ProductVariantPrice::where('product_id',$id)->delete();
			}
			\Session::flash('success', 'Product Updated successfully.');
			return redirect('/admin/products/edit/'.$id);
		}else{
			return redirect('/admin/products/lists');
		}
	}
	
	// Add Shipping //
	public function addShipping(Request $request,$id){
		$pageTitle = "Add Product Shipping";
		$product = Products::findOrFail($id);
		$shipping = ProductShipping::where('product_id',$id)->first();
		if($request->isMethod('post')){
			$data = $request->all();
			
			if($data['type'] != 'free_value'){
				$data['min_value'] = null;
			}
			
			if($data['type'] != 'flat'){
				$data['price'] = null;
				$data['additional_qty_price'] = null;
				if(!array_key_exists('reduce_price',$data)){
					$data['reduce_price'] = 0;
					$data['additional_qty_price'] = null;
				}
			}else{
				//$data['weight'] = null;
				if(!array_key_exists('reduce_price',$data)){
					$data['reduce_price'] = 0;
					$data['additional_qty_price'] = null;
				}
			}
			//pr($data);die;
					
			$validArr = [
				'type' => 'required',
				//'weight' => 'required|numeric',
				//'weight' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            ];
			$messages = [
				'type.required' => 'Shipping type select one',
				'additional_qty_price.required' => 'Additional quantity price field is required.',
				'weight.required' => 'Product Weight is required.',
				'weight.numeric' => 'Please Enter Numeric Product Weight.',
			];		
			$validation = Validator::make($data, $validArr);
			
			/* $validation->sometimes(['weight'], 'required|numeric', function($input)
			{
				return $input->type != 'flat';
			}); */
			
			$validation->sometimes(['min_value'], 'required', function($input)
			{
				return $input->type == 'free_value';
			});
			
			$validation->sometimes(['price'], 'required', function($input)
			{
				return $input->type == 'flat';
			});
			
			$validation->sometimes(['additional_qty_price'], 'required', function($input)
			{
				return $input->reduce_price == 1;
			});
			
			if ($validation->passes()) {
				
				if(count($shipping) > 0){
					$ship = ProductShipping::findOrFail($shipping->id);
				}else{
					$ship = new ProductShipping();
				}
				$ship->product_id = $id;
				$ship->type = $data['type'];
				$ship->min_value = $data['min_value'];
				//$ship->weight = $data['weight'];
				$ship->price = $data['price'];
				$ship->reduce_price = $data['reduce_price'];
				$ship->additional_qty_price = $data['additional_qty_price'];
				if($ship->save()){
					//Products::where('id',$id)->update(['shipping_weight'=>$data['weight']]);
					\Session::flash('success', 'Product shipping save successfully.');
					return redirect('/admin/products/lists');
				}
			}else{
				\Session::flash('error', 'Product shipping not save, please correct below errors.');
				return redirect('/admin/products/shipping/'.$id)->withErrors($validation)->withInput();
			}
		}
		
		return view('Admin/products/product_shipping_add',compact('pageTitle','id','product','shipping'));
	}
	
	// Delete Product //
	public function delete_product($id){
		$product=Products::where('id', $id)->delete();
		if($product){
			\Session::flash('success', 'Product deleted successfully.');
			return \Redirect::to('/admin/products/lists');
		}
		else{
			\Session::flash('error', 'Product not deleted.');
			return \Redirect::to('/admin/products/lists');
		}
	}
	
	// Action perform on Products ////
	public function action($id,$status){
		$product=Products::where('id', $id)->update(['status'=>$status]);
		if($product){
			\Session::flash('success', 'Product status updated successfully.');
			return \Redirect::to('/admin/products/lists');
		}
		else{
			\Session::flash('error', 'Product action not perform properly,Please try agail.');
			return \Redirect::to('/admin/products/lists');
		}
	}
	
	// Action perform on Products ////
	public function delete_image(Request $request){
		if($request->isMethod('post')){
			$data = $request->all();
			$detail = ProductsImages::where('id',$data['id'])->first();
			$productImage = public_path('/uploads/product/'.$detail->name);
			if(file_exists($productImage)) {
				unlink($productImage);
				ProductsImages::destroy($data['id']);
				return 1;
			}
			else{
				return 0;
			}
		}
		else{
			return 0;
		}
	}
	
	// Action perform on Products ////
	public function add_option(Request $request){
		$responce['flag'] = 0;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			
			$CatObj = new ProductOptions();
			$CatObj->product_id = $data['product_id'];
			$CatObj->option_id = $data['id'];
			$CatObj->save();
			$option_array = ProductOptions::where('product_id',$data['product_id'])->get()->toArray();
			
			$responce['flag'] = 1;
			$options = CustomOptions::get()->where('status',1);
			$responce['html'] = '<option value ="">Select Values</option>';
			foreach($options as $val){
				if($val->id == $data['id']){
					$responce['tr_html'] = '<tr><td>'.$val->label.'</td><td><button class="btn remove-delete btn-danger " data-id="'.$val->id.'" type="button"><i class="fa fa-trash"></i></button></td></tr>';
				}
				$check = '';
				if(in_array($val->id,array_column($option_array, 'option_id'))){
					$responce['html'] .= '<option disabled="disabled" value="'.$val->id.'">'.$val->label.'</option>';
				}
				else{
					$responce['html'] .= '<option value="'.$val->id.'">'.$val->label.'</option>';
				}
			}
			return json_encode($responce);
		}
		else{
			return json_encode($responce);
		}
	}
	
	// Action perform on Products ////
	public function delete_data(Request $request){
		$responce['flag'] = 0;
		$responce['html'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			
			$detail = DB::table($data['table'])->where('id',$data['id'])->delete();
			$responce['flag'] = 1;
			$option_array = ProductOptions::where('product_id',$data['product_id'])->get()->toArray();
			
			$options = CustomOptions::get()->where('status',1);
			$responce['html'] = '<option value ="">Select Values</option>';
			foreach($options as $val){
				if(in_array($val->id,array_column($option_array, 'option_id'))){
					$responce['html'] .= '<option disabled="disabled" value="'.$val->id.'">'.$val->label.'</option>';
				}
				else{
					$responce['html'] .= '<option value="'.$val->id.'">'.$val->label.'</option>';
				}
			}
			return json_encode($responce);
		}
		else{
			return json_encode($responce);
		}
	}
	
	/* Custom Option Function Below */
	
	// Add custom options ////////
	public function custom_add(Request $request){
		$pageTitle = 'Custom Option Add';
		if($request->isMethod('post')){
			$data = $request->all();
			if(!array_key_exists('paid_free',$data)){
				$data['paid_free'] = 0;
			}
			//pr($data);die;
			
			$validArr = [
				'name' => 'required|string|max:255',
				'label' => 'required|string|max:255',
                'select_type' => 'required',
                'field_group' => 'required',
                'price_formate' => 'required',
                //'description' => 'required',
            ];
			if($data['select_type'] == 1){
				$validArr['options'] = 'required';
			}
			else if($data['select_type'] == 2){
				if(empty($data['options'][0]['price'])){
					$validArr['Input price'] = 'required';
				}
			}
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
					$options = new CustomOptions();
					$options->field_group = $data['field_group'];
					$options->price_formate = $data['price_formate'];
					$options->name = $data['name'];
					$options->label = $data['label'];
					$options->option_type = $data['select_type'];
					$options->option_keys = json_encode($data['options']);
					$options->free = $data['paid_free'];
					$options->description = $data['description'];
					$options->save();
					\Session::flash('success', 'Custom option add complete.');
					return redirect('/admin/products/custom/option/lists');
			}
			else{
				return redirect('/admin/products/custom/option/add')->withErrors($validation)->withInput();
			}
		}
		return view('Admin/products/custom_option_add',compact('pageTitle'));
	}
	
	// Edit custom options ////////
	public function custom_edit(Request $request,$id){
		$pageTitle = 'Custom Option Edit';
		$option = CustomOptions::where('id',$id)->first();;
		if($request->isMethod('post')){
			$data = $request->all();
			if(!array_key_exists('paid_free',$data)){
				$data['paid_free'] = 0;
			}
			//pr($data);die;
			
			$validArr = [
				'name' => 'required|string|max:255',
				'label' => 'required|string|max:255',
                'select_type' => 'required',
                'field_group' => 'required',
                'price_formate' => 'required',
				//'description' => 'required',
            ];
			if($data['select_type'] == 1){
				$validArr['options'] = 'required';
			}
			else if($data['select_type'] == 2){
				if($data['paid_free'] == 0 and empty($data['options'][0]['price'])){
					$validArr['Input price'] = 'required';
				}
			}
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {
					$option->field_group = $data['field_group'];
					$option->price_formate = $data['price_formate'];
					$option->name = $data['name'];
					$option->label = $data['label'];
					$option->option_type = $data['select_type'];
					$option->option_keys = json_encode($data['options']);
					$option->free = $data['paid_free'];
					$option->description = $data['description'];
					$option->save();
					\Session::flash('success', 'Custom option save.');
					return redirect('/admin/products/custom/option/lists');
			}
			else{
				return redirect('/admin/products/custom/option/edit/'.$id)->withErrors($validation)->withInput();
			}
		}
		return view('Admin/products/custom_option_edit',compact('pageTitle','id','option'));
	}
	
	// List of all custom options ////////
	public function custom_lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = 'Custom Options List'; 
		
		$data = $request->all();
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('options');
		}
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		\DB::enableQueryLog();
		$db=CustomOptions::whereIn('status',[1,0]);
		if($request->isMethod('post')){
			if(isset($data['name']) and !empty($data['name'])){
				session(['options.name' => $data['name']]);
			}else{
				session()->forget('options.name');
			}
			if(isset($data['label']) and !empty($data['label'])){
				session(['options.label' => $data['label']]);
			}else{
				session()->forget('options.label');
			}
			if(isset($data['field_group']) and !empty($data['field_group'])){
				session(['options.field_group' => $data['field_group']]);
			}else{
				session()->forget('options.field_group');
			}
			if(isset($data['price_formate']) and !empty($data['price_formate'])){
				session(['options.price_formate' => $data['price_formate']]);
			}else{
				session()->forget('options.price_formate');
			}
			if(isset($data['status']) and !empty($data['status'])){
				session(['options.status' => $data['status']]);
			}else{
				session()->forget('options.status');
			}
		}
		
		if (session()->has('options')) {
			if (session()->has('options.name')) {
				$name = session()->get('options.name');
				$db->where(function ($q) use($request,$name) {
					$q->orWhere('name','like','%'.$name.'%');
				});
			}
			if (session()->has('options.label')) {
				$label = session()->get('options.label');
				$db->where(function ($q) use($request,$label) {
					$q->orWhere('label','like','%'.$label.'%');
				});
			}
			if (session()->has('options.field_group')) {
				$field_group = session()->get('options.field_group');
				$db->where(function ($q) use($request,$field_group) {
					$q->orWhere('field_group','like','%'.$field_group.'%');
				});
			}
			if (session()->has('options.price_formate')) {
				$price_formate = session()->get('options.price_formate');
				$db->where('price_formate',$price_formate);
			}
			if (session()->has('options.status')) {
				$status = session()->get('options.status');
				$db->where('status',$status);
			}
		}
		if($field != null){
			if($field == 'group'){
				$field = 'field_group';
			}
			if($field == 'format'){
				$field = 'price_formate';
			}
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		$options=$db->paginate($limit);
		//pr(qLog());
		return view('Admin/products/custom_option_lists',compact('pageTitle','options','limit','field','sort'));
	}
	
	// View Custom Option ////////
	public function custom_view(Request $request,$id){
		$pageTitle = 'Custom Option View'; 
		$options=CustomOptions::where('id',$id)->first();
		
		return view('Admin/products/custom_option_view',compact('pageTitle','options','limit'));
	}
	
	// Action perform on custom option ////
	public function custom_action($id,$status){
		$option=CustomOptions::where('id', $id)->update(['status'=>$status]);
		if($option){
			\Session::flash('success', 'Custom option status updated successfully.');
			return \Redirect::to('/admin/products/custom/option/lists');
		}
		else{
			\Session::flash('error', 'Custom option action not perform properly,Please try agail.');
			return \Redirect::to('/admin/products/custom/option/lists');
		}
	}
	
	public function delete_tab(Request $request){
		$responce['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			if(DB::table('product_tabs')->where('id',$data['id'])->delete()){
				$responce['status'] = 'success';
			}
		}
		return json_encode($responce);
	}
}
