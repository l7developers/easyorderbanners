<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;

class AdminLoginMiddleware
{
    /**
     * Handle an incoming request. User must be logged in to do admin check
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if(!empty(\Auth::user())){
			if(\Auth::user()->user_type==1)
			{
				//pr(\Auth::user()->user_type);die;
				//return $next($request);
				return redirect('/admin/dashboard');
			}
			else if(\Auth::user()->user_type==2)
			{
				//pr(\Auth::user()->user_type);die;
				//return $next($request);
				//return redirect('/');
				return redirect('/admin/dashboard');
			}
		}
		else{
			$currentAction = \Route::currentRouteAction();
			$asd=explode('@', $currentAction);
			if($asd[1]=='login'){
				//pr($asd);die("login");
				//return redirect('/');
			}
			else{
				//return redirect('/');
			}
		}
    }
}