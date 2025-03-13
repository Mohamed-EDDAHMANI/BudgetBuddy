<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken', [$user->name])->plainTextToken;
            return response()->json(['token' => $token]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken("personal_access_token", [$user->name])->plainTextToken;
        if(! $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function user(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    public function getUserName(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $token = $user->currentAccessToken();
        $name = $token ? ($token->abilities[0] ?? 'no-name') : 'no-name';

        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'token_ability' => $name,
        ]);
    }
}


// "user": {
//         "name": "simox",
//         "email": "simox@gmail.com",
//         "updated_at": "2025-03-10T12:27:14.000000Z",
//         "created_at": "2025-03-10T12:27:14.000000Z",
//         "id": 1,
//         "token": "1|I3LH1cjFZ3PyoJRvhvYEmWJPfHUkOhA1S0O13ef9"
//     }
