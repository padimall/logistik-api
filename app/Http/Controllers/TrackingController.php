<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tracking;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function package(Request $request)
    {
        $request->validate([
            'target_id'=>'required|exists:packages,id'
        ]);

        $data = Tracking::where('package_id',$request['target_id'])->orderBy('created_at','DESC')->get();

        if(is_null($data)){
            return response()->json([
                'status' => 0,
                'message' => 'Resource not found!'
            ],404);
        }
        return response()->json([
            'status' => 1,
            'message' => 'Resource found!',
            'data' => $data
        ],200);
    }

    public function send(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id'
        ]);

        $data = $request->all();
        $data['user_id'] = request()->user()->id;
        $data['location'] = request()->user()->origin;
        $data['detail'] = 'Paket akan dikirimkan ke gateway di '.request()->user()->destination;
        $response = Tracking::create($data);

        return response()->json([
            'status' => 1,
            'message' => 'Resource created!'
        ],201);
    }

    public function receive(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id'
        ]);

        $data = $request->all();
        $data['user_id'] = request()->user()->id;
        $data['location'] = request()->user()->origin;
        $data['detail'] = 'Paket telah sampai di drop point di '.request()->user()->origin;
        $response = Tracking::create($data);

        return response()->json([
            'status' => 1,
            'message' => 'Resource created!'
        ],201);
    }

    public function delete(Request $request){
        $request->validate([
            'target_id' => 'required'
        ]);

        $data = DB::table('trackings')
                ->where('id',$request['target_id'])
                ->where('user_id',request()->user()->id)
                ->first();

        if(!is_null($data))
        {
            $response = $data->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Resource deleted!'
            ],200);
        }
        else
        {
            return response()->json([
                'status' => 0,
                'message' => 'Resource not found!'
            ],404);
        }



    }
}
