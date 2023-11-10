<?php

namespace App\Console\Commands;

use App\Models\Photo;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ServerPing extends Command
{
    /** WIP !!
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:ping';

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
        try {
            $client = new Client();

            $res = $client->request("POST", env("MASTER_APP_URL") . "/api/ping", [
                'verify' => false,
                "headers"=>[
                    "key" => env('API_KEY')
                ]
            ]);

            if($res->getStatusCode() != 200) {
                $response = $res->getBody();
                $this->line("FAILED : BAD RESPONSE");
                Log::error($response);
                return false;
            }

        } catch (\Exception $ex) {
            $this->line("FAILED ");

            // Log::debug(json_encode($file_parts));

            report($ex);
            return false;
        }
    }

}
