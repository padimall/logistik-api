<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tracking;
use App\Package;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function package(Request $request)
    {
        $request->validate([
            'target_id'=>'required|exists:packages,no_resi'
        ]);

        $package = Package::where('no_resi',$request['target_id'])->first();

        $data = Tracking::where('package_id',$package['id'])->orderBy('created_at','DESC')->get();

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

    public function multiplesend(Request $request)
    {
        $request->validate([
            'list' => 'required'
        ]);

        $list = $request['list'];

        for($i=0; $i<sizeof($list); $i++){
            $package = Package::where('no_resi',$list[$i])->first();
            $data['package_id'] = $package['id'];
            $data['user_id'] = request()->user()->id;
            $data['location'] = request()->user()->origin;
            $data['detail'] = 'Paket akan dikirimkan ke gateway di '.request()->user()->destination;
            $response = Tracking::create($data);
            $package->last_pick = NULL;
            $package->save();
        }
        return response()->json([
            'status' => 1,
            'message' => 'Success!'
        ],200);
    }

    public function send(Request $request)
    {
        $request->validate([
            'target_id' => 'required|exists:packages,no_resi'
        ]);

        $package = Package::where('no_resi',$request['target_id'])->first();

        if($package['last_pick'] != request()->user()->id)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Package not here!'
            ],200);
        }

        $data['package_id'] = $package['id'];
        $data['user_id'] = request()->user()->id;
        $data['location'] = request()->user()->origin;
        $data['detail'] = 'Paket akan dikirimkan ke gateway di '.request()->user()->destination;
        $response = Tracking::create($data);


        $package->last_pick = NULL;
        $package->save();

        return response()->json([
            'status' => 1,
            'message' => 'Resource created!'
        ],201);
    }

    public function receive(Request $request)
    {
        $request->validate([
            'target_id' => 'required|exists:packages,no_resi'
        ]);

        $package = Package::where('no_resi',$request['target_id'])->first();
        if($package['last_pick'] == request()->user()->id)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Already Here!'
            ],200);
        }

        $data['package_id'] = $package['id'];
        $data['user_id'] = request()->user()->id;
        $data['location'] = request()->user()->origin;
        $data['detail'] = 'Paket telah sampai di drop point di '.request()->user()->origin;
        $response = Tracking::create($data);


        $package->last_pick = request()->user()->id;
        $package->save();

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
