<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        return view('login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'aid' => ['required', 'string', 'regex:/^[Aa][0-9]+$/i'],
                'pwd' => ['required', 'string', 'min:8', Password::min(8)->letters()->numbers()->mixedCase()->symbols()]
            ]);
        } catch (ValidationException $e) {
            // dd($e->getMessage());
            return response()->json(['status' => 999, 'err' => 'Please ensure are fields are filled with valid data and format.']);
        }

        $credentials = ['password' => $request->pwd, 'admin_id' => strtoupper($request->aid)];
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(['status' => 808, 'redirect_url' => route('admin')]);
        } else return response()->json(['status' => 999, 'err' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
