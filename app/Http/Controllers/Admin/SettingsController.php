<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Setting;


class SettingsController extends Controller
{
    public function index(Request $request){
    	
		$pageTitle = "Site Settings";
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			/*$validArr = [
                'name' => 'required',
                'content' => 'required',
                'designation_company' => 'required'
            ];*/

            $validArr = [];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) {

				foreach($data as $key=>$val)
				{
					if($key =='_token')
						continue;

					$setting=Setting::where('name', $key)->first(); 
					$setting->value = $val;
					$setting->save();
				}			
				
				\Session::flash('success', 'Settings Saved successfully.');
				return redirect('/admin/settings');				
			}
			else{
				\Session::flash('error', 'Settings not saved.');
				return redirect('/admin/settings')->withErrors($validation)->withInput();
        	}
		}

		$settings=Setting::pluck('value','name')->toArray();
		
		return view('Admin.setting.index',compact('pageTitle','settings'));
	}
	
	
}
