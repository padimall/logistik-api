<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use \App\Http\Traits\UsesUuid;
    protected $fillable = [
        'package_id','user_id','location','detail'
    ];
}
