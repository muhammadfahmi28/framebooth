<?php

namespace App\Console\Commands;

use App\Models\Photo;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class PhotoSync extends Command
{
    /** WIP !!
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photo:sync {--work}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('work')) {
            for ($i=0; true ; $i++) {
                $this->checkAndUpload();
                sleep(env("UPLOAD_WAIT", 5));
            }
            $this->line("and it ends...");
            return Command::SUCCESS;
        } else {
            if ($this->checkAndUpload()) {
                return Command::SUCCESS;
            };
        }
    }

    function checkAndUpload() : bool {
        $pending = Photo::with('tuser:id,uid')->whereNull('uploaded_at')->whereNull('failed_at')->orderBy("created_at", "ASC")->first();

        if ($pending == null) {
            $this->line("NO PENDING UPLOAD, SEARCHING PAST FAILED");
            $pending = Photo::with('tuser:id,uid')->whereNull('uploaded_at')->whereNotNull('failed_at')->orderBy("failed_at", "ASC")->first();
            if ($pending == null) {
                $this->line("NO FAILED UPLOAD");
                return true;
            }
        }

        $file_parts = [];

        try {
            $this->line("UPLOADING " . $pending->id . "...");

            $file_parts[] = [
                'name' => 'uid',
                'contents' => $pending->tuser->uid,
            ];

            $file_parts[] = [
                'name' => 'main',
                'contents' => file_get_contents($pending->getRealPath()),
                // 'contents' => $pending->getRealPath(),
                'filename' => $pending->filename
            ];

            // Inserting raw files
            $raws_paths = $pending->getRawsRealPath();
            foreach ($raws_paths as $key => $raw_path) {
                $file_parts[] = [
                    'name' => "raw[$key]",
                    'contents' => file_get_contents($raw_path),
                    // 'contents' => $raw_path,
                    'filename' => $pending->raws[$key]
                ];
            }

        } catch (\Exception $ex) {
            $this->line("FAILED TO LOAD IMAGES");

            $pending->update([
                "failed_at" => now(),
            ]);

            report($ex);

            return false;
        }

        // $this->line(dd($file_parts));
        // return true;

        try {
            $client = new Client();

            $res = $client->request("POST", env("MASTER_APP_URL") . "/api/photos/$pending->id/upload/", [
                "headers"=>[
                    "key" => env('API_KEY')
                ],
                "multipart" => $file_parts
            ]);

            if($res->getStatusCode() != 200) {
                $response = $res->getBody();
                $this->line("UPLOADING " . $pending->id . " FAILED : BAD RESPONSE");
                Log::error("Error Uploading " . $pending->id);
                Log::error($response);
                return false;
            }

        } catch (\Exception $ex) {
            $this->line("FAILED TO UPLOAD");

            $pending->update([
                "failed_at" => now(),
            ]);

            report($ex);
            return false;
        }

        // Set lagi just o make sure lol.
        $pending->update([
            "uploaded_at" => now(),
            "failed_at" => null
        ]);

        $this->line("UPLOADING " . $pending->id . " COMPLETE");
        return true;
    }

}
