<?php

namespace Innocenzi\Vite\Commands;

use Illuminate\Console\Command;

class ScaffoldViteCommand extends Command
{
    public $signature = 'vite:setup';
    public $description = 'Generates a Vite configuration file.';
    public $hidden = true;

    public function handle()
    {
        $this->comment('All done');
    }
}
