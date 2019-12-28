<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Route;

class ActiveMiddleware
{
    /**
     * Handle an incoming request. User must be logged in to do admin check
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
		/* preg_match('/([a-z]*)@/i', $request->route()->getActionName(), $matches);
		$controllerName = $matches[1];
		
		$currentRoute = \Route::currentRouteAction();
		$currentAction=explode('@', $currentRoute);
		$actionName=$currentAction[1];
		
		define('CURRENT_CONTROLLER',$controllerName);
		define('CURRENT_ACTION',$actionName); */
    }
}