<?php

namespace App ;
use Illuminate\Support\Facades\Http;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\UnencryptedToken;

use Illuminate\Support\Facades\DB;

class CampingCareAuth 
{
    
    function getUserByToken($request)
    {

        $token = $request->input('authtoken') ;

        $response = Http::post('https://api.staging.camping.care/v21/oauth/token', [
            'auth_token' => $token
        ]);

        $parser = new Parser(new JoseEncoder());

        try {
            $decodedToken = $parser->parse($response->json('idToken'));
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            echo 'Oh no, an error: ' . $e->getMessage();
        }

        $user = [] ;
        $user['user_uid'] = $decodedToken->claims()->get('user_id') ;
        $user['name'] = $decodedToken->claims()->get('name') ;
        $user['email'] = $decodedToken->claims()->get('email') ;
        $user['idToken'] = $decodedToken->claims()->get('idToken') ;

        $where = [] ;
        $where['user_uid'] = $user['user_uid'] ;
        $where['admin_id'] = $request->input('admin_id');

        // if($request->input('chain_id') > 0){
        //     $where['chain_id'] = $request->input('chain_id') ;
        // }else{
        //     $where['admin_id'] = $request->input('admin_id');
        // }
        
        DB::table('app_accounts')
        ->updateOrInsert($where,

            [
                'chain_id' => $request->input('chain_id'), 
                'name' => $user['name'],
                'email' => $user['email'],
                'id_token' => $response->json('idToken'),
                'refresh_token' => $response->json('refreshToken'),
                'expires_in' => time() + $response->json('expiresIn'),
            ]

        );

        $account = DB::table('app_accounts')->where($where)->first();

        // dd($users) ;
        // dd($decodedToken) ;
        // dd($response->body()) ;

        // dd($response->json('idToken')) ;
        // dd($response->json('refreshToken')) ;
        // dd($response->json('expiresIn')) ;
        $account->data = json_decode($account->data, true);

        return $account  ;
        
    }

    function getUser($userId, $IdToken = false)
    {

        $account = DB::table('app_accounts')->where('user_uid', $userId)->first();

        return $this->updateToken($account, $IdToken)  ;
        
    }

    function getAccount($adminId, $chainId = 0)
    {

        // if($chainId > 0){
        //     $account = DB::table('app_accounts')->where('chain_id', $chainId)->orderBy('id', 'DESC')->first();
        // }else{
        //     $account = DB::table('app_accounts')->where('admin_id', $adminId)->orderBy('id', 'DESC')->first();
        // }
        $account = DB::table('app_accounts')->where('admin_id', $adminId)->orderBy('id', 'DESC')->first();

        return $this->updateToken($account)  ;
        
    }

    function updateToken($account = null, $IdToken = false)
    {

        if(!$account){
            throw new Exception("Could not find account", 1);
        }

        if($IdToken && $account->id_token != $IdToken){
            throw new Exception("Could not verify id token", 1);
        }

        if(time() > ($account->expires_in - 10)){
            
            $response = Http::post('https://api.staging.camping.care/v21/oauth/refresh_token', [
                'refresh_token' => $account->refresh_token
            ]);

            $account->id_token = $response->json('id_token') ;
            $account->refresh_token = $response->json('refresh_token') ;
            $account->expires_in = $response->json('expires_in') ;
            
            DB::table('app_accounts')
            ->where('user_uid', $account->user_uid)
            ->update([
                'id_token' => $response->json('id_token'),
                'refresh_token' => $response->json('refresh_token'),
                'expires_in' => time() + $response->json('expires_in'),
            ]) ;
            
        }

        $account->data = json_decode($account->data, true);

        return $account  ;
        
    }

    function delete($account)
    {

        $where = [] ;
        $where['user_uid'] = $account->user_uid;
        $where['admin_id'] = $account->admin_id ;

        // if($account->chain_id > 0){
        //     $where['chain_id'] = $account->chain_id ;
        // }else{
        //     $where['admin_id'] = $account->admin_id ;
        // }

        DB::table('app_accounts')->where($where)->delete() ;

        dd('deleted');
        
    }

}
