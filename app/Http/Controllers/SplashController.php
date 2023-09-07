<?php

namespace App\Http\Controllers;

use App\Models\Tuser;
use Illuminate\Http\Request;

class SplashController extends Controller
{
    function index() {
        return view('pages.splash');
    }

    function login(Request $request) {
        //todo encode/decode

        $validated = $request->validate([
            'code' => 'required',
        ]);

        $tuser = Tuser::where('code', $validated['code'])->first();
        if ($tuser) {
            $tuser->makeVisible([
                'code',
                'uid'
            ]);
            return dd($tuser->toArray());
        }
        return back()->withErrors(["code" => ["Code Not Valid"]]);
    }
    //
}
