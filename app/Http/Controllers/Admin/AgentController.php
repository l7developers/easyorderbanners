<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;


class AgentController extends Controller
{
    public function add(Request $request){
		$pageTitle = "Agent Add";
		$roles = [
					'1' => 'Admin',
					'2' => 'Agent',
					'4' => 'Web User',
				];
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);
			$validArr = [
                'role_id' => 'required',
                'fname' => 'required|string|max:255',
				'lname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:users',
				'password' => 'required|string|min:6|confirmed',
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$user = new User();
				$user->role_id = $data['role_id'];
				$user->fname = $data['fname'];
				$user->lname = $data['lname'];
				$user->email = $data['email'];
				$user->extension = $data['extension'];
				$user->direct = $data['direct'];
				$user->mobile = $data['mobile'];
				$user->status = 1;
				$user->password = bcrypt($data['password']);
				
				if($user->save()){
					$params = array(
									'slug'=>'admin_agent_register',
									'to'=>$data['email'],
									'params'=>array(
												'{{name}}'=>$data['fname'].' '.$data['lname'],
												'{{EMAIL}}'=>$data['email'],
												'{{PASSWORD}}'=>$data['password'],
												'{{SITE_URL}}'=>config('constants.SITE_URL'),
												'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
												'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
												'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
												)
									);
					parent::sendMail($params);
					\Session::flash('success', 'Agent added complete.');
					return redirect('/admin/agents/lists');
				}
				else{
					\Session::flash('error', 'Agent not added.');
					return redirect('/admin/agents/add');
				}
			}
			else{
				\Session::flash('error', 'Agent not added.');
				return redirect('/admin/agents/add')->withErrors($validation)->withInput();
        	}
		}
		return view('Admin/agents/add',compact('pageTitle','roles'));
	}
	
	public function lists(Request $request,$field=null,$sort='ASC'){
		$pageTitle = "Agents List";
		$data = $request->all();
		
		/* if(!$request->isMethod('post') and !isset($data['page'])){
			session()->forget('agnets');
		} */
		
		if(isset($data['rs']) && $data['rs']==1){
			session()->forget('agnets');
		}
		
		$roles = [
					'1' => 'Admin',
					'2' => 'Agent',
					'4' => 'Web User',
				];
		$orcondition = array();
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		//\DB::enableQueryLog();
		
		$db=User::whereIn('role_id',[2,4]);
		
		if($request->isMethod('post')){
			if(isset($data['fname']) and !empty($data['fname'])){
				session(['agnets.fname' => $data['fname']]);
			}else{
				session()->forget('agnets.fname');
			}
			if(isset($data['lname']) and !empty($data['lname'])){
				session(['agnets.lname' => $data['lname']]);
			}else{
				session()->forget('agnets.lname');
			}
			if(isset($data['email']) and !empty($data['email'])){
				session(['agnets.email' => $data['email']]);
			}
			if(isset($data['status']) and $data['status'] != ''){
				session(['agnets.status' => $data['status']]);
			}
		}
		
		if (session()->has('agnets')) {
			/* if (session()->has('agnets.name')) {
				$name = session()->get('agnets.name');
				$db->where(function ($q) use($orcondition,$request,$name) {
					$q->orWhere('fname','like','%'.$name.'%');
					$q->orWhere('lname','like','%'.$name.'%');
				});
			} */
			if (session()->has('agnets.fname')) {
				$fname = session()->get('agnets.fname');
				$db->where('fname','like','%'.$fname.'%');
			}
			if (session()->has('agnets.lname')) {
				$lname = session()->get('agnets.lname');
				$db->where('lname','like','%'.$lname.'%');
			}
			if (session()->has('agnets.email')) {
				$email = session()->get('agnets.email');
				$db->where('email','like','%'.$email.'%');
			}
			if (session()->has('agnets.status')) {
				$status = session()->get('agnets.status');
				$db->where('status',$status);
			}
		}
		if($field != null){
			$db->orderBy($field,$sort);
		}else{
			$db->orderBy('created_at','desc');
		}
		
		$agents = $db->paginate($limit);
		
		return view('Admin/agents/lists',compact('pageTitle','limit','agents','data','field','sort','roles'));
	}
	
	public function edit(Request $request,$id){
		$pageTitle = "Agent Edit";
		
		$roles = [
					'1' => 'Admin',
					'2' => 'Agent',
					'4' => 'Web User',
				];
		
		$user = User::findOrFail($id);
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr = [
                'role_id' => 'required',
                'fname' => 'required|string|max:255',
				'lname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            ];
			if(!empty($data['password'])){
				$validArr = ['password' => 'string|min:6|confirmed'];
			}
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$user->role_id = $data['role_id'];
				$user->fname = $data['fname'];
				$user->lname = $data['lname'];
				$user->email = $data['email'];
				$user->extension = $data['extension'];
				$user->direct = $data['direct'];
				$user->mobile = $data['mobile'];
				if(!empty($data['password'])){
					$user->password = bcrypt($data['password']);
				}
				
				if($user->save()){
					\Session::flash('success', 'Agent Edit.');
					return redirect('/admin/agents/lists');
				}
				else{
					\Session::flash('error', 'Agent not edit.');
					return redirect('/admin/agents/edit/'.$id);
				}
			}
			else{
				\Session::flash('error', 'Agent not Edit.');
				return redirect('/admin/agents/edit/'.$id)->withErrors($validation)->withInput();
        	}
		}
		
		return view('Admin/agents/edit',compact('pageTitle','user','id','roles'));
	}
	
	public function view($id){
		$pageTitle = "Agent View";
		$user=User::where('id', $id)->get()->toArray();
		//pr($user);
		return view('Admin/agents/view',compact('pageTitle','user'));
	}
	
	public function delete_agent($id){
		$agent=User::where('id', $id)->delete();
		if($agent){
			\Session::flash('success', 'Agent deleted successfully.');
			return \Redirect::to('/admin/agents/lists');
		}
		else{
			\Session::flash('error', 'Agent not deleted.');
			return \Redirect::to('/admin/agents/lists');
		}
	}
	
	public function action($id,$status){
		$agent=User::where('id', $id)->update(['status'=>$status]);
		if($agent){
			\Session::flash('success', 'Agent status updated successfully.');
			return \Redirect::to('/admin/agents/lists');
		}
		else{
			\Session::flash('error', 'Agent status not updated.');
			return \Redirect::to('/admin/agents/lists');
		}
	}
}
