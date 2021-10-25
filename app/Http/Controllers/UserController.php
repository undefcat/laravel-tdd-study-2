<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function signUp(Request $request)
    {
        $rules = [
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'],
            'name' => ['required'],
        ];

        $data = $request->validate($rules);

        $user = new User();

        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->name = $data['name'];

        $user->save();

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function signIn(Request $request)
    {
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];

        $credential = $request->validate($rules);

        if (!Auth::attempt($credential)) {
            return response()->json(null, Response::HTTP_UNAUTHORIZED);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
