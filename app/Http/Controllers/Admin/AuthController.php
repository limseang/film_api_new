<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirebaseRealTimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $firebaseService;
    
    public function __construct(FirebaseRealTimeService $firebaseService)
    {
        $this->middleware('guest')->except('logout');
        $this->firebaseService = $firebaseService;
    }

    public function getLogin()
    {
        return view('login');
    }
    public function postLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        // Get user before logging out
        $user = Auth::user();
        
        // Mark user as offline in Firebase if they exist
        if ($user) {
            $this->firebaseService->updateUserStatus($user->id, false, [
                'last_active' => time(),
                'logout_time' => time()
            ]);
        }
        
        // Standard logout procedure
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }


}
