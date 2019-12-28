<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/myaccount';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	 
	private $remember = '';
	
    public function __construct()
    {
		$this->redirectTo = url()->previous();
		$this->middleware('guest')->except('logout');
		$this->remember = unserialize(Cookie::get('remember'));
    }
	
	public function showLoginForm()
    {
		return view('Auth.login',['remember'=>$this->remember]);
    }
	
	public function login(Request $request)
    {
        $this->validateLogin($request);
		$cookie_name = "remember";
		$cookie_value = $request->all();
		if($request->get('remember')){
			//dd(Cookie('name','jitendra',true,150000));
			setcookie($cookie_name, serialize($cookie_value), time() + (86400 * 30), "/");
		}else{
			setcookie($cookie_name, serialize($cookie_value), time() - (86400 * 30), "/");
		}
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
			return $this->sendLoginResponse($request);
        }
		
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
	
    protected function authenticated($request, $user) {    	
		if ($user->status != 1 and ($user->role_id == 3 or $user->role_id == 1 or $user->role_id == 4)){
			\Session::flash('error', 'Your account is not activated, please activate your account from your email firstly.');
			\Auth::logout();
			$this->redirectTo = '/login';
		}
		else if ($user->role_id != 3 and $user->role_id != 1 and $user->role_id != 4){
			\Session::flash('error', 'Log In failed, please try again or try resetting password.');
			\Auth::logout();
			$this->redirectTo = '/login';
		}
    }
}
