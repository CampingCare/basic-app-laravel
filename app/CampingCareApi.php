<?php

namespace App ;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class CampingCareApi 
{
    
    static function getEndpoint($testmodes = 0)
    {

        $url = 'https://api.camping.care/v21' ; 

        if($testmodes == '1'){
            $url = 'https://api.staging.camping.care/v21' ; 
        }

        return $url ;

    }

}
