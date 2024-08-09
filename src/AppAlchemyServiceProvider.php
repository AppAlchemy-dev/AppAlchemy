<?php

namespace AppAlchemy;

use AppAlchemy\Commands\TailwindConfigCommand;
use AppAlchemy\Http\Middleware\DetectAppAlchemy;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AppAlchemyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('appalchemy')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(TailwindConfigCommand::class);
    }

    public function packageBooted(): void
    {
        $this->registerBladeDirectives();

        $this->app->booted(function () {
            $this->registerViewComposer();

            try {
                $this->registerMiddleware();
            } catch (BindingResolutionException $e) {
                Log::warning('Failed to register AppAlchemy middleware: '.$e->getMessage());
            }
        });
    }

    public function packageRegistered(): void
    {
        $this->app->bind(AppAlchemy::class, function (Application $app) {
            return new AppAlchemy(
                $app['request'],
                $app['config']
            );
        });

        $this->app->alias(AppAlchemy::class, 'appalchemy');
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('alchemy', function () {
            return "<?php if (app('appalchemy')->isAppAlchemyApp()): ?>";
        });

        Blade::directive('endalchemy', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('nonalchemy', function () {
            return "<?php if (!app('appalchemy')->isAppAlchemyApp()): ?>";
        });

        Blade::directive('endnonalchemy', function () {
            return '<?php endif; ?>';
        });
    }

    /**
     * @throws BindingResolutionException
     */
    private function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('detect-appalchemy', DetectAppAlchemy::class);
    }

    private function registerViewComposer(): void
    {
        View::composer('*', function ($view) {
            $appAlchemy = app(AppAlchemy::class);
            $view->with('appAlchemyStyles', $appAlchemy->injectCustomStyles());
            $view->with('appAlchemyBridge', $appAlchemy->injectJavaScriptBridge());
        });
    }
}
