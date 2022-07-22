<?php

namespace Innocenzi\Vite;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\View\Compilers\BladeCompiler;
use Innocenzi\Vite\Commands\ExportConfigurationCommand;
use Innocenzi\Vite\Commands\UpdateTsconfigCommand;
use Innocenzi\Vite\EntrypointsFinder\DefaultEntrypointsFinder;
use Innocenzi\Vite\EntrypointsFinder\EntrypointsFinder;
use Innocenzi\Vite\HeartbeatCheckers\HeartbeatChecker;
use Innocenzi\Vite\HeartbeatCheckers\HttpHeartbeatChecker;
use Innocenzi\Vite\TagGenerators\CallbackTagGenerator;
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
            ->hasCommand(ExportConfigurationCommand::class)
            ->hasCommand(UpdateTsconfigCommand::class);
    }

    public function registeringPackage()
    {
        $this->registerBindings();
        $this->registerDirectives();
        $this->registerAbout();
    }

    protected function registerBindings()
    {
        $this->app->singleton(Vite::class, fn () => new Vite());

        $this->app->bind(EntrypointsFinder::class, config('vite.interfaces.entrypoints_finder', DefaultEntrypointsFinder::class));
        $this->app->bind(HeartbeatChecker::class, config('vite.interfaces.heartbeat_checker', HttpHeartbeatChecker::class));
        $this->app->bind(TagGenerator::class, config('vite.interfaces.tag_generator', CallbackTagGenerator::class));
    }

    protected function registerDirectives()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $compiler) {
            /**
             * @vite
             * @vite('configName')
             */
            $compiler->directive('vite', function ($expression = null) {
                return sprintf(
                    '<?php echo vite_tags(e(%s)); ?>',
                    $expression ?: '"' . config('vite.default') . '"'
                );
            });
            
            /**
             * @tag('entry')
             * @tag('entry', 'configName')
             */
            $compiler->directive('tag', function ($expression = null) {
                $args = collect(explode(',', $expression))->map(fn ($str) => trim($str));

                if (!\in_array(\count($args), [1, 2])) {
                    throw new InvalidArgumentException('The @tag directive accepts one or two arguments, ' . \count($args) . ' given.');
                }
                
                [$entryName, $configName] = $args->toArray() + ['', '"' . config('vite.default') . '"'];
                
                return sprintf(
                    '<?php echo vite_tag(e(%s), e(%s)); ?>',
                    $entryName,
                    $configName
                );
            });

            $compiler->directive('client', function ($expression = null) {
                return sprintf(
                    '<?php echo vite_client(e(%s)); ?>',
                    $expression ?: '"' . config('vite.default') . '"'
                );
            });

            $compiler->directive('react', function ($expression = null) {
                return sprintf(
                    '<?php echo vite_react_refresh_runtime(e(%s)); ?>',
                    $expression ?: '"' . config('vite.default') . '"'
                );
            });
        });
    }

    protected function registerAbout()
    {
        if (!class_exists(AboutCommand::class)) {
            return;
        }

        $default = config('vite.default');
        $configurations = array_diff(array_keys(config('vite.configs', [])), [$default]);

        AboutCommand::add('Vite', [
            'Version' => InstalledVersions::getPrettyVersion('innocenzi/laravel-vite'),
            'Configurations' => implode(' <fg=gray>/</> ', ["<fg=yellow;options=bold>{$default}</>", ...$configurations]),
            'Environment variable prefixes' => implode(' <fg=gray>/</> ', config('vite.env_prefixes', [])),
        ]);
    }
}
