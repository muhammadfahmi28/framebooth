<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhotoController extends Controller
{
    function capture() {
        return view('pages.photo.capture');
    }
    //
}
