<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;



class AuthController extends Controller
{
    public function register(Request $request){

        $fields = $request->validate([
           'email' => ['required', 'string', 'email', 'unique:users,email'],
           'password'=> ['required', 'string'],
           'name'=> ['required', 'string', 'max:255'],
           'phone_number'=> ['required', 'string', 'max:255'],
           'role'=> ['integer']
        ]);



        $user = User::create([
           'email'=> $fields['email'],
           'password'=> bcrypt($fields['password']),
           'name'=> $fields['name'],
           'phone_number'=> $fields['phone_number'],
           'user_role'=> $fields['role']
        ]);

        $role = $fields['role'] === 1 ? "admin" : "user";

        $token = $user->createToken(env('TOKEN_AUTHENTICATION'), [$role]);

       return ['success'=> true, "user"=> $user, "token"=>$token->plainTextToken];
    }
   
    public function login(Request $request){

        $fields = $request->validate([
           'email' => ['required', 'string', 'email'],
           'password'=> ['required', 'string']
        ]);

       //check email
       $user = User::where('email', $fields['email'])->first();

       //check password 
       if (!$user || !Hash::check($fields['password'], $user->password)) {
           return response(['success'=>false, 'message'=> 'Invalid username or password'], 400);
       }

       $user_role = $user->user_role === 1 ? "admin" : "user";

       $token = $user->createToken(env('TOKEN_AUTHENTICATION'), [$user_role])->plainTextToken;

       return response(['success'=> true, 'user'=> $user, 'token'=> $token], 200);
    }

   // public function logout(Request $request) {
   //    auth()->user()->tokens()->delete();

   //    Redis::flushDB();

   //    return ['message' => 'Logged out'];
   // }
}
