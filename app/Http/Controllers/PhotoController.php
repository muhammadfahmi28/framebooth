<?php

namespace App\Http\Controllers;

use App\Models\PendingPrint;
use App\Models\Photo;
use App\Models\Tuser;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;
use chillerlan\QRCode\QRCode;
use Intervention\Image\ImageManager;

class PhotoController extends Controller
{
    function capture() {
        return view('pages.photo.capture');
    }

    function saveAndPrint(Request $request) {
        $manager = new ImageManager(['driver' => 'gd']);
        $tuser = Tuser::find(auth()->user()->id);
        $uid = $tuser->uid;

        $photo_filename = time() . Str::random(3);
        $photo_ext = 'jpg';

        // validate
        $validated = $request->validate([
            'main_photo' => 'required',
            'raw' => 'required'
        ]);

        // TODO REMOTE GET calon url {$uid}/" . $photo_filename . "." . $photo_ext -->> return string url
        $imgUrl = 'https://www.google.com/'; // << Dari Remote
        $qrBase64 = (new QRCode)->render($imgUrl);

        try {

            // Save with QR
            $imgBase64 = $request->main_photo;
            $imgImage = $manager->read($imgBase64);

            if (env('PRINT_QR', false)) {
                $imgQR = $manager->read($qrBase64);
                $imgQR->scale(height: 360);
                $imgImage->place($imgQR, 'top-left', 459, 2127);
            }

            // $imgImage->toJpeg(100)->save(storage_path("app/public/photos/{$uid}/" . $photo_filename . "." . $photo_ext));
            $imgBase64 = $imgImage->toJpeg(100)->toDataUri(); // to data Uri cause intervention cant create directories

            $imgImage->scale(height: 360); //for thumbs
            $imgThumbBase64 = $imgImage->toJpeg(70)->toDataUri();
            $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgBase64));
            $imgThumbBaseData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgThumbBase64));

            // // Save Directly w/o QR
            // $imgBase64 = $request->main_photo;
            // $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgBase64));

            //  -----  SAVE FILE
            // STORE TEMPS
            $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
            file_put_contents($tmpFilePath, $imgData);

            $tmpFileThumbPath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
            file_put_contents($tmpFileThumbPath, $imgThumbBaseData);

            // STORE PHOTO
            $tmpFile = new  File($tmpFilePath);
            $file = new UploadedFile(
                $tmpFile->getPathname(),
                $tmpFile->getFilename(),
                $tmpFile->getMimeType(),
                0,
                true
            );
            Storage::putFileAs("public/photos/{$uid}/", $file, $photo_filename . "." . $photo_ext);

            // STORE THUMB
            $tmpFileThumb = new  File($tmpFileThumbPath);
            $file = new UploadedFile(
                $tmpFileThumb->getPathname(),
                $tmpFileThumb->getFilename(),
                $tmpFileThumb->getMimeType(),
                0,
                true
            );
            Storage::putFileAs("public/photos/{$uid}/small/", $file, $photo_filename . "." . $photo_ext);

            //  -----  SAVE RAWS
            $rawsBase64 = $request->raw;
            $rawFileNames = [];
            $i = 1;
            foreach ($rawsBase64 as $rawBase64) {
                $rawImgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $rawBase64)) ;
                $rawThumbImage = $manager->read($rawImgData);
                $rawThumbImage->scale(height: 360); //for thumbs
                $rawThumbBase64 = $rawThumbImage->toJpeg(70)->toDataUri();
                $rawThumbData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $rawThumbBase64)) ;

                // STORE RAWS
                $saveName = $photo_filename . "_raw_" . $i . "." . $photo_ext;

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
                Storage::putFileAs("public/photos/{$uid}/", $file, $saveName);
                $rawFileNames[] = $saveName;

                $tmpFileThumbPath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
                file_put_contents($tmpFileThumbPath, $rawThumbData);

                $tmpFileThumb = new  File($tmpFileThumbPath);
                $fileThumb = new UploadedFile(
                    $tmpFileThumb->getPathname(),
                    $tmpFileThumb->getFilename(),
                    $tmpFileThumb->getMimeType(),
                    0,
                    true
                );
                Storage::putFileAs("public/photos/{$uid}/small/", $fileThumb, $saveName);

                $i++;
                // put raws
            }

        } catch (\Throwable $th) {
            return json_encode(["status" => "FAILED", "body" => $request->all()]);
        }

        // TODO UPLOAD KE REMOTE Dengan link placehoder diatas

        $photo = $tuser->photos()->create([
            "filename" => $photo_filename . "." . $photo_ext,
            "raws" => $rawFileNames
        ]);

        // insert into pending
        $pending = PendingPrint::whereNull('second_id')->orderBy("created_at", "ASC")->first();
        if (is_null($pending)) {
            //crete new pending
            $pending = PendingPrint::create([
                'filename_1st'=> $photo->filename,
                'first_id'=> $photo->id,
            ]);
        } else {
            $pending->update([
                'filename_2nd' => $photo->filename,
                'second_id' => $photo->id
            ]);
        }

        return json_encode(["status" => "OK", "body" => $request->all(), "dd" => $photo->toArray()]);
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

    function recieveSyncPhoto(Request $request, $photo_id) {
        $photo = Photo::find($photo_id);
        $main = $request->file('main');
        $raws = $request->file('raw');

        if (!empty($photo)) {

            $owner = $photo->tuser;
            $manager = new ImageManager(['driver' => 'gd']);
            Storage::makeDirectory("public/photos/{$owner->uid}/small"); //prepare dir

            $mainImageThumbs = $manager->read($main->getRealPath());
            $mainImageThumbs->scale(height: 360); //for thumbs
            $savePath = storage_path("app/public/photos/{$owner->uid}/small/".$photo->filename);
            $mainSavedThumbs = $mainImageThumbs->toJpeg(100)->save($savePath);

            Storage::putFileAs("public/photos/{$owner->uid}/", $main, $photo->filename);

            foreach ($photo->raws as $key => $raw_filename) {
                if (array_key_exists($key, $raws)) {
                    $rawImageThumbs = $manager->read($raws[$key]->getRealPath());
                    $rawImageThumbs->scale(height: 360); //for thumbs
                    $savePath = storage_path("app/public/photos/{$owner->uid}/small/".$raw_filename);
                    $rawSavedThumbs = $rawImageThumbs->toJpeg(100)->save($savePath);
                    Storage::putFileAs("public/photos/{$owner->uid}/", $raws[$key], $raw_filename);
                }
            }

            $photo->update([
                "updated_at" => now()
            ]);

            return response("Upload Success", 200);
        }
        return response("not found", 300);
    }

    function testRecieveSyncPhoto(Request $request, $photo_id) {
        // return response("OK", 200);
        $photo = Photo::find($photo_id);
        $raws = $request->file('raw');
        $main = $request->file('main');
        $requessss = $request->files->all();

        $i = 0;
        if (!empty($photo)) {
            $manager = new ImageManager(['driver' => 'gd']);
            $mainImage = $manager->read($main->getRealPath());
            // return dd($requessss);
            return dd($requessss, $mainImage);

            foreach ($raws as $file) {
                Storage::putFileAs("public/uptest/", $file, time() . $i . '.png');
                // Storage::disk('public')->put(time(), $file);
                // $file->store('storage/uploads','public');
                $i++;
            }
            // return response("asd", 200);
        }
        return response("not found", 300);
    }

}
