<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Image;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function redirectTo()
    {
    if (auth()->user()->is_admin) {
        return '/admin';  // adminの場合、このURLにリダイレクトします。
    }
        return '/home';  // それ以外の場合、このURLにリダイレクトします。
    }

    public function showLoginForm()
    {
        $images = Image::orderBy('download_count', 'desc')->take(7)->get();
        
        return view('auth.login', compact('images'));
    }

    public function showRules()
    {
        return view('auth.rules');
    }



    

}
