<?php

namespace Innocenzi\Vite;

use Illuminate\Support\Facades\Blade;
use Innocenzi\Vite\Commands\ViteCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ViteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-vite')
            ->hasConfigFile();
        // ->hasCommand(ViteCommand::class);
    }

    public function registeringPackage()
    {
        Blade::directive('vite', function ($entryName = null) {
            if (! $entryName) {
                return sprintf('<?php echo %s::read()->toHtml(); ?>', Manifest::class);
            }

            return sprintf(
                '<?php echo %s::read()->getEntry(e(%s))->toHtml(); ?>',
                Manifest::class,
                empty($entryName) ? '"resources/scripts/main.ts"' : $entryName
            );
        });
    }
}
