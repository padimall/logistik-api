<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Destination;
use Illuminate\Support\Facades\DB;

class DestinationController extends Controller
{
    public function showAll()
    {
        $data = Destination::where('user_id',request()->user()->id)->get();
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
        $data = Destination::where('id',$request['target_id'])->first();

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
            'country' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
        ]);

        $data = $request->all();
        $data['user_id'] = request()->user()->id;
        $response = Destination::create($data);

        return response()->json([
            'status' => 1,
            'message' => 'Resource created!'
        ],201);
    }

    public function update(Request $request)
    {

        $request->validate([
            'target_id'=>'required|exists:destinations,id'
        ]);

        $data = Destination::where('id',$request['target_id'])->first();

        if(!is_null($request['country'])){
            $request->validate([
                'country' => 'required'
            ]);
            $data->country = $request['country'];
        }

        if(!is_null($request['province'])){
            $request->validate([
                'province' => 'required'
            ]);
            $data->province = $request['province'];
        }

        if(!is_null($request['city'])){
            $request->validate([
                'city' => 'required'
            ]);
            $data->city = $request['city'];
        }

        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Resource updated!'
        ],200);
    }

    public function delete($id){
        $data = Destination::find($id);
        $response = $data->delete();
        return response()->json($response,200);

    }
}
