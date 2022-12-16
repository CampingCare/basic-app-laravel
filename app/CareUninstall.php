<?php

namespace App;
use Session;

class CareUninstall
{
    
    static public function run()
    {

        // remove the entire session
        Session::flush();

    }

}
