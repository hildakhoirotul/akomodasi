<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\IsInspektur;
use App\Models\Activity;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    protected $user;
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'nik';
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            if ($user->role == 'admin') {
                Alert::success('Berhasil Masuk', 'Selamat Datang ' . auth()->user()->name);
                // return redirect()->route('home');
                $url = session()->get('url.intended', '/home');
                return redirect()->intended($url);
            } elseif ($user->role == 'inspektur') {
                // return redirect()->route('home.inspektur');
                $url = session()->get('url.intended', '/inspektur-dashboard');
                if ($url == '/inspektur-dashboard') {
                    Alert::success('Berhasil Masuk', 'Selamat Datang ' . auth()->user()->name);
                }
                return redirect()->intended($url);
            } else {
                Alert::error('Access Denied', 'Pastikan NIK dan Password Anda benar');
                return redirect()->route('login');
            }
        } else {
            Alert::error('Login Gagal', 'NIK atau kata sandi salah!')->persistent(true, false);
            return redirect()->route('login');
        }
    }

    public function guestLogin()
    {
        $guest = User::where('role', 'guest')->first();
        Auth::login($guest);

        return redirect()->route('home.guest');
    }

    public function logout(Request $request)
    {
        if (session()->has('login_activity')) {
            $activity = new Activity();
            $activity->user = auth()->user()->name;
            $activity->description = 'Logout';
            $activity->status = 'text-info';
            $activity->save();

            session()->forget('login_activity');
        }
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }
}
