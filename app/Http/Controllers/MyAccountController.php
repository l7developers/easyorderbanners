<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Ups;
use DB;
use App\User;
use App\UserAddress;
use App\UserArtwork;
use App\Menus;
use App\Orders;
use App\StaticPage;
use App\Sliders;
use App\Testimonials;
use App\Reviews;
use App\OrderProducts;
use App\State;
use App\UserCards;
use Hash;
use Exception;

// amazon s3 lib for upload file on amazon
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

class MyAccountController extends Controller
{
    public function __construct(){
        $this->middleware('auth',['except' => ['myArtWorkFilesUpload']]);
    }

	public function myaccount(Request $request){
		$pageTitle = 'My Account';
		$user = User::findOrFail(\Auth::user()->id);
		if($request->isMethod('post')){
			$data = $request->all();
			
			$validArr = [
                'fname' => 'required|string|max:255',
				'lname' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
				//'phone_number' => 'required|min:10|numeric',	
				'phone_number' => 'required|string',
            ];
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()) { 
				$user->fname = $data['fname'];
				$user->lname = $data['lname'];
				$user->email = $data['email'];
				$user->phone_number = $data['phone_number'];
				$user->company_name = $data['company_name'];
				
				if($user->save()){
					\Session::flash('success', 'Profile updated successfully.');
					return redirect('myaccount');
				}else{
					\Session::flash('error', 'Profile not updated.');
					return redirect('myaccount');
				}
			}else{
				\Session::flash('error', 'Profile not updated,Please try again.');
				return redirect('myaccount')->withErrors($validation)->withInput();
			}
		}
		return view('users/myaccount',compact('pageTitle','user'));
    }
	
	public function changePassword(Request $request){
		$pageTitle = 'Change Password';
		$user = User::findOrFail(\Auth::user()->id);
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr_password = [
                'OldPassword' => 'required',
				'NewPassword' => 'required|confirmed|min:6|max:50|different:OldPassword',
				'NewPassword_confirmation' => 'required|min:6|max:50'
            ];
						
			$validation = Validator::make($data, $validArr_password);
			if ($validation->passes()){
				if (Hash::check($data['OldPassword'], $user->password)) {
					
					$user->password = bcrypt($data['NewPassword']);
					
					if($user->save()){
						\Session::flash('success', 'Password changed successfull, Please login again. ');
					}
					else{
						\Session::flash('error', 'Password not changed, Please try again. ');
						return redirect('/change-password')->withInput();
					}
				}
				else{
					\Session::flash('error', 'old password not matched ,please enter correct old password. ');
					return redirect('/change-password')->withInput();
				}
			}
			else{
				return redirect('/change-password')->withErrors($validation)->withInput();
			}
		}
		return view('users/changepassword',compact('pageTitle','data'));
    }
	
	public function addresses(Request $request){
		$pageTitle = 'My Addresses';
		$data = UserAddress::getAddress(\Auth::user()->id);
		$states = State::where('status',1)->pluck('stateName','stateCode')->all();
		if($request->isMethod('post')){
			$params = $request->all();
			if(array_key_exists('billing',$params)){
				$validArr = [
					'billing.fname' => 'required|string|max:255',
					'billing.lname' => 'required|string|max:255',
					'billing.add1' => 'required',
					'billing.zipcode' => 'required',
					'billing.city' => 'required',
					'billing.state' => 'required',
					'billing.country' => 'required',
				];
				$messages = [
					'billing.fname.required' => 'this is required field.',
					'billing.lname.required' => 'this is required field.',
					'billing.add1.required' => 'this is required field.',
					'billing.zipcode.required' => 'this is required field.',
					'billing.city.required' => 'this is required field.',
					'billing.state.required' => 'this is required field.',
				];
			}
			else if(array_key_exists('shipping',$params)){
				$validArr = [
					'shipping.address_name' => 'required|string|max:255',
					'shipping.fname' => 'required|string|max:255',
					'shipping.lname' => 'required|string|max:255',
					'shipping.add1' => 'required',
					'shipping.zipcode' => 'required',
					'shipping.city' => 'required',
					'shipping.state' => 'required',
					'shipping.country' => 'required',
				];
				$messages = [
					'shipping.address_name.required' => 'this is required field.',
					'shipping.fname.required' => 'this is required field.',
					'shipping.lname.required' => 'this is required field.',
					'shipping.add1.required' => 'this is required field.',
					'shipping.zipcode.required' => 'this is required field.',
					'shipping.city.required' => 'this is required field.',
					'shipping.state.required' => 'this is required field.',
				];
			}
			
			//pr($params);die;
			$validation = Validator::make($params, $validArr,$messages);
			if ($validation->passes()) {
				if(array_key_exists('billing',$params)){
					UserAddress::where('user_id',Auth::user()->id)->where('type',1)->update(['status'=>0]);
					$user_address = new UserAddress();
					$user_address->user_id = Auth::user()->id;
					$user_address->type = 1;
					$user_address->company_name = $params['billing']['company_name'];
					$user_address->phone_number = $params['billing']['phone_number'];
					$user_address->fname = $params['billing']['fname'];
					$user_address->lname = $params['billing']['lname'];
					$user_address->add1 = $params['billing']['add1'];
					$user_address->add2 = $params['billing']['add2'];
					$user_address->zipcode = $params['billing']['zipcode'];
					$user_address->city = $params['billing']['city'];
					$user_address->state = $params['billing']['state'];
					$user_address->country = $params['billing']['country'];
					$user_address->save();
				}
				else if(array_key_exists('shipping',$params)){
					$user_address = new UserAddress();
					$user_address->user_id = Auth::user()->id;
					$user_address->type = 2;
					$user_address->address_name = $params['shipping']['address_name'];
					$user_address->company_name = $params['shipping']['company_name'];
					$user_address->phone_number = $params['shipping']['phone_number'];
					$user_address->fname = $params['shipping']['fname'];
					$user_address->lname = $params['shipping']['lname'];
					$user_address->add1 = $params['shipping']['add1'];
					$user_address->add2 = $params['shipping']['add2'];
					$user_address->ship_in_care = $params['shipping']['ship_in_care'];
					$user_address->zipcode = $params['shipping']['zipcode'];
					$user_address->city = $params['shipping']['city'];
					$user_address->state = $params['shipping']['state'];
					$user_address->country = $params['shipping']['country'];
					$user_address->save();
				}
				\Session::flash('success', 'Address added successfully.');
				return \Redirect::to('/addresses');
			}else{
				\Session::flash('error', 'Address not added successfully,Please try again.');
				return \Redirect::to('/addresses/')->withErrors($validation)->withInput();
			}
		}
		//pr($data);die;
		return view('users/addresses',compact('pageTitle','data','states'));
    }
	
	public function editAddress(Request $request,$id){
		$pageTitle = 'Edit Address';
		$data = UserAddress::findOrFail($id);
		$states = State::where('status',1)->pluck('stateName','stateCode')->all();
		//pr($data);die;
		if($request->isMethod('post')){
			$detail = $request->all();
			//pr($detail);die;
			$validArr = [
                'address_name' => 'required|string',
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'add1' => 'required',
                'zipcode' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
            ];
			
			$validation = Validator::make($detail, $validArr);
			if ($validation->passes()) { 				
				$data->address_name = $detail['address_name'];
				$data->company_name = $detail['company_name'];
				$data->phone_number = $detail['phone_number'];
				$data->fname = $detail['fname'];
				$data->lname = $detail['lname'];
				$data->add1 = $detail['add1'];
				$data->add2 = $detail['add2'];
				$data->zipcode = $detail['zipcode'];
				$data->city = $detail['city'];
				$data->state = $detail['state'];
				$data->country = $detail['country'];
				if($data->type==2)
				{
					//$data->address_name = $detail['address_name'];
					$data->ship_in_care = $detail['ship_in_care'];
				}
				if($data->save()){
					\Session::flash('success', 'Address edited successfully.');
					return \Redirect::to('/addresses');
				}else{
					\Session::flash('error', 'Address not edited.');
					return \Redirect::to('/edit/address/'.$id)->withInput();
				}
			}else{
				\Session::flash('error', 'Address not edited.');
				return \Redirect::to('/edit/address/'.$id)->withErrors($validation)->withInput();
			}
		}
		return view('users/editaddress',compact('pageTitle','data','states'));
    }
	
	public function deleteAddress(Request $request,$id){
		$address = UserAddress::where('id', $id)->delete();
		if($address){
			\Session::flash('success', 'Address deleted successfully.');
			return \Redirect::to('/addresses');
		}
		else{
			\Session::flash('error', 'Address Not Deleted, Please try again.');
			return \Redirect::to('/addresses');
		}
	}
	
	public function myOrders(){
		$pageTitle = 'Orders';
		
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		
		$db = Orders::where('orders.user_id',Auth::user()->id)->with('Products','orderProductOptions','files');
		
		$db->leftJoin('testimonials as testimonial','testimonial.order_id','=','orders.id');
		
		$db->select('orders.*','testimonial.id as order_review');
		
		$db->orderBy('orders.created_at','desc');
		
		$orders = $db->paginate($limit);
		
		return view('users/myorders',compact('pageTitle','orders','limit'));
    }
	
	public function orderReview(Request $request,$orderId){
		$pageTitle = "Order Review";
		
		$check = Testimonials::where('order_id',$orderId)->where('user_id',Auth::user()->id)->first();
		
		if(!empty($check)){
			\Session::flash('error', 'Review already submitted.');
			return redirect()->back();
		}
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr_password = [
                'name' => 'required',
                'designation_company' => 'required',
                'content' => 'required',
            ];			
			
			$validation = Validator::make($data, $validArr_password);
			if ($validation->passes()){
				$review = new Testimonials();
				$review->order_id = $data['order_id'];
				$review->user_id = Auth::user()->id; 
				$review->name = $data['name']; 
				$review->designation_company = $data['designation_company']; 
				$review->content = $data['content']; 
				$review->status = 0; 
				if($review->save()){
					\Session::flash('success', 'Thank you for your feedback.');
					return redirect('/orders');
				}
			}
			else{
				return redirect()->back()->withErrors($validation)->withInput();
			}
		}
		return view('users/order-review',compact('pageTitle','orderId'));
	}
	
	public function review(Request $request,$orderId,$productId){
		$pageTitle = "Review";
		
		$check = Reviews::where('order_id',$orderId)->where('product_id',$productId)->where('user_id',Auth::user()->id)->first();
		
		if(!empty($check)){
			\Session::flash('error', 'Review already submitted.');
			return redirect()->back();
		}
		if($request->isMethod('post')){
			$data = $request->all();
			//pr($data);die;
			$validArr_password = [
                'rating' => 'required',
                'comment' => 'required',
            ];			
			
			$validation = Validator::make($data, $validArr_password);
			if ($validation->passes()){
				$review = new Reviews();
				$review->order_id = $data['order_id']; 
				$review->product_id = $data['product_id']; 
				$review->user_id = Auth::user()->id; 
				$review->rating = $data['rating']; 
				$review->comment = $data['comment']; 
				$review->status = 0; 
				if($review->save()){
					\Session::flash('success', 'Thank you for your feedback.');
					return redirect('/orders');
				}
			}
			else{
				return redirect('/review/'.$orderId.'/'.$productId)->withErrors($validation)->withInput();
			}
		}
		return view('users/review',compact('pageTitle','orderId','productId'));
	}
	
	public function ViewOrder($id,$print=null){
		$pageTitle = 'Order View';
		$db = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress','files']);
		$order = $db->first();
		//pr($order->toArray());die;
		
		return view('users/orderview',compact('pageTitle','id','order','print'));
	}
	
	public function PrintOrder($id){
		$agents = User::select('id', DB::raw("concat(fname, ' ', lname) as name"))->where('role_id',2)->where('status', 1)->pluck('name','id')->all();
		
		$db = Orders::where('id', $id)->with(['customer','agent','orderProduct','orderProductOptions','orderAddress']);
		$order = $db->first();
		
		if($order->user_id != Auth::user()->id){
			 return redirect('/');
		}
		
		return view('users/orderprint',compact('order','agents'));
	}
	
	public function subscriber(Request $request){
		$responce['status'] = '';
		if($request->isMethod('post')){
			$data = $request->all();
			
			$validArr = [
                'email' => 'required|string|email|max:255'
            ];
			
			$validation = Validator::make($data, $validArr);
			if ($validation->passes()){ 
				if (!DB::table('subscribers')->where('email', '=', $data['email'])->exists()) {
					$myArray['email']= $data['email'];
					$myArray['created_at']= \Carbon\Carbon::now()->toDateTimeString();
					$myArray['updated_at']= \Carbon\Carbon::now()->toDateTimeString();
					$subscriber = DB::table('subscribers')->insert($myArray);
				}
				$responce['status'] = 'success';
				\Session::flash('success', 'Thank you for join with us.');
			}
			else{
				$responce['res'] = $validation->errors();
			}
		}
		return json_encode($responce);
	}
	
	public function activate($token=null){
		if ($user = User::where('token', '=', $token)->first()) {
			//pr($user);die;
			$token = time().mt_rand();
			User::where('id',$user->id)->update(['status'=>1,'token'=>$token]);
			\Session::flash('success', 'Now your account is activate, you can login your account.');
			return \Redirect::to('/login');
		}else{
			 abort(404);
		}
	}
	
	public function tracking($track_id){
		$pageTitle = 'Order Tracking';
		$tracking = new Ups\Tracking(config('constants.Ups_accessKey'), config('constants.Ups_userId'), config('constants.Ups_password'), config('constants.Ups_SandBox'));
		$order_product = OrderProducts::where('tracking_id',$track_id)->first();
		
		$track = array();
		$errorMessage="";
		$i = 1;
		try{
			$shipment = $tracking->track($track_id);
			//pr($shipment->Package->Activity);die;
			foreach($shipment->Package->Activity as $activity) {
				$data = (array)$activity;
				if(!empty((array) $data['ActivityLocation'])){
					if(isset($data['ActivityLocation']->Address)){
						$address = (array) $data['ActivityLocation']->Address;
						if(count($address) > 0){
							if(array_key_exists('City',$address))
							$track[$i]['address']['City'] = $address['City'];
							if(array_key_exists('StateProvinceCode',$address))
							$track[$i]['address']['StateProvinceCode'] = $address['StateProvinceCode'];
							if(array_key_exists('CountryCode',$address))
							$track[$i]['address']['CountryCode'] = $address['CountryCode'];
						}else{
							$track[$i]['address'] = '';
						}
					}
				}else{
					$track[$i]['address'] = '';
				}
				
				if(isset($data['Date'])){
					$track[$i]['date'] = date('d/m/Y',strtotime($data['Date']));
				}
				if(isset($data['Time'])){
					$track[$i]['time'] = date('H:i',strtotime($data['Time']));
				}
				if(isset($data['Status']->StatusType->Description)){
					$track[$i]['description'] = $data['Status']->StatusType->Description;
				}
				$i++; 
			}
			//pr($track);die;
		} catch (Exception $e) {
			//var_dump($e);
			//pr($e);die;
			$errorMessage = $e->getMessage();
			$track = array();
		}
			
		return view('users/tracking',compact('pageTitle','track','track_id','order_product','errorMessage'));
	}
	
	public function cards(Request $request){
		$pageTitle = 'My Cards';
		$data = UserCards::where('user_id',\Auth::user()->id)->get();
		$months = [
					'01' => 'January',
					'02' => 'February',
					'03' => 'March',
					'04' => 'April',
					'05' => 'May',
					'06' => 'June',
					'07' => 'July ',
					'08' => 'August',
					'09' => 'September',
					'10' => 'October',
					'11' => 'November',
					'12' => 'December',
				];
		//pr($data);
		return view('/users/cards',compact('pageTitle','data','months'));
	}
	
	public function cardEdit(Request $request,$id){
		$pageTitle = "Card Detail Edit";
		$data = UserCards::findOrFail($id);
		$months = [
					'01' => 'January',
					'02' => 'February',
					'03' => 'March',
					'04' => 'April',
					'05' => 'May',
					'06' => 'June',
					'07' => 'July ',
					'08' => 'August',
					'09' => 'September',
					'10' => 'October',
					'11' => 'November',
					'12' => 'December',
				];
		$years = array_combine(range(date('Y'), date('Y')+10), range(date('Y'), date('Y')+10));
		
		if($request->isMethod('post')){
			$detail = $request->all();
			//pr($detail);die;
			$validArr = [
				'expire_year' => 'required',
				'expire_month' => 'required',
			];
			$validation = Validator::make($detail, $validArr);
			if ($validation->passes()) { 
				$expiry_date = $detail['expire_year'].'-'.$detail['expire_month'];
				$data->expire_date = date('m-Y',strtotime($expiry_date));
				if($data->save()){
					\Session::flash('success', 'Card Detail edited successfully.');
					return \Redirect::to('/cards');
				}else{
					\Session::flash('error', 'Card Detail not edited,plese try again.');
					return \Redirect::to('/card/edit/'.$id)->withInput();
				}
			}else{
				\Session::flash('error', 'Card Detail not edited.please try again.');
				return \Redirect::to('/card/edit/'.$id)->withErrors($validation)->withInput();
			}
		}
		return view('/users/editcard',compact('pageTitle','data','months','years'));
	}
	
	public function deleteCard(Request $request,$id){
		$card = UserCards::where('id', $id)->delete();
		if($card){
			\Session::flash('success', 'Card Detail deleted successfully.');
			return \Redirect::to('/cards');
		}
		else{
			\Session::flash('error', 'Card Detail Not Deleted, Please try again.');
			return \Redirect::to('/cards');
		}
	}
	
	public function myArtWorkFiles(Request $request){
		$pageTitle = 'Uploads ArtWork Files';

		$artworkFiles = UserArtwork::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->get();

		return view('users/my_artwork_files',compact('pageTitle','id','order','artworkFiles'));			
	}

    /**
     * @param Request $request
     * @return string
     */
	public function myArtWorkFilesUpload(Request $request)
    {
		$res['status'] = false;
		if($request->isMethod('post')) {
			$data = $request->all();

			$validArr = [
				'project_name' => 'required',
				'comment' => 'required',
				'files' => 'required|array|min:1',
			];
			$message = [
                'files.mimes' => 'Only eps,jpg,jpeg,pdf,png and id images are allowed',
            ];

			$validation = Validator::make($data, $validArr ,$message);
			if ($validation->passes()) {
				$files_url = '';
				$file_count = 1;

                $credentials = new Credentials(config('constants.aws_key'), config('constants.aws_secret'));

				$s3 = new S3Client([
				    'version'     => 'latest',
				    'region'      => 'us-west-2',
				    'credentials' => $credentials
				]);

				$user_email = Auth::user()->email;
				
				foreach ($data['files'] as $key => $file) {
					$filename = $file->getClientOriginalName();
					$extension = $file->getClientOriginalExtension();
					$file_name = date('His').time() . $file_count . $filename;
					$file_count++;

					try {
					    $result = $s3->putObject([
					        'Bucket' => 'easybanneruploads',
					        'Key'    => $user_email.'/'.$filename,
					        'Body'   => fopen($file->getPathName(), 'r'),        
					        'ACL'    => 'public-read',
					    ]);					   
					    $files_url .= $result['ObjectURL'].'<br/>';					    

					} catch (Aws\S3\Exception\S3Exception $e) {
																    
					    \Session::flash('error', $e->getMessage());
					}

				}
				$files_url = trim($files_url,'<br/>');

				$userArtwork = new UserArtwork();
				$userArtwork->user_id = Auth::user()->id;
				$userArtwork->project_name = $data['project_name'];
				$userArtwork->comment = $data['comment'];
				$userArtwork->files_url = $files_url;				
				$userArtwork->save();

				$params = array('slug'=>'detailed_file_upload',
							'to'=>config('constants.ARTWORK_FILE_MAIL'),
							'cc'=>config('constants.store_email'),
							'params'=>array(
										'{{admin_name}}'=>config('constants.ADMIN_NAME'),
										'{{user_email}}'=>Auth::user()->email,
										'{{file_name}}'=>$file_name,
										'{{files_url}}'=>$files_url,
										'{{user_fname}}'=>Auth::user()->fname,
										'{{user_lname}}'=>Auth::user()->lname,
										'{{user_phone_number}}' => Auth::user()->phone_number,
										'{{project_name}}'=>$data['project_name'],
										//'{{address}}'=>$data['address'],
										'{{comment}}'=>$data['comment'],
								));
				parent::sendMail($params);
				$res['status'] = true;
					
				$res['success_msg'] = 'Files uploaded successfully.';
				
				\Session::flash('success', 'Files uploaded successfully.');
			}else{
				$res['error_messages'] = $validation->errors();
				$res['error_msg'] = 'File not uploaded,please try again.';
			}
		}else{
			$res['error_msg'] = 'Files not uploaded,please try again.';
		}
		return json_encode($res);
	}
	
	public function myArtWorkFilesDelete($id){

		$artworkFiles = UserArtwork::where('id',$id)->delete();
		\Session::flash('success', 'ArtWork file deleted successfully.');
		return \Redirect::to('/my-artwork-files');

		/*$credentials = new Credentials('AKIAJEFF5BAZ272XEWGQ', 'bm7VT4PnuDnyvjK48IR6PXdDItp8zxkRDts0GRLs');
		$s3 = new S3Client([
		    'version'     => 'latest',
		    'region'      => 'us-west-2',
		    'credentials' => $credentials
		]);
		$result = $s3->deleteObject([
       	 	'Bucket' => 'easybanneruploads',
        	'Key'    => 'easyorderbanner@mailinator.com/Screenshot (4).png'
    	]); */
	}	
	
	public function old_myArtWorkFilesUpload(Request $request){
		$pageTitle = 'Uploads ArtWork Files';
		if($request->isMethod('post')){
			$data = $request->all();
			$validArr = [
				'project_name' => 'required',
				'address' => 'required',
				'comment' => 'required',
				'files' => 'required|array|min:1',
				//'files' => 'required|mimes:eps,jpg,jpeg,pdf,id,png|array|min:1',
			];
			$messgae = [
					'files.mimes' => 'Only eps,jpg,jpeg,pdf,png and id images are allowed',
					];
			$validation = Validator::make($data, $validArr ,$messgae);
			if ($validation->passes()){
				$files_url = '';
				$file_count = 1;

				$credentials = new Credentials('AKIAIO7JHTMKZZ5GL5TQ', 'G9FAY3/X1CIS080f8wCMpXPA6Nn6z9jpEbC9l7uj');

				$s3 = new S3Client([
				    'version'     => 'latest',
				    'region'      => 'us-west-2',
				    'credentials' => $credentials
				]);
				$user_email = Auth::user()->email;			
				
				foreach ($data['files'] as $key => $file) {
					$filename = $file->getClientOriginalName();
					$extension = $file->getClientOriginalExtension();
					$file_name = date('His').time().$file_count.$filename;
					$file_count++;					

					//$destinationPath = public_path('/uploads/myartfiles/');
					//$file->move($destinationPath, $file_name);

					try {
					    $result = $s3->putObject([
					        'Bucket' => 'easybanneruploads',
					        'Key'    => $user_email.'/'.$filename,
					        'Body'   => fopen($file->getPathName(), 'r'),        
					        'ACL'    => 'public-read',
					    ]);					   
					    $files_url .= $result['ObjectURL'].'<br/>';					    

					} catch (Aws\S3\Exception\S3Exception $e) {
																    
					    \Session::flash('error', $e->getMessage());
					}

				}
				$files_url = trim($files_url,'<br/>');

				$params = array('slug'=>'detailed_file_upload',
							'to'=>config('constants.ADMIN_MAIL'),
							'params'=>array(
										'{{admin_name}}'=>config('constants.ADMIN_NAME'),
										'{{user_email}}'=>Auth::user()->email,
										'{{file_name}}'=>$file_name,
										'{{files_url}}'=>$files_url,
										'{{user_fname}}'=>Auth::user()->fname,
										'{{user_lname}}'=>Auth::user()->lname,
										'{{user_phone_number}}'=>Auth::user()->phone_number,
										'{{project_name}}'=>$data['project_name'],
										'{{address}}'=>$data['address'],
										'{{comment}}'=>$data['comment'],
								));
				parent::sendMail($params);
				\Session::flash('success', 'Files uploaded successfully.');
				return \Redirect::to('/my-artwork-files');
			}else{
				\Session::flash('error', 'Files not uploaded, please try again.');
				return \Redirect::to('/my-artwork-files')->withErrors($validation)->withInput();
			}
		}

		return view('users/my_artwork_files',compact('pageTitle','id','order'));			
	}
	
	public function email(){
		$user = new User();
		$user->fname = "Jitendra";
		$user->lname = "Dariwal";
		
		//sendMail('slug',$to,$params);
		$to = 'testsoon@mailinator.com';
		\Mail::send(['html' => 'emails.registerMail'], ['data' => $user], function ($message) use ($to) {
			$message->from('admin@admin.com', 'Apparel Resources Jobs');
			$message->to($to)->subject('Registration successfully');
		});
	}
	
}
