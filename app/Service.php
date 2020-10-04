<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use \App\Http\Traits\UsesUuid;
    protected $fillable = [
        'name','detail','price'
    ];
}
