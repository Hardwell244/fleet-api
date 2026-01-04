<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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

        // Bind DeliveryRepository
        $this->app->bind(
            \App\Repositories\Contracts\DeliveryRepositoryInterface::class,
            \App\Repositories\DeliveryRepository::class
        );

        // Bind DeliveryService
        $this->app->bind(
            \App\Services\DeliveryService::class,
            function ($app) {
                return new \App\Services\DeliveryService(
                    $app->make(\App\Repositories\Contracts\DeliveryRepositoryInterface::class)
                );
            }
        );

        // Bind DeliveryEventRepository
        $this->app->bind(
            \App\Repositories\Contracts\DeliveryEventRepositoryInterface::class,
            \App\Repositories\DeliveryEventRepository::class
        );

        // Bind DeliveryEventService
        $this->app->bind(
            \App\Services\DeliveryEventService::class,
            function ($app) {
                return new \App\Services\DeliveryEventService(
                    $app->make(\App\Repositories\Contracts\DeliveryEventRepositoryInterface::class)
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forçar HTTPS em produção
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
