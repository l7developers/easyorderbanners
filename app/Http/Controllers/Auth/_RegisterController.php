<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/thank';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
	
	public function showRegistrationForm()
    {
        return view('Auth.register');
    }
	
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            //'phone_number' => 'required|min:10|numeric',
			'phone_number' => 'required|string',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */

    protected function create(array $data)
    {
		$token = time().mt_rand();
		
		$user = new User();
		$user->fname = $data['fname'];
		$user->lname = $data['lname'];
		$user->email = $data['email'];
		$user->phone_number = $data['phone_number'];
		$user->company_name = $data['company_name'];
		if($data['company_name']=="")
		{
			$user->company_name = $user->fname.' '.$user->lname; 
		}
		$user->token = $token;
		$user->password = bcrypt($data['password']);
		$user->status = 1 ;

		$user->save();
			
		$params = array(
						'slug'=>'user_register',
						'to'=>$user->email,
						'cc'=>config('constants.store_email'),
						'params'=>array(
									'{{name}}'=>$data['fname'].' '.$data['lname'],									
									'{{email}}'=>$data['email'],									
									'{{password}}'=>$data['password'],									
									'{{SITE_URL}}'=>config('constants.SITE_URL'),
									'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
									'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
									)
						);
			
		parent::sendMail($params);			
    }
    /*protected function create(array $data)
    {
		$token = time().mt_rand();
		
		$user = new User();
		$user->fname = $data['fname'];
		$user->lname = $data['lname'];
		$user->email = $data['email'];
		$user->phone_number = $data['phone_number'];
		$user->token = $token;
		$user->password = bcrypt($data['password']);
					
		$user->save();
			
		$params = array(
						'slug'=>'user_register',
						'to'=>$user->email,
						'params'=>array(
									'{{name}}'=>$data['fname'].' '.$data['lname'],
									'{{LINK}}'=> url('activate/'.$token),
									'{{SITE_URL}}'=>config('constants.SITE_URL'),
									'{{ADMIN_MAIL}}'=>config('constants.ADMIN_MAIL'),
									'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
									)
						);
			
		parent::sendMail($params);
		
		$params = array(
						'slug'=>'registeration_mail_to_admin',
						'to'=>config('constants.ADMIN_MAIL'),
						'params'=>array(
									'{{NAME}}'=>$data['fname'].' '.$data['lname'],
									'{{EMAIL}}'=>$data['email'],
									'{{PHONE_NUMBER}}'=>$data['phone_number'],
									'{{SITE_NAME}}'=>config('constants.SITE_NAME'),
									'{{ADMIN_NAME}}'=>config('constants.ADMIN_NAME'),
									)
						);
		parent::sendMail($params);
    }*/
	
	public function register(Request $request)
    {
		$this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));
		
        //$this->guard()->login($user);
		return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }
}
