<?php

namespace Zenit\ApplicationAPIKeys\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ApplicationAPIKeysPluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware(['api', 'application-api', 'throttle:api.application'])
            ->prefix('/api/application')
            ->scopeBindings()
            ->group(plugin_path('application-api-keys', 'routes', 'api-application.php'));
    }
}
