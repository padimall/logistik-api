<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use \App\Http\Traits\UsesUuid;
    protected $fillable = [
        'user_id','country','province','city'
    ];
}
