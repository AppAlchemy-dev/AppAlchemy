<?php

namespace AppAlchemy;

use AppAlchemy\Commands\TailwindConfigCommand;
use AppAlchemy\Http\Middleware\AppAlchemyAuthMiddleware;
use AppAlchemy\Http\Middleware\DetectAppAlchemy;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
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
        $this->app[Kernel::class]->pushMiddleware(AppAlchemyAuthMiddleware::class);

        $this->registerBladeDirectives();

        if (! class_exists('AddApiTokenToUsersTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/add_api_token_to_users_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_add_api_token_to_users_table.php'),
            ], 'appalchemy-migrations');
        }

        $this->publishes([
            __DIR__.'/../src/Traits/AppAlchemyUser.php' => app_path('Models/Traits/AppAlchemyUser.php'),
        ], 'appalchemy-traits');

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
                request: $app['request'],
                config: $app['config'],
                auth: $app['auth'],
            );
        });

        $this->app->alias(AppAlchemy::class, 'appalchemy');
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('appAlchemyStyles', function () {
            return "<?php echo app(\AppAlchemy\AppAlchemy::class)->injectCustomStyles(); ?>";
        });

        Blade::directive('appAlchemyScripts', function () {
            return "<?php echo app(\AppAlchemy\AppAlchemy::class)->injectJavaScriptBridge(); ?>";
        });

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
