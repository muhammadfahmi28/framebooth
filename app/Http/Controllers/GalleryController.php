<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Tuser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    function index() {
        $tuser = Tuser::find(auth()->user()->id);
        $photos = $tuser->photos;
        if ($photos->count())
        { //use count. has() will create new query.
            $folder = Photo::DEFAULT_DIR . '/' . $tuser->uid;
            return view('pages.gallery.index', compact('tuser', 'photos', 'folder'));
        } else {
            return redirect()->route('app.capture');
        }
    }

    function publicIndex(Request $request, String $uid) {
        $tuser = Tuser::where('uid', $uid)->first();
        if ($tuser == null) {
            return $this->publicShowFail();
        }
        $photos = $tuser->photos;
        if ($photos->count())
        { //use count. has() will create new query.
            if ($photos->count() === 1 && env("FEATURE_REDIRECT_IF_ONE_PHOTO", false)) {
                $id = $photos->first()->id;
                return redirect("/g/$uid/v/$id");
            }
            $folder = Photo::DEFAULT_DIR . '/' . $tuser->uid;
            return view('pages.gallery.public-index', compact('uid','tuser', 'photos', 'folder'));
        } else {
            return $this->publicShowFail();
        }
    }

    function publicShow(Request $request, $uid = null, $pid = null) {
        $uid = !empty($uid) ? $uid : $request->uid;
        $filename = !empty($request->filename) ? urldecode($request->filename) : null;
        // return dd($uid, $filename);
        if (empty($uid) || (empty($filename) && empty($pid))) {
            return abort('404');
        }

        $tuser = Tuser::where('uid', $uid)->first();

        if (!empty($tuser)) {
            $photo = null;
            if (!empty($pid)) {
                $photo = $tuser->photos()->find($pid);
            } else {
                $photo = $tuser->photos()->where('filename', $filename)->first();
            }
            $folder = Photo::DEFAULT_DIR . '/' . $tuser->uid;

            if (empty($photo)) { // could been better but whatever
                // return dd("no photo", $uid, $filename, $tuser->photos);
                return $this->publicShowFail(); // Content Not available. Sorry content not exst or not yet available, pls come back after a few moments
            }

            if (!file_exists($photo->getRealPath())) {
                return view('pages.gallery.public-show-fail');
            }

            foreach ($photo->getRawsRealPath() as $key => $raw) {
                if (!file_exists($raw)) {
                    return view('pages.gallery.public-show-fail');
                }
            }

            $title = $photo->created_at->toDateString();
            $photo_urls = [['url' => $photo->getAssetPath(), 'small' => $photo->getAssetPath(true), 'type' => 'raw']];
            $others = collect($photo->other_photos ?? []);
            $other_urls = $photo->getOtherAssetPath(null);
            $other_small_urls = $photo->getOtherAssetPath(null, true);
            $raws_urls = $photo->getRawsAssetPath();
            $raws_small_urls = $photo->getRawsAssetPath(true);

            foreach ($others as $key => $item) {
                $photo_urls[] = ['url' => $other_urls[$key], 'small' => $other_small_urls[$key], 'type' => $item['type'] ?? 'other'];
            }

            foreach ($raws_urls as $key => $url) {
                $photo_urls[] = ['url' => $url, 'small' => $raws_small_urls[$key], 'type' => 'raw'];
            }

            // dd($photo->other_photos, $photo->getOtherAssetPath(null), $photo->getOtherAssetPath(null, true));

            return view('pages.gallery.public-show', compact('title', 'uid', 'tuser', 'photo', 'photo_urls' , 'folder'));
        }
        // return dd("fallback", $uid, $filename);
        return $this->publicShowFail();
    }

    function publicShowFail() {
        if (env('PSUEDO_MASTER', false)) {
            return view('pages.gallery.public-show-fail');
        }
        return abort('404');
    }

    function show($id) {
        $tuser = Tuser::find(auth()->user()->id);
        $photo = $tuser->photos()->find($id);
        return view('pages.gallery.show', compact('tuser', 'photo',));
    }

    function delete($id) {
        $tuser = Tuser::find(auth()->user()->id);
        $photo = $tuser->photos()->find($id);

        if ($photo == null) {
            return redirect()->route('app.gallery');
        }

        $filename = $photo->filename;
        $real_path = $photo->getRealPath();
        $raws_real_path = $photo->getRawsRealPath();

        try {
            if (file_exists($real_path)) {
                unlink($real_path);
            }

            if (file_exists($photo->getRealPath(true))) {
                unlink($photo->getRealPath(true));
            }
        } catch (\Exception $ex) {
            Log::alert("CANT DELETE IMAGE FILE!! PROCEED DELETE MODEL ANYWAY >> " . $filename );
            report($ex);
        }

        try {
            foreach ($raws_real_path as $real_path) {
                if (file_exists($real_path)) {
                    try {
                        unlink($real_path);
                    } catch (\Exception $ex) {}
                }
            }

            foreach ($photo->getRawsRealPath(true) as $real_path) {
                if (file_exists($real_path)) {
                    try {
                        unlink($real_path);
                    } catch (\Exception $ex) {}
                }
            }

        } catch (\Exception $ex) {
            Log::alert("CANT DELETE RAW IMAGE FILE!! PROCEED DELETE MODEL ANYWAY >> " . $filename );
            report($ex);
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
