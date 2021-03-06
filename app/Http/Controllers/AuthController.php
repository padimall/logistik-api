<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|string|unique:users',
            'origin' => 'required|string',
            'type' => 'required|string',
            'password' => 'required|string|confirmed|min:8',

        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'origin' => $request->origin,
            'type' => $request->type,
            'password' => bcrypt($request->password)
        ]);
        $user->save();

        return response()->json([
            'status' => 1,
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token',['user-token']);
        $token = $tokenResult->token;
        if ($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        else {
            $token->expires_at = Carbon::now()->addDays(1);
        }
            $token->save();

            $data = User::where('id',request()->user()->id)->first();
            if(!is_null($request['device_id'])){
                $request->validate([
                    'device_id' => 'required'
                ]);
                $data->device_id = $request['device_id'];
            }
            $data->save();

            return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
    }

    public function login_dev(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'keyword' => 'required|string'
        ]);

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized'
            ], 401);

        if(hash('sha256',$request['keyword']) != 'c95f46c7236e806bf134ac4ebc372a8a0313845630ba7072b2ea743f8a030491'){
            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('System Access Token',['system-token']);
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(54000);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {

        $data = User::where('id',request()->user()->id)->first();

        if(!is_null($request['name'])){
            $request->validate([
                'name' => 'required'
            ]);
            $data->name = $request['name'];
        }

        if(!is_null($request['email'])){
            $request->validate([
                'email' => 'required|email|unique:users,email'
            ]);
            $data->email = $request['email'];
        }

        if(!is_null($request['password'])){
            $request->validate([
                'password' => 'required'
            ]);
            $data->password = bcrypt($request['password']);
        }

        if(!is_null($request['origin'])){
            $request->validate([
                'origin' => 'required'
            ]);
            $data->origin = $request['origin'];
        }

        if(!is_null($request['type'])){
            $request->validate([
                'type' => 'required'
            ]);
            $data->type = $request['type'];
        }

        if(!is_null($request['phone'])){
            $request->validate([
                'phone' => 'required|unique:users,phone'
            ]);
            $data->phone = $request['phone'];
        }

        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Resource updated!'
        ],200);
    }

    public function update_password(Request $request)
    {

        $data = User::where('id',request()->user()->id)->first();

        if(!is_null($request['old_password'])){
            if(Hash::check($request['old_password'], $data->password)){
                $request->validate([
                    'password' => 'required|string|confirmed|min:8'
                ]);
                $data->password = bcrypt($request['password']);
                $data->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Password change!'
                ],200);
            }
            else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Wrong old password!'
                ],200);
            }
        }

    }
}
