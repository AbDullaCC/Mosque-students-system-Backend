<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Adminstrator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class UserController extends Controller
{

    public function register(Request $request){

        try{
            $credentials = $request->validate([
                'name' => 'required|max:20|min:1',
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
            'token' => $user->createToken('user')->plainTextToken,
            'name' => $user->name,
            'email' => $user->email
        ],200);
    }

    public function logout(Request $request){

        auth()->user()->currentAccessToken()->delete();
        
        return response()->json([
            'you logged out successfully.'
        ],200);
    }

    public function createUser(Request $request)
    {
        $this->authorize('owner',Mosque::class);

        try{
            $info = $request->validate([
                'name' => 'required|max:20|min:1|unique:users',
                'email' => 'required|email|max:50|unique:users',
                'role' => 'required|in:admin,teacher',
                'mosque_id' => 'required|exists:mosques,id',
                'admin_type' => 'required_if:role,admin|in:helper,responsible'
            ]);
        }
        catch(ValidationException $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ],400);
        }

        $password = Str::random(6);
        $info['password'] = Hash::make($password);

        $user = User::create($info);

        $user['password'] = $password;
        $user['mosque_id'] = $info['mosque_id'];
        $user->makeVisible(['password']);

        if($user->role == 'teacher'){
            Teacher::create(['user_id' => $user->id, 'mosque_id' => $info['mosque_id']]);
        }
        else{
            Adminstrator::create(['user_id' => $user->id, 'mosque_id' => $info['mosque_id'], 'admin_type' => $request->admin_type]);
            $user['admin_type'] = $request->admin_type;
        }

        return response()->json([
            'message' => 'new user was created successfully',
            'user' => $user,
        ]);
    }
}
