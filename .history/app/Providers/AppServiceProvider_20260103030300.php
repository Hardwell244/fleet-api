<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind VehicleRepository
        $this->app->bind(
            \App\Repositories\VehicleRepository::class,
            \App\Repositories\VehicleRepository::class
        );

        // Bind VehicleService
        $this->app->bind(
            \App\Services\VehicleService::class,
            function ($app) {
                return new \App\Services\VehicleService(
                    $app->make(\App\Repositories\VehicleRepository::class)
                );
            }
        );

        // Bind DriverRepository
        $this->app->bind(
            \App\Repositories\DriverRepository::class,
            \App\Repositories\DriverRepository::class
        );

        // Bind DriverService
        $this->app->bind(
            \App\Services\DriverService::class,
            function ($app) {
                return new \App\Services\DriverService(
                    $app->make(\App\Repositories\DriverRepository::class)
                );
            }
        );

        // Bind MaintenanceRepository
        $this->app->bind(
            \App\Repositories\Contracts\MaintenanceRepositoryInterface::class,
            \App\Repositories\MaintenanceRepository::class
        );

        // Bind MaintenanceService
        $this->app->bind(
            \App\Services\MaintenanceService::class,
            function ($app) {
                return new \App\Services\MaintenanceService(
                    $app->make(\App\Repositories\Contracts\MaintenanceRepositoryInterface::class)
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
