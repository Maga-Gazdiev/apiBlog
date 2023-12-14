<?php

namespace App\Http\Controllers\api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;



class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $request->validate([
            'email' => 'required|email|ends_with:gmail.com,yandex.ru,mail.ru,yandex.com,mail.com,gmail.ru',
            'name' => 'required|string|max:255',
            'password' => ['required', 'string', 'min:8'],
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;
        $user = Auth::user();
        return response(['token' => $token], 200);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->toArray())) {
            return response(['error' => 'Не зарегистрирован'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response(['token' => $token], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response('Вы успешно вышли из аккаунта', 200);
    }
}
