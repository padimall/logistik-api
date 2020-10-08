<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Package;
use App\Tracking;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function showAll()
    {
        $data = DB::table('packages')
                ->join('services','services.id','=','packages.service_id')
                ->select('packages.*','services.name AS service_name')
                ->where('packages.last_pick',request()->user()->id)
                ->get();

        if(sizeOf($data)==0){
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

    public function showLimit(Request $request)
    {
        $request->validate([
            'limit' => 'required'
        ]);

        $data = Package::inRandomOrder()->limit($request['limit'])->get();
        if(sizeOf($data)==0){
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

    public function show(Request $request)
    {
        $request->validate([
            'target_id'=>'required'
        ]);
        $data = Package::where('id',$request['target_id'])->first();

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

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'type' => 'required|string',
            'sender' => 'required|string',
            'sender_contact' => 'required|string',
            'receiver' => 'required|string',
            'receiver_contact' => 'required|string',
            'receiver_province' => 'required|string',
            'receiver_city' => 'required|string',
            'receiver_district' => 'required|string',
            'receiver_post_code' => 'required|string',
            'address' => 'required|string',
            'weight' => 'required|numeric',
            'price' => 'required|numeric'
        ]);


        $data = $request->all();
        $data['user_id'] = request()->user()->id;
        $data['last_pick'] = request()->user()->id;
        $data['origin'] = request()->user()->origin;



        $resi = 'PL1602156904';
        while(Package::where('no_resi',$resi)->first()){
            $resi = 'PL'.strtotime(date('d-m-Y H:i:s'));
        }
        $data['no_resi'] = $resi;
        $response = Package::create($data);

        $trackingData = array(
            'package_id' => $response['id'],
            'user_id' => request()->user()->id,
            'location' => request()->user()->origin,
            'detail' => 'Paket telah diterima di drop point di '.request()->user()->origin.' (PADISTIC: '.$resi.')',
        );

        $saveTracking = Tracking::create($trackingData);

        return response()->json([
            'status' => 1,
            'message' => 'Resource created!',
            'resi'=>$resi
        ],201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'target_id'=>'required'
        ]);

        $data = Package::where('id',$request['target_id'])->first();

        if(!is_null($request['service_id'])){
            $request->validate([
                'service_id' => 'required|exists:services,id'
            ]);
            $data->service_id = $request['service_id'];
        }

        if(!is_null($request['type'])){
            $request->validate([
                'type' => 'required|string'
            ]);
            $data->type = $request['type'];
        }

        if(!is_null($request['origin'])){
            $request->validate([
                'origin' => 'required|string'
            ]);
            $data->origin = $request['origin'];
        }

        if(!is_null($request['sender'])){
            $request->validate([
                'sender' => 'required|string'
            ]);
            $data->sender = $request['sender'];
        }

        if(!is_null($request['sender_contact'])){
            $request->validate([
                'sender_contact' => 'required|string'
            ]);
            $data->sender_contact = $request['sender_contact'];
        }

        if(!is_null($request['receiver'])){
            $request->validate([
                'receiver' => 'required|string'
            ]);
            $data->receiver = $request['receiver'];
        }

        if(!is_null($request['receiver_contact'])){
            $request->validate([
                'receiver_contact' => 'required|string'
            ]);
            $data->receiver_contact = $request['receiver_contact'];
        }

        if(!is_null($request['receiver_province'])){
            $request->validate([
                'receiver_province' => 'required|string'
            ]);
            $data->receiver_province = $request['receiver_province'];
        }

        if(!is_null($request['receiver_city'])){
            $request->validate([
                'receiver_city' => 'required|string'
            ]);
            $data->receiver_city = $request['receiver_city'];
        }

        if(!is_null($request['receiver_district'])){
            $request->validate([
                'receiver_district' => 'required|string'
            ]);
            $data->receiver_district = $request['receiver_district'];
        }

        if(!is_null($request['receiver_post_code'])){
            $request->validate([
                'receiver_post_code' => 'required|string'
            ]);
            $data->receiver_post_code = $request['receiver_post_code'];
        }

        if(!is_null($request['address'])){
            $request->validate([
                'address' => 'required|string'
            ]);
            $data->address = $request['address'];
        }

        if(!is_null($request['weight'])){
            $request->validate([
                'weight' => 'required|numeric'
            ]);
            $data->weight = $request['weight'];
        }

        if(!is_null($request['price'])){
            $request->validate([
                'price' => 'required|numeric'
            ]);
            $data->price = $request['price'];
        }

        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Resource updated!'
        ],200);
    }

    public function package_service()
    {
        $package = DB::table('packages')->count();

        $service = DB::table('services')->count();

        return response()->json([
            'status' => 1,
            'message' => 'Resource found!',
            'package' => $package,
            'service' => $service,
        ],200);
    }

    public function delete($id){
        $data = Package::find($id);
        $response = $data->delete();
        return response()->json($response,200);

    }
}
