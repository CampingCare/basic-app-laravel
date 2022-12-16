<?php

namespace App ;
use Illuminate\Support\Facades\Http;
use Session ;
use Exception ;

use Illuminate\Support\Facades\DB;

class CareApi
{

    public $endpoint = 'https://api.staging.camping.care/v21' ; 
    
    public function get($path, $params)
    {

        return Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getIdToken(),
            'X-Admin-Id' => Session::get('adminId'),
            'Content-Type' => 'application/json',
        ])->get($this->endpoint.$path, $params) ;

    }

    public function put($path, $params)
    {

        return Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getIdToken(),
            'X-Admin-Id' => Session::get('adminId'),
            'Content-Type' => 'application/json',
        ])->put($this->endpoint.$path, $params) ;

    }

    public function post($path, $params)
    {

        return Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getIdToken(),
            'X-Admin-Id' => Session::get('adminId'),
            'Content-Type' => 'application/json',
        ])->post($this->endpoint.$path, $params) ;

    }

    public function delete($path, $params)
    {

        return Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getIdToken(),
            'X-Admin-Id' => Session::get('adminId'),
            'Content-Type' => 'application/json',
        ])->delete($this->endpoint.$path, $params) ;

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

        if($authtoken == false){
            throw new Exception("No authtoken found", 1);
        }

        $response = Http::post($this->endpoint.'/oauth/token', [
            'auth_token' => $authtoken
        ]) ;

        $tokens = $response->object() ; 

        Session::put('idToken', $tokens->idToken) ;
        Session::put('refreshToken', $tokens->refreshToken) ;
        Session::put('expiresIn', time() + ($tokens->expiresIn - 10)) ; // minus 10 sec margin
        
        // also store the userId and Email address
        $tokenParts = explode(".", $response->json('idToken')) ;  
        $tokenPayload = base64_decode($tokenParts[1]) ;

        $userData = json_decode($tokenPayload) ;

        Session::put('userUid', $userData->user_id) ;
        Session::put('email', $userData->email) ;
        Session::put('name', $userData->name) ;

        return true ;
        
    }

}
