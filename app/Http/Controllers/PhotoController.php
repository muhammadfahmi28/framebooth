<?php

namespace App\Http\Controllers;

use App\Models\PendingPrint;
use App\Models\Tuser;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

class PhotoController extends Controller
{
    function capture() {
        return view('pages.photo.capture');
    }

    function saveAndPrint(Request $request) {
        $tuser = Tuser::find(auth()->user()->id);
        $uid = $tuser->uid;

        $photo_filename = time() . Str::random(3);
        $photo_ext = 'jpg';

        // validate
        $validated = $request->validate([
            'main_photo' => 'required',
            'raw' => 'required'
        ]);

        try {
            $imgBase64 = $request->main_photo;
            $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgBase64));

            // save File
            $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
            file_put_contents($tmpFilePath, $imgData);

            $tmpFile = new  File($tmpFilePath);
            $file = new UploadedFile(
                $tmpFile->getPathname(),
                $tmpFile->getFilename(),
                $tmpFile->getMimeType(),
                0,
                true
            );
            Storage::putFileAs("public/photos/{$uid}/", $file, $photo_filename . "." . $photo_ext);

            $rawsBase64 = $request->raw;
            $rawFileNames = [];
            $i = 1;
            foreach ($rawsBase64 as $rawBase64) {
                $rawImgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $rawBase64)) ;
                $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
                file_put_contents($tmpFilePath, $rawImgData);

                $tmpFile = new  File($tmpFilePath);
                $file = new UploadedFile(
                    $tmpFile->getPathname(),
                    $tmpFile->getFilename(),
                    $tmpFile->getMimeType(),
                    0,
                    true
                );
                $saveName = $photo_filename . "_raw_" . $i . "." . $photo_ext;
                Storage::putFileAs("public/photos/{$uid}/", $file, $saveName);
                $rawFileNames[] = $saveName;
                $i++;
                // put raws
            }

        } catch (\Throwable $th) {
            return json_encode(["status" => "FAILED", "body" => $request->all()]);
        }

        $tuser->photos()->create([
            "filename" => $photo_filename . "." . $photo_ext,
            "raws" => $rawFileNames
        ]);

        //TODO insert into pending
        // $pending = PendingPrint::whereNull('second_id')->orderBy("created_at", "ASC")->first();
        // if (is_null($pending)) {
        //     //crete new pending
        //     $pending = PendingPrint::create([

        //     ]);
        // } else {
        //     $pending->second = ""; //
        // }

        //TODO UPLOAD KE DRIVE

        return json_encode(["status" => "OK", "body" => $request->all()]);
    }

    function testcsrf(Request $request) {
        $tuser = Tuser::find(auth()->user()->id);

        // $tuser = auth()->user();
        $uid = $tuser->uid;

        $photo_filename = time() . Str::random(3);
        $photo_ext = 'png';
        try {
            $imgBase64 = $request->photo;
            $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgBase64));

            // save it to temporary dir first.
            $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
            file_put_contents($tmpFilePath, $imgData);

            // this just to help us get file info.
            $tmpFile = new  File($tmpFilePath);

            $file = new UploadedFile(
                $tmpFile->getPathname(),
                $tmpFile->getFilename(),
                $tmpFile->getMimeType(),
                0,
                true // Mark it as test, since the file isn't from real HTTP POST.
            );

            Storage::putFileAs("public/photos/{$uid}/", $file, $photo_filename . "." . $photo_ext);

        } catch (\Throwable $th) {
            return json_encode(["status" => "FAILED", "body" => $request->all()]);
        }

        $tuser->photos()->create([
            "filename" => $photo_filename . "." . $photo_ext
        ]);

        return json_encode(["status" => "OK", "body" => $request->all()]);
    }
}
