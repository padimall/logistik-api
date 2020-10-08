<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use \App\Http\Traits\UsesUuid;
    protected $fillable = [
        'user_id','service_id','type','origin','sender',
        'sender_contact','receiver','receiver_contact',
        'receiver_province','receiver_city','receiver_district',
        'receiver_post_code','address','weight','price','last_pick','no_resi'
    ];
}
