<?php

use Illuminate\Support\Facades\Route;
use App\CareApi;
use App\Models\Logs;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['care.app'])->group(function () {

    Route::get('/', function () {

        if(Session::get('installed')){

            $api = new CareApi() ;

            $result = $api->get('/users/me', ['get_rights' => true]) ;

            view()->share('user', $result->object()) ;

            return view('installed') ;

        }
        
        return view('welcome') ;

    })  ; 

    Route::get('/widgets/reservation', function () {

        return view('/widgets/reservation') ;

    })  ; 

    Route::get('/settings', function () {

        return view('/settings') ;

    })  ; 

    Route::get('/logs', function (Request $request) {
        
        $logs = 'no logs' ; 

        if(Session::has('adminId')){

            if($request->input('action') == 'clear'){
                Logs::where('admin_id', Session::get('adminId'))->delete();
            }

            $logs = Logs::where('admin_id', Session::get('adminId'))
               ->orderBy('id', 'desc')
               ->take(10)
               ->get();

        }

        view()->share('logs', $logs) ;

        return view('/logs') ;

    })  ; 

    

}) ;