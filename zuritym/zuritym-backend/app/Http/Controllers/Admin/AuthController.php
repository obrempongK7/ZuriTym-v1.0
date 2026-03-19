<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function showLogin() {
        if (Auth::check() && Auth::user()->isAdmin()) return redirect()->route('admin.dashboard');
        return view('admin.auth.login');
    }
    public function login(Request $request) {
        $request->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::attempt(['email'=>$request->email,'password'=>$request->password,'role'=>'admin'], $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }
        return back()->withErrors(['email'=>'Invalid admin credentials.'])->withInput();
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
