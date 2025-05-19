<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !$user->checkPassword($request->password)) {
            return response([
                "email" => ["Bad credentials"],
                "password" => ["Bad credentials"]
            ], 401);
        }
        $token = $user->createToken($user->name)->plainTextToken;

        return response([
            "message" => "Login successful",
            "user" => $user,
            "token" => $token
        ], 201);
    }

    public function register(Request $request)
    {

        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create($fields);
        $token = $user->createToken($user->name)->plainTextToken;

        return response([
            "message" => "Account created successfully",
            "user" => $user,
            "token" => $token
        ], 201);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response(["message" => "Logged out successfully"]);
    }
}
