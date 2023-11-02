<?php

namespace App\Console\Commands;

use App\Models\PendingPrint;
use App\Models\Photo;
use Illuminate\Console\Command;
use Intervention\Image\ImageManager;

class PhotoPrint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photo:print';

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
        // Get pending print with 2nd photo available
        $pending = PendingPrint::whereNotNull('second_id')->whereNull('printed_at')->orderBy("created_at", "ASC")->first();
        if ($pending == null) {
            $this->line("NO COMPLETE PENDING PRINT");
            return Command::SUCCESS;
        }
        $images = Photo::whereIn('id', array($pending->first_id, $pending->second_id))->get();
        // $this->line($images[0]->getRelativePath() . $images[1]->getRelativePath());

        $manager = new ImageManager(['driver' => 'gd']);
        $mainImage = $manager->read($images[0]->getRealPath());
        $secondImage = $manager->read($images[1]->getRealPath());
        // $this->line($mainImage->getHeight() . $secondImage->getHeight());

        $w = $mainImage->getWidth() + $secondImage->getWidth();
        $h = max($mainImage->getHeight(), $secondImage->getHeight());

        $mergedImage = $manager->create($w, $h);
        $mergedImage->place($mainImage, 'top-left');
        $mergedImage->place($secondImage, 'top-right');

        $filenamemerge = $pending->id . "_" . time() . '.jpg';

        // $savePath = 'storage/app/public/prints/'. $filenamemerge;

        $savePath = 'app/public/prints/'. $filenamemerge;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $savePath = str_replace('/', '\\', $savePath);
        }
        $savePath = storage_path($savePath);


        $encoded = $mergedImage->toJpeg(100)->save($savePath);
        $this->line("MERGE OK");
        $this->line("PRINTING...");
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec('rundll32 C:\WINDOWS\system32\shimgvw.dll,ImageView_PrintTo "'.$savePath.'" "'.env('PRINTER_NAME').'"');
            $this->line("PRINTING CALLED");
        } else {
            $this->line("UNSUPPORTED OS");
        }
        $pending->update([
            "printed_at" => now(),
            "filename_merged" => $filenamemerge
        ]);

        return Command::SUCCESS;
    }

}
