<?php

namespace Innocenzi\Vite;

use Illuminate\View\Compilers\BladeCompiler;
use Innocenzi\Vite\Commands\ExportConfigurationCommand;
use Innocenzi\Vite\EntrypointsFinder\DefaultEntrypointsFinder;
use Innocenzi\Vite\EntrypointsFinder\EntrypointsFinder;
use Innocenzi\Vite\ServerCheckers\HttpServerChecker;
use Innocenzi\Vite\ServerCheckers\ServerChecker;
use Innocenzi\Vite\TagGenerators\DefaultTagGenerator;
use Innocenzi\Vite\TagGenerators\TagGenerator;
use InvalidArgumentException;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ViteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-vite')
            ->hasConfigFile()
            ->hasCommand(ExportConfigurationCommand::class);
    }

    public function registeringPackage()
    {
        $this->registerBindings();
        $this->registerDirectives();
    }

    protected function registerBindings()
    {
        $this->app->singleton(Vite::class, fn () => new Vite());

        $this->app->bind(EntrypointsFinder::class, config('vite.entrypoints_finder', DefaultEntrypointsFinder::class));
        $this->app->bind(ServerChecker::class, config('vite.server_checker', HttpServerChecker::class));
        $this->app->bind(TagGenerator::class, config('vite.tag_generator', DefaultTagGenerator::class));
    }

    protected function registerDirectives()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $compiler) {
            /**
             * @vite
             * @vite('configName')
             */
            $compiler->directive('vite', function ($expression) {
                return sprintf(
                    '<?php echo vite_tags(e(%s)); ?>',
                    $expression ?: '"' . config('vite.default') . '"'
                );
            });
            
            /**
             * @tag('entry')
             * @tag('entry', 'configName')
             */
            $compiler->directive('tag', function ($expression) {
                $args = collect(explode(',', $expression))->map(fn ($str) => trim($str));

                if (! \in_array(\count($args), [1, 2])) {
                    throw new InvalidArgumentException('The @tag directive accepts one or two arguments, ' . \count($args) . ' given.');
                }
                
                [$entryName, $configName] = $args;
                
                return sprintf(
                    '<?php echo vite_tag(e(%s), e(%s)); ?>',
                    $entryName,
                    $configName ?: '"' . config('vite.default') . '"'
                );
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
