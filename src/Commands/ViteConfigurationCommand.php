<?php

namespace Innocenzi\Vite\Commands;

use Illuminate\Console\Command;

class ViteConfigurationCommand extends Command
{
    public $signature = 'vite:config';
    public $description = 'Prints the Vite configuration.';
    public $hidden = true;

    public function handle()
    {
        echo json_encode(\config('vite'));
    }
}
