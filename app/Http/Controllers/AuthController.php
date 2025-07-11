<?php

namespace App\Http\Controllers;

use App\Actions\CreateAvailabilityAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Availability;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        $user = User::create( $request->all() );

        app(CreateAvailabilityAction::class)->execute($user, $request->availability);

        return $this->success([
                'user' => $user,
                'token' => $user->createToken('api_token')->plainTextToken,
            ], 'User registered successfully.', 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401 , ['password' => 'Invalid credentials']);
        }

        return $this->success([
                'user' => $user,
                'token' => $user->createToken('api_token')->plainTextToken,
            ], 'User logged in successfully.');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->success(null, 'Logged out');
    }
}
