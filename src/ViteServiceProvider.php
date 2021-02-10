<?php

namespace Innocenzi\Vite;

use Illuminate\Support\Facades\Blade;
use Innocenzi\Vite\Commands\ViteConfigurationCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ViteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-vite')
            ->hasConfigFile()
            ->hasCommand(ViteConfigurationCommand::class);
    }

    public function registeringPackage()
    {
        $this->app->singleton(Vite::class, fn () => new Vite());

        Blade::directive('vite', function ($entryName = null) {
            if (! $entryName) {
                return sprintf('<?php echo vite_client() ?>');
            }

            return sprintf('<?php echo vite_entry(e(%s)); ?>', $entryName);
        });
    }
}
