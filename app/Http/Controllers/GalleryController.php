<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Tuser;
use App\Models\User;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    function index() {
        $tuser = Tuser::find(auth()->user()->id);
        $photos = $tuser->photos;
        $folder = Photo::DEFAULT_DIR . '/' . $tuser->uid;
        return view('pages.gallery.index', compact('tuser', 'photos', 'folder'));
    }

    function show($id) {
        $tuser = Tuser::find(auth()->user()->id);
        $photo = $tuser->photos()->find($id);
        return view('pages.gallery.show', compact('tuser', 'photo',));
    }

    function delete($id) {
        $tuser = Tuser::find(auth()->user()->id);
        $photo = $tuser->photos()->find($id);
        $real_path = $photo->getRealPath();

        if (file_exists($real_path)) {
            try {
                unlink($real_path);
            } catch (\Throwable $th) {
                report($th);
                return back()->withErrors(["delete"=>"cannot delete photo"]);
            }
        }
        $photo->delete();
        return back();
    }

    function testshellopen($id){
        // $tuser = Tuser::find(auth()->user()->id);
        // $photo = $tuser->photos()->find($id);
        // if ($photo) {
        //     $storage_rel_path = 'app/public/'.Photo::DEFAULT_DIR . '/' . $tuser->uid.'/'.$photo->filename;
        //     if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        //         $storage_rel_path = str_replace('/', '\\', $storage_rel_path);
        //         shell_exec(storage_path($storage_rel_path));
        //     }
        //     return dd(storage_path($storage_rel_path));
        // }
        return back();
    }

    function printPhoto($id){
        $tuser = Tuser::find(auth()->user()->id);
        $photo = $tuser->photos()->find($id);
        if ($photo) {
            $storage_rel_path = 'app/public/'.Photo::DEFAULT_DIR . '/' . $tuser->uid.'/'.$photo->filename;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $storage_rel_path = str_replace('/', '\\', $storage_rel_path);
                $real_path = storage_path($storage_rel_path);
                exec('rundll32 C:\WINDOWS\system32\shimgvw.dll,ImageView_PrintTo "'.$real_path.'" "PDF24"');
            }

        }
        return back();
    }
}
