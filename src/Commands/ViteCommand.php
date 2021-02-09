<?php

namespace Innocenzi\Vite\Commands;

use Illuminate\Console\Command;

class ViteCommand extends Command
{
    public $signature = 'laravel-vite';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
