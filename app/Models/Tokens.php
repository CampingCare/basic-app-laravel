<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tokens extends Model
{
    use HasFactory;

    public $casts = [
        'id_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];
    
}
