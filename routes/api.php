<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Logs;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/webhooks/contact', function (Request $request) {

    $response = 'ok'; 

    $log = new Logs;

    $log->description = 'Webhooks contact' ;
    $log->admin_id =  77 ;
    $log->request =  json_encode('request') ;
    $log->response = json_encode($response);

    $log->save();
    
    return response()->json($response) ;

})  ; 