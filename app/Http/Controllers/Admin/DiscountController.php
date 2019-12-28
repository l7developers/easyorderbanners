<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Discounts;
use App\Products;


class DiscountController extends Controller
{
    public function add(Request $request){
		$pageTitle = "Discount Add";
		$products = Products::where('status',1)->pluck('name','id')->all();
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'quantity' => 'required|numeric|min:1',
				'percent' => 'required|numeric|between:0,100',
				'product_type' => 'required',
            ];
			$messages = [
				'product_type.required' => 'The product is required.',
				'products.required' => 'Select atlease one product',
			];
			
			$validation = Validator::make($data, $validArr,$messages);
			
			$validation->sometimes(['products'], 'required', function($input){
				return $input->product_type == 2;
			});
			
			if ($validation->passes()) {
				$discount = new Discounts();
				$discount->quantity = $data['quantity'];
				$discount->percent = $data['percent'];
				if($data['product_type'] == 2){
					$discount->products = implode(',',$data['products']);
				}else{
					$discount->products = null;
				}
				$discount->status = '1';
				
				if($discount->save()){
					\Session::flash('success', 'Discount added complete.');
					return redirect('/admin/discount/lists');
				}
				else{
					\Session::flash('error', 'Discount not added.');
					return redirect('/admin/discount/add');
				}
			}
			else{
				\Session::flash('error', 'Discount not added.');
				return redirect('/admin/discount/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/discount/add',compact('pageTitle','products'));
	}
	
	public function lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = "Discounts List";
		$data = $request->all();
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		
		$db = Discounts::whereIn('status',[1,0]);
		
		if($field != null){
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		$discounts = $db->paginate($limit);
		//pr(qLog());
		//pr($discounts);die;
		return view('Admin/discount/lists',compact('pageTitle','limit','discounts','data','field','sort'));
	}
	
	public function edit(Request $request,$id){
		$pageTitle = "Discount Edit";
		
		$discount = Discounts::findOrFail($id);
		$products = Products::where('status',1)->pluck('name','id')->all();
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'quantity' => 'required|numeric|min:1',
				'percent' => 'required|numeric|between:0,100',
				'product_type' => 'required',
            ];
			$messages = [
				'product_type.required' => 'The product is required.',
				'products.required' => 'Select atlease one product',
			];
			
			$validation = Validator::make($data, $validArr,$messages);
			
			$validation->sometimes(['products'], 'required', function($input){
				return $input->product_type == 2;
			});
			if ($validation->passes()) { 
				$discount->quantity = $data['quantity'];
				$discount->percent = $data['percent'];
				if($data['product_type'] == 2){
					$discount->products = implode(',',$data['products']);
				}else{
					$discount->products = null;
				}
				$discount->status = '1';
				
				if($discount->save()){
					\Session::flash('success', 'Discount Edit.');
					return redirect('/admin/discount/lists');
				}
				else{
					\Session::flash('error', 'Discount not edit.');
					return redirect('/admin/discount/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Discount not Edit.');
				return redirect('/admin/discount/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/discount/edit',compact('pageTitle','discount','id','products'));
	}
	
	public function action($id,$status){
		
		$discount=Discounts::where('id', $id)->update(['status'=>$status]);
		if($discount){
			\Session::flash('success', 'Discount status updated successfully.');
			return \Redirect::to('/admin/discount/lists');
		}
		else{
			\Session::flash('error', 'Discounts status not updated.');
			return \Redirect::to('/admin/discount/lists');
		}
	}
}
