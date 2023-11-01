<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PhotosPrint extends Command
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
        // Signal Print
        // Set printed at

        return Command::SUCCESS;
    }
}
