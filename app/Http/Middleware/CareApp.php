<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\CareInstall;
use App\CareUninstall;
use App\CareApi;

use Session;

class CareApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        
        // First check if the app runs into the Care platform
        // The HTTP_REFERER should always be one of our platform
        // allow request outside the platform if debug is on (Like a local env.)
        $refererHost = false ;

        $url = parse_url($request->server('HTTP_REFERER')) ;

        if(is_array($url) && isset($url['host'])){
            $refererHost = $url['host'] ;
        }

        if(
            config('app.debug') === false && 
            $refererHost != $request->host() &&
            $refererHost != 'app.camping.care' &&
            $refererHost != 'app.hotel.care' &&
            $refererHost != 'app.bungalow.care'
        ){
            return response()->view('errors/not-in-platform');
        }

        // Set all the default settings
        $installed = false ;
        $idToken = false ;
        $api = new CareApi();

        $adminId = false ;

        if($request->input('admin_id')){

            if(Session::has('adminId') && $request->input('admin_id') != Session::get('adminId')){
                Session::flush();
            }

            Session::put('adminId', $request->input('admin_id')) ;
            $adminId = $request->input('admin_id') ;
            
        }else if(Session::has('adminId')){
            $adminId = Session::get('adminId') ;
        }

        if($request->input('authtoken')){
            $api->getTokens($request->input('authtoken'), true) ;
        }
        
        if(Session::has('idToken')){

            $idToken = Session::get('idToken') ;
            $installed = true ;

        }

        $chainId = false ;

        if($request->input('chain_id')){
            Session::put('chainId', $request->input('chain_id')) ;
            $chainId = $request->input('chain_id') ;
        }else if(Session::has('chainId')){
            $chainId = Session::get('chainId') ;
        }

        $appId = false ;

        if($request->input('app_id')){
            Session::put('appId', $request->input('app_id')) ;
            $appId = $request->input('app_id') ;
        }else if(Session::has('appId')){
            $appId = Session::get('appId') ;
        }

        $lang = false ;

        if($request->input('lang')){
            Session::put('lang', $request->input('lang')) ;
            $lang = $request->input('lang') ;
        }else if(Session::has('lang')){
            $lang = Session::get('lang') ;
        }

        $action = $request->input('action') ;

        // check if we need to install the app
        if($request->input('action') == 'install'){
            CareInstall::run();
        }

        view()->share('action', $action) ;
        view()->share('installed', $installed) ;
        view()->share('adminId', $adminId) ;
        view()->share('chainId', $chainId) ;

        view()->share('appId', $appId) ;
        view()->share('lang', $lang) ;
        
        Session::put('installed', $installed) ;

        if($installed){
            view()->share('userUid', Session::get('userUid')) ;
            view()->share('email', Session::get('email')) ;
            view()->share('name', Session::get('name')) ;
        }

        if($request->input('action') == 'uninstall'){
            CareUninstall::run();
        }

        return $next($request) ;

    }

}
