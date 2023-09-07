<?php

namespace App\Http\Controllers;

use App\Models\Tuser;
use App\Models\User;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    function index() {
        $tuser = Tuser::find(auth()->user()->id);
        $photos = $tuser->photos();
        return view('pages.gallery.index', compact('tuser', 'photos'));
    }
}
