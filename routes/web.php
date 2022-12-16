<?php

use Illuminate\Support\Facades\Route;
use App\CareApi;

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

Route::get('/', function () {

    if(Session::get('installed')){

        $api = new CareApi() ;

        $result = $api->get('/users/me', ['get_rights' => true]) ;

        view()->share('user', $result->object()) ;

        return view('installed') ;

    }
    
    return view('welcome') ;

})->middleware('care.app') ; 