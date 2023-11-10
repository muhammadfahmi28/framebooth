<?php

namespace App\Http\Controllers;

use App\Models\Tuser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SplashController extends Controller
{
    function index() {
        return view('pages.splash.index');
    }

    function login(Request $request) {
        //todo encode/decode request

        $validated = $request->validate([
            'code' => 'required',
        ]);

        $tuser = Tuser::where('code', $validated['code'])->first();
        if ($tuser) {
            $tuser->makeVisible([
                'code',
                'uid'
            ]);

            $is_expired = ($tuser->valid_until == null) ? false : Carbon::now()->gt($tuser->valid_until);

            if ($is_expired) {
                return back()->withErrors(["code" => ["Code Expired"]]);
            }

            Auth::login($tuser);
            return Redirect::route('app.gallery');
        }
        return back()->withErrors(["code" => ["Code Not Valid"]]);
    }

    function logout(Request $request) {
        Auth::logout();
        return redirect('/');
    }

    function apiPong(Request $request) {
        return json_encode(["status"=>"PONG", "SERVER"=>env('API_KEY'), "CLIENT"=>request()->header('key')]) ;
    }

}
