<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function doLogin(AuthRequest $request) {

        if (Auth::attempt($request->only('email','password'))) {

            $user = Auth::user();
            $token = $user->createToken($request->device ?? 'web')->plainTextToken;
            $user->token = $token;

            return response()->json(['user' => $user, 'status' => true, 'message' => 'Authenticated']);
        } else {
            return response()->json(['status' => false, 'message' => 'Wrong username or password'],422);
        }
    }
}
