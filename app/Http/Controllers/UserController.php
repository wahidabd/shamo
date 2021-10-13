<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller{

    // Login
    public function login(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => 'required'
            ]);

            if ($validate->fails()){
                return ResponseFormatter::error([
                    $validate->errors()
                ], 'Authentication Failed');
            }

            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized',
                ], 'Login Failed');
            }

            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $token = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        } catch (Exception $err) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $err,
            ], 'Authentication Failed');
        }
    }

    // Register
    public function register(Request $request) {

        try{
            $validate = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:30'],
                'username' => ['required', 'string', 'max:30', 'unique:users'],
                'email' => ['required', 'email', 'max:50', 'unique:users'],
                'password' => ['required', 'string', 'min:6', new Password]
            ]);

            if ($validate->fails()){
                return ResponseFormatter::error([
                    $validate->errors()
                ], 'Authentication Failed');
            }

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password)
            ]);

            return ResponseFormatter::success([], 'User Registered');

        }catch(Exception $err){
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $err,
            ],'Authentication Failed');
        }
    }
    
}
