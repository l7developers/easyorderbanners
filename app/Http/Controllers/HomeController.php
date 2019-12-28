<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\StoreCustomQuoteRequest;
use App\Http\Requests\StoreVolumeRequest;
use App\Http\Requests\StoreOverSizeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use App\User;
use App\UserAddress;
use App\Menus;
use App\Orders;
use App\StaticPage;
use App\Sliders;
use App\Testimonials;
use App\Products;
use App\HomePages;
use App\CustomerLogos;
use App\HomeImages;

use \App\Helpers\UPSShipping;

class HomeController extends Controller
{
    public function __construct()
    {
        /*$this->middleware('auth',['except' => ['index','contactus','contactus_save','customeQuote','customeQuote_save','volumeDiscount','volume_discount_save','oversized_banner','oversized_banner_save','subscriber','activate']]); */
    }

    public function index(){
		$pages = StaticPage::whereIn('slug',['custom-banners--sign-printing-made-easy','welcome-to-easy-order-banners','why-choose-easyorderbannerscom'])->get()->toArray();
		
		$sliders = Sliders::where('status',1)->get();
		$testimonials = Testimonials::where('status',1)->get();
		
		$customer_logos = CustomerLogos::where('status',1)->get();
		
		$home_array = array();
		$home_data = HomePages::with(['images'])->get();
		foreach($home_data as $data){
			if($data->product_id != null){
				$products = Products::where('status',1)->whereIn('id',explode(',',$data->product_id))->select('id','name','price','image','slug','cat_image','cat_image_title','image_title','show_width_height')->get();
				$data->product_detail = $products;
			}
			$home_array[$data->type] = $data;
		}
		//pr($home_array);die;
		//pr($customer_logos);die;
		return view('home/index',compact('pages','testimonials','sliders','home_array','customer_logos'));
    }
	
	public function contactus(){
		$pageTitle = 'Contact Us';		
		return view('home/contactus',compact('pageTitle'));
	}
	
	public function contactus_save(StoreContactRequest $request){
		$data = $request->all();
		$params = array(
						'slug'=>'contact_us',
						'to'=>config('constants.store_email'),
						'params'=>array(
									'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
									'{{NAME}}'=>$data['name'],
									'{{EMAIL}}'=>$data['email'],
									'{{SUBJECT}}'=>$data['subject'],
									'{{MESSAGE}}'=>$data['message'],
									)
						);
		parent::sendMail($params);
		\Session::flash('success', 'Thank you for contact with us.');
		return view('home/contactus',compact('pageTitle'));
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
	
	public function customeQuote(){
		$pageTitle = "Custom Quote Form";
		return view('home/custom_quote',compact('pageTitle'));
	}
	
	public function volumeDiscount(){
		$pageTitle = "Volume Discount Form";
		return view('home/volume_discount',compact('pageTitle'));
	}
	
	public function customeQuote_save(StoreCustomQuoteRequest $request){
		$pageTitle = "Custom Quote and Volume Discount Form";
		$data = $request->all();		
		$params = array(
						'slug'=>'custom_quotes',
						'to'=>config('constants.store_email'),
						'params'=>array(
									'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
									'{{name}}'=>$data['custom']['fname'].' '.$data['custom']['lname'],
									'{{email}}'=>$data['custom']['email'],
									'{{company}}'=>$data['custom']['company'],
									'{{address}}'=>$data['custom']['address'],
									'{{city}}'=>$data['custom']['city'],
									'{{state}}'=>$data['custom']['state'],
									'{{zipcode}}'=>$data['custom']['zipcode'],
									'{{country}}'=>$data['custom']['country'],
									'{{phone}}'=>$data['custom']['phone'],
									'{{fax}}'=>$data['custom']['fax'],
									'{{quantity}}'=>$data['custom']['quantity'],
									'{{material_type}}'=>$data['custom']['material_type'],
									'{{size}}'=>$data['custom']['size'],
									'{{due_date}}'=>date('d M Y',strtotime($data['custom']['due_date'])),
									'{{detail}}'=>$data['custom']['detail'],
									)
						);
		
		parent::sendMail($params);
		\Session::flash('success', 'Thank you for contact with us.');
		return redirect('/custom_quotes_&_volume_discounts');
	}
	
	public function volume_discount_save(StoreVolumeRequest $request){
		$pageTitle = "Custom Quote and Volume Discount Form";
		$data = $request->all();		
		$params = array(
						'slug'=>'volume_quote',
						'to'=>config('constants.store_email'),
						'params'=>array(
									'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
									'{{name}}'=>$data['volume']['fname'].' '.$data['volume']['lname'],
									'{{email}}'=>$data['volume']['email'],
									'{{phone}}'=>$data['volume']['phone'],
									'{{organization_name}}'=>$data['volume']['organization_name'],
									'{{amount_sqft_per_month}}'=>$data['volume']['amount_sqft_per_month'],
									'{{comment}}'=>$data['volume']['comment'],
									)
						);
		parent::sendMail($params);
		\Session::flash('success', 'Thank you for contact with us.');
		return redirect('/custom_quotes_&_volume_discounts');
	}
	
	public function oversized_banner(){
		$pageTitle = "Oversized Banner form";
		return view('home/oversize_banner',compact('pageTitle'));
	}
	
	
	public function oversized_banner_save(StoreOverSizeRequest $request){
		$pageTitle = "Oversized Banner form";
		$data = $request->all();
		
		$params = array(
						'slug'=>'oversize_quotes',
						'to'=>config('constants.store_email'),
						'params'=>array(
									'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
									'{{name}}'=>$data['oversize']['fname'].' '.$data['oversize']['lname'],
									'{{email}}'=>$data['oversize']['email'],
									'{{company}}'=>$data['oversize']['company'],
									'{{address}}'=>$data['oversize']['address'],
									'{{city}}'=>$data['oversize']['city'],
									'{{state}}'=>$data['oversize']['state'],
									'{{zipcode}}'=>$data['oversize']['zipcode'],
									'{{country}}'=>$data['oversize']['country'],
									'{{phone}}'=>$data['oversize']['phone'],
									'{{fax}}'=>$data['oversize']['fax'],
									'{{quantity}}'=>$data['oversize']['quantity'],
									'{{material_type}}'=>$data['oversize']['material_type'],
									'{{size}}'=>$data['oversize']['size'],
									'{{due_date}}'=>date('d M Y',strtotime($data['oversize']['due_date'])),
									'{{detail}}'=>$data['oversize']['detail'],
									)
						);
		parent::sendMail($params);
		\Session::flash('success', 'Thank you for contact with us.');
		return redirect('/oversized-banner');
	}
	
	public function testimonials(Request $request){
		$pageTitle = "Testimonials";
		$limit = config('constants.ADMIN_PAGE_LIMIT');
		$testimonials = Testimonials::where('status',1)->orderBy('created_at','desc')->paginate($limit);
		return view('home/testimonials',compact('pageTitle','testimonials'));
	}
	
	public function email()
	{
		$user = new User();
		$user->fname = "Jitendra";
		$user->lname = "Dariwal";		

		//sendMail('slug',$to,$params);
		$to = 'testsoon@mailinator.com';
		$to = 'anil.kumar@octalsoftware.net';
		\Mail::send(['html' => 'emails.registerMail'], ['data' => $user], function ($message) use ($to) {
			$message->from('info@easyorderbanners.com', 'Apparel Resources Jobs');
			$message->to($to)->subject('Registration successfully');
		});
		
		die('Email Sent Successfully');
	}

	public function loginByEmail($email)
	{	
		$user = User::where('email', $email)->first();
		if ($user != null)
		{
		    Auth::loginUsingId($user->id);
		    return redirect('/myaccount');
		}
	}
}
