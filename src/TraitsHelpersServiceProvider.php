<?php

namespace GIS\TraitsHelpers;

use GIS\TraitsHelpers\Commands\RenameMorphType;
use GIS\TraitsHelpers\Helpers\BuilderActionsManager;
use GIS\TraitsHelpers\Helpers\DateHelper;
use Illuminate\Support\ServiceProvider;

class TraitsHelpersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

    }

    public function register(): void
    {
        $this->app->bind("date_helper", function () {
            return app(DateHelper::class);
        });

        $this->app->singleton("builder-actions", function () {
            return new BuilderActionsManager;
        });

        // Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                RenameMorphType::class
            ]);
        }
    }
}
