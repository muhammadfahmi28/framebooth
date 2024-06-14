<?php

namespace App\Console\Commands;

use App\Models\PendingPrint;
use App\Models\Photo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Font;
use Intervention\Image\ImageManager;

class PhotoPrint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photo:print {--work}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print available pending Print';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('work')) {
            for ($i=0; true ; $i++) {
                $this->checkAndPrint();
                sleep(env("UPLOAD_WAIT", 3));
            }
            $this->line("and it ends...");
            return Command::SUCCESS;
        } else {
            if ($this->checkAndPrint()) {
                return Command::SUCCESS;
            };
        }
    }

    function checkAndPrint() : bool {
        // Get pending print with 2nd photo available
        $pending = PendingPrint::whereNotNull('second_id')->whereNull('printed_at')->orderBy("created_at", "ASC")->first();

        if ($pending == null) {
            $this->line("NO COMPLETE PENDING PRINT, SEARCHING PAST FAILED");
            $pending = PendingPrint::whereNotNull('second_id')->whereNotNull('failed_at')->orderBy("failed_at", "ASC")->first();
            if ($pending == null) {
                $this->line("NO FAILED PENDING PRINT");
                return true;
            }
        }

        try {
            $wGutter = 300; //!!DONTCHANGE 300
            $images = Photo::with('tuser')->whereIn('id', array($pending->first_id, $pending->second_id))->get();
            // $this->line($images[0]->getRelativePath() . $images[1]->getRelativePath());

            $manager = new ImageManager(['driver' => 'gd']);
            $mainImage = $manager->read($images[0]->getRealPath());
            $secondImage = $manager->read($images[1]->getRealPath());
            $mainCode = $images[0]->tuser->code;
            $secondCode = $images[0]->tuser->code;

            $fontPath = base_path().'/public/assets/vendor/google/static/Urbanist-Medium.ttf';

            // $this->line($mainImage->getHeight() . $secondImage->getHeight());

            // $w = $mainImage->getWidth() + $secondImage->getWidth() + $wGutter;
            // $h = max($mainImage->getHeight(), $secondImage->getHeight() + $wGutter);

            // $mergedImage = $manager->create($w, $h);
            // $mergedImage = $mergedImage->fill('#FFFFFF', 10, 10);

            $mergedImage = $manager->read(base_path().'/public/assets/images/print-background.png');

            $mergedImage->place($mainImage, 'top-right', ($mergedImage->getWidth()/2)+8, ($mergedImage->getHeight()/2) - ($mainImage->getHeight()/2) + 8); //Yadjusted
            $mergedImage->place($secondImage, 'top-left', ($mergedImage->getWidth()/2)-8, ($mergedImage->getHeight()/2) - ($secondImage->getHeight()/2) + 8); //Yadjusted
            // $mergedImage->resizeCanvas(200,  0, 'center', true);

            // write text to image
            $mergedImage->text($mainCode, ($wGutter/2)+28, 97, function ($font) {
                $font->filename( base_path().'/public/assets/vendor/google/static/Urbanist-Medium.ttf');
                $font->size(56);
                $font->align('left');
                $font->valign('bottom');
            });

            $mergedImage->text($secondCode, ($mergedImage->getWidth()/2)+48, 97, function (Font $font) {
                $font->filename( base_path().'/public/assets/vendor/google/static/Urbanist-Medium.ttf');
                $font->size(56);
                $font->align('left');
                $font->valign('bottom');
            });

            $mergedImage->scale($mergedImage->getWidth() - 236); //!! ENABLE ON PROD - Final Adjustment Disable for test scale

            $filenamemerge = $pending->id . "_" . time() . '.jpg';

            // $savePath = 'storage/app/public/prints/'. $filenamemerge;

            $savePath = 'app/public/prints/'. $filenamemerge;
            Storage::makeDirectory('public/prints');
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $savePath = str_replace('/', '\\', $savePath);
            }
            $savePath = storage_path($savePath);

            $encoded = $mergedImage->toJpeg(100)->save($savePath);
            $this->line("MERGE OK");
            $this->line("PRINTING...");
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec('rundll32 C:\WINDOWS\system32\shimgvw.dll,ImageView_PrintTo "'.$savePath.'" "'.env('PRINTER_NAME').'"'); //!! ENABLE ON PROD Disable for testing merge

                // exec('"C:\Program Files\IrfanView\i_view64.exe" "'.$savePath.'" /print="'.env('PRINTER_NAME').'"');
                // exec('mspaint /pt "'.$savePath.'" "'.env('PRINTER_NAME').'"');
                $this->line("PRINTING CALLED");
            } else {
                $this->line("UNSUPPORTED OS");
            }

        } catch (\Exception $ex) {
            $this->line("FAILED TO LOAD IMAGES");

            $pending->update([
                "failed_at" => now(),
            ]);

            report($ex);
            return false;
        }


        $pending->update([
            "printed_at" => now(),
            "failed_at" => null,
            "filename_merged" => $filenamemerge
        ]);

        return true;
    }

}
