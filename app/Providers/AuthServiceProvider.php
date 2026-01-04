<?php

namespace App\Providers;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Maintenance;
use App\Models\Delivery;
use App\Policies\VehiclePolicy;
use App\Policies\DriverPolicy;
use App\Policies\MaintenancePolicy;
use App\Policies\DeliveryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Vehicle::class => VehiclePolicy::class,
        Driver::class => DriverPolicy::class,
        Maintenance::class => MaintenancePolicy::class,
        Delivery::class => DeliveryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
