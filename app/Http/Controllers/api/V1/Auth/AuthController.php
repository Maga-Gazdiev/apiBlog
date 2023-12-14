<?php

namespace App\Http\Controllers\api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|email|ends_with:gmail.com,yandex.ru,mail.ru,yandex.com,mail.com,gmail.ru',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => Hash::make($request->password)]
        ));

        $token = $user->createToken('authToken')->plainTextToken;
        $user = Auth::user();
        return response(['token' => $token], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
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
