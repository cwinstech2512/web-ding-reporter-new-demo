<?php
namespace App\Http\Middleware;
   
use Closure;
   
class FrontWebMiddleware
{
    
    // public $restrictIps = env('FRONT_WEB_IP', '');
        
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $restrictIps = explode(',', env('FRONT_WEB_IP', ''));
        // dd($request->ip());
        if (!in_array($request->ip(), $restrictIps)) {
    
            return response()->json(["you don't have permission to access this application."]);
        }
    
        return $next($request);
    }
}