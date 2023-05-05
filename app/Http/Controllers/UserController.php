<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){

        try{
            $validated = request()->validate([
                'name' => 'required|max:20,min:1',
                'email' => 'required|email|max:50|unique:users',
                'password' => 'required|confirmed|min:5'
            ]);
        }
        catch(ValidationException $e){
            return response()->json([
                'message' => 'error',
                'error: ' => $e->getMessage()
            ],400);
        }

        $validated['role'] = 'owner';
        $validated['password'] = Hash::make($validated['password']);
        $user = new User($validated);
        $user->save();

        $token = $user->createToken('user')->plainTextToken;
        return response()->json([
            'message' => 'registered succefully',
            'token: ' => $token
        ],200);
    }
}
