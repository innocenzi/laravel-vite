<?php

namespace Innocenzi\Vite;

use Illuminate\Support\Facades\Blade;
use Innocenzi\Vite\Commands\ExportConfigurationCommand;
use Innocenzi\Vite\Commands\GenerateAliasesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ViteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-vite')
            ->hasConfigFile()
            ->hasCommand(ExportConfigurationCommand::class)
            ->hasCommand(GenerateAliasesCommand::class);
    }

    public function registeringPackage()
    {
        $this->app->singleton(Vite::class, fn () => new Vite());

        Blade::directive('vite', function ($entryName = null) {
            if (! $entryName) {
                return '<?php echo vite_tags() ?>';
            }

            return sprintf('<?php echo vite_entry(e(%s)); ?>', $entryName);
        });

        Blade::directive('client', function () {
            return '<?php echo vite_client(); ?>';
        });

        Blade::directive('react', function () {
            return '<?php echo vite_react_refresh_runtime(); ?>';
        });
    }
}
