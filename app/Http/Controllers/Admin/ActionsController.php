<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\User;
use App\Email;


class ActionsController extends Controller
{
	//------------ Update function ------------//
	
	public function update(Request $request){
		$res['status'] = '';
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			foreach($data['update_fields'] as $value){
				$db = DB::table($data['table'])->where($data['where_field'],$data['where_value'])->update([$value['update_field']=>$value['update_value']]);
				if($db){
					$res['status'] = 'success';
				}
				//pr(qLog());
			}
		}
		return json_encode($res);
	}
	
	//------------ Delete function ------------//
	
	public function delete(Request $request){
		$res['status'] = '';
		
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$db = DB::table($data['table'])->where('id',$data['id'])->delete();
			if($db){
				$res['status'] = 'success';
				if(array_key_exists('image_unlink',$data) and $data['image_unlink'] == 'true'){
					unlink($data['image']);
				}
				
				if(array_key_exists('related_tables',$data) and !empty($data['related_tables'])){
					$db1 = DB::table($data['related_tables']['name'])->where($data['related_tables']['field_name'],$data['id'])->delete();
				}
			}
		}
		return json_encode($res);
	}
}
