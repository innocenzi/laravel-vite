<?php

namespace Innocenzi\Vite\Commands;

use Illuminate\Console\Command;

class ExportConfigurationCommand extends Command
{
    public $signature = 'vite:config';
    public $description = 'Prints the Vite configuration.';
    public $hidden = true;

    public function handle()
    {
        $this->output->write(json_encode(\config('vite')));
    }
}
