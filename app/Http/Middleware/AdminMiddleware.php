<?php
namespace App\Http\Middleware;
use Closure;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;

class AdminMiddleware
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
	
    public function handle($request, Closure $next, ...$guards)
    {
		//echo "hello";die;
		if(isset(\Auth::user()->id) and (\Auth::user()->role_id==1 OR \Auth::user()->role_id==2))
        { 
            $this->authenticate($guards);

			/* $currentAction = \Route::currentRouteAction();		
			list($controller, $action) = explode('@', $currentAction);
			$controller = preg_replace('/.*\\\/', '', $controller);
			
			if($controller != 'OrderPOController' or ($controller == 'OrderPOController' and !in_array($action,['po_mail','create_pdf']))){
				session()->forget('po_detail');
			} */
			
			return $next($request);
        }
		else{
			\Auth::logout();
			\Session::flash('error', 'Log In failed, please try again or try resetting password.');
			return redirect('/admin/login');
			//return redirect('/admin/');
		}
    }
	
	
    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate(array $guards)
    {
        if (empty($guards)) {
            return $this->auth->authenticate();
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        throw new AuthenticationException('Unauthenticated.', $guards);
    }
}