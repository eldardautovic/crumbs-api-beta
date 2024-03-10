<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request) {
        $request->validated($request->all());

        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->error("", "Invalid credentials.", 401);
        }

        $user = User::where('email', $request->email)->findFirst();

        return $this->success([
            'token' => $user->createToken('API token of' . $user->name)->plainTextToken
        ], "Successful login.", 200);
    }

    public function register(StoreUserRequest $request) {
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API token of' . $user->name)->plainTextToken
        ], 'Successful register.', 200);
    }
}
