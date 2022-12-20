<?php

namespace App ;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use Session ;
use Exception ;

use App\Models\Tokens;
use App\Models\Logs;

class CareApi
{

    public $endpoint = 'https://api.camping.care/v21' ; 
    
    public function get($path, $params = [])
    {

        return $this->response(Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getIdToken(),
            'X-Admin-Id' => Session::get('adminId'),
            'Content-Type' => 'application/json',
        ])->get($this->endpoint.$path, $params), $params) ;

    }

    public function put($path, $params = [])
    {

        return $this->response(Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getIdToken(),
            'X-Admin-Id' => Session::get('adminId'),
            'Content-Type' => 'application/json',
        ])->put($this->endpoint.$path, $params), $params) ;

    }

    public function post($path, $params = [])
    {

        return $this->response(Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getIdToken(),
            'X-Admin-Id' => Session::get('adminId'),
            'Content-Type' => 'application/json',
        ])->post($this->endpoint.$path, $params), $params) ;

    }

    public function delete($path, $params = [])
    {

        return $this->response(Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getIdToken(),
            'X-Admin-Id' => Session::get('adminId'),
            'Content-Type' => 'application/json',
        ])->delete($this->endpoint.$path, $params), $params) ;

    }
    
    public function getIdToken($authtoken = false, $force = false)
    {

        $this->getTokens($authtoken, $force) ;
        
        return Session::get('idToken') ; 
        
    }

    public function getRefreshToken($authtoken = false, $force = false)
    {
        
        $this->getTokens($authtoken, $force) ;
        
        return Session::get('refreshToken') ; 
        
    }

    public function getTokens($authtoken = false, $force = false)
    {   
        
        if(
            $force == false &&
            Session::has('idToken') &&
            time() < Session::get('expiresIn') &&
            Session::has('refreshToken') 
        ){
            return true ;
        }        

        if(time() < Session::get('expiresIn')){

            if(!Session::has('refreshToken') ){
                throw new Exception("No refreshToken found", 1);
            }
            
            $response = Http::post($this->endpoint.'/oauth/refresh_token', [
                'refresh_token' => Session::get('refreshToken') 
            ]) ;
    
            $tokens = $response->object() ; 

            $tokens->idToken = $tokens->id_token ;
            $tokens->refreshToken = $tokens->refresh_token ;
            $tokens->expiresIn = $tokens->expires_in ;

        }else{

            if($authtoken == false){
                throw new Exception("No authtoken found 1", 1);
            }
    
            $response = Http::post($this->endpoint.'/oauth/token', [
                'auth_token' => $authtoken
            ]) ;
    
            $tokens = $response->object() ; 
            
        }

        Session::put('idToken', $tokens->idToken) ;
        Session::put('refreshToken', $tokens->refreshToken) ;
        Session::put('expiresIn', time() + ($tokens->expiresIn - 10)) ; // minus 10 sec margin
        
        // also store the userId and Email address
        $tokenParts = explode(".", $tokens->idToken) ;  
        $tokenPayload = base64_decode($tokenParts[1]) ;

        $userData = json_decode($tokenPayload) ;

        Session::put('userUid', $userData->user_id) ;
        Session::put('email', $userData->email) ;
        Session::put('name', $userData->name) ;

        $careTokens = Tokens::where('admin_id', Session::get('adminId'))->first();

        if(!$careTokens){
            $careTokens = new Tokens ;
        }

        $careTokens->admin_id = Session::get('adminId') ;
        $careTokens->user_uid = Session::get('userUid') ;
        $careTokens->id_token = Session::get('idToken') ;
        $careTokens->refresh_token = Session::get('refreshToken') ;
        $careTokens->expires_in = Session::get('expiresIn') ;

        $careTokens->save() ;

        return true ;
        
    }

    public function setTokens($adminId)
    {   
        
        $tokens = Tokens::where('admin_id', $adminId)->first();

        Session::put('idToken', $tokens->id_token) ;
        Session::put('refreshToken', $tokens->refresh_token) ;
        Session::put('expiresIn', $tokens->expires_in) ; 
        Session::put('userUid', $tokens->user_uid) ;

        return true ;
        
    }

    public function response($response, $params = [])
    {
        
        if($response->getStatusCode() != 200){

            $log = new Logs;
            $log->description = 'CareApi error: ('.$response->getStatusCode().')' ;
            $log->admin_id = $molliePayment->admin_id ;
            $log->request = json_encode($params) ;
            $log->response = json_encode($response->json()) ;
            $log->save() ;

        };

        return $response ; 
        
    }

}
