<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function register(Request $request){

        try{
            $credentials = $request->validate([
                'name' => 'required|max:20,min:1',
                'email' => 'required|email|max:50|unique:users',
                'password' => 'required|confirmed|min:5|max:15'
            ]);
        }
        catch(ValidationException $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ],400);
        }

        $credentials['role'] = 'owner';
        $credentials['password'] = Hash::make($credentials['password']);
        $user = new User($credentials);
        $user->save();

        $token = $user->createToken('user')->plainTextToken;
        return response()->json([
            'message' => 'registered successfully',
            'token' => $token
        ],200);
    }

    public function login(Request $request){

        try{
            $credentials = $request->validate([
                'email' => 'required|exists:users|email',
                'password' => 'required|min:5|max:15'
            ]);
        }
        catch(ValidationException $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ],400);
        }

        $user = User::where('email',$credentials['email'])->first();

        if(!Hash::check($credentials['password'], $user['password'])){
            return response()->json([
                'message' => 'error',
                'error' => 'wrong password'
            ],401);
        }

        return response()->json([
            'message' => 'logged in successfully',
            'token' => $user->createToken('user')->plainTextToken
        ],200);
    }

    public function logout(Request $request){

        auth()->user()->currentAccessToken()->delete();
        
        return response()->json([
            'you logged out successfully.'
        ],200);
    }
}
