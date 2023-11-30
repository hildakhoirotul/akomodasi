<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
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
            // if (auth()->user()->is_admin == 1) {
            // Alert::success('Berhasil Masuk', 'Selamat Datang ' . auth()->user()->nama);
            // return redirect()->route('dashboard');
            // } else {
            Alert::success('Berhasil Masuk', 'Selamat Datang ' . auth()->user()->name);
            return redirect()->route('home');
            // }
        } else {
            Alert::error('Login Gagal', 'NIK atau kata sandi salah!')->persistent(true, false);
            return redirect()->route('login');
        }
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
