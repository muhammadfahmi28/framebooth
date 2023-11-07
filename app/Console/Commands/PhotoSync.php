<?php

namespace App\Console\Commands;

use App\Models\Photo;
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
                sleep(2);
            }
            $this->line("and it ends...");
            return Command::SUCCESS;
        } else {
            if ($this->checkAndUpload()) {
                return Command::SUCCESS;
            };
        }
    }

    function checkAndPrint() : bool {
        $pending = Photo::whereNull('printed_at')->orderBy("created_at", "ASC")->first();
        if ($pending == null) {
            $this->line("NO PENDING UPLOAD");
            return true;
        }


        return false;
    }

}
