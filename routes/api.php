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

Route::post('/webhooks', function (Request $request) {

    $response = 'ok'; 
    $data = $request->input('data') ;

    // $data['id']

    $log = new Logs;

    $log->description = $request->input('event') .' -- '. $request->input('admin_id') .' -- '. $request->input('key') ;
    $log->admin_id = $request->input('admin_id') ;
    $log->request =  json_encode($data) ;
    $log->response = json_encode($response);

    $log->save();
    
    return response()->json($response) ;

})  ; 