<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  public function signup(SignupRequest $request)
  {
    $data = $request->validated();

    $user = User::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => bcrypt($data['password'])
    ]);
    $token = $user->createtoken('main')->plainTextToken;

    return response()->json([
      'user' => $user,
      'token' => $token
    ]);
  }

  public function login(LoginRequest $request)
  {
    $credentials = $request->validated();
    $remember = $credentials['remember'] ?? false;
    unset($credentials['remember']);

    if(!Auth::attempt($credentials, $remember)) {
      return response()->json([
        'error' => 'Invalid credentials'
      ], 422);
    }
    $user = Auth::user();
    $token = $user->createToken('main')->plainTextToken;

    return response()->json([
      'user' => $user,
      'token' => $token
    ]);
  }

  public function logout(Request $request)
  {
    $user = Auth::user();
    $user->currentAccessToken()->delete();

    return response()->json([
      'success' => true
    ]);
  }
}
