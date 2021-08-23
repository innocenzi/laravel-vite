<?php

namespace Innocenzi\Vite;

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

    public function bootingPackage()
    {
        $this->app->singleton(Vite::class, fn () => new Vite());

        $this->app->afterResolving('blade.compiler', function ($compiler) {
            $compiler->directive('vite', function ($entryName = null) {
                if (! $entryName) {
                    return '<?php echo vite_tags() ?>';
                }

                return sprintf('<?php echo vite_entry(e(%s)); ?>', $entryName);
            });

            $compiler->directive('client', function () {
                return '<?php echo vite_client(); ?>';
            });

            $compiler->directive('react', function () {
                return '<?php echo vite_react_refresh_runtime(); ?>';
            });
        });
    }
}
