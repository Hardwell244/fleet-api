<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Adiciona indexes para otimização de queries
     */
    public function up(): void
    {
        // Indexes para vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            $table->index('status', 'idx_vehicles_status');
            $table->index(['company_id', 'status'], 'idx_vehicles_company_status');
        });

        // Indexes para drivers
        Schema::table('drivers', function (Blueprint $table) {
            $table->index('is_available', 'idx_drivers_is_available');
            $table->index(['company_id', 'is_available'], 'idx_drivers_company_available');
        });

        // Indexes para deliveries
        Schema::table('deliveries', function (Blueprint $table) {
            $table->index('status', 'idx_deliveries_status');
            $table->index('tracking_code', 'idx_deliveries_tracking_code');
            $table->index(['company_id', 'status'], 'idx_deliveries_company_status');
            $table->index('driver_id', 'idx_deliveries_driver_id');
            $table->index('vehicle_id', 'idx_deliveries_vehicle_id');
        });

        // Indexes para maintenances
        Schema::table('maintenances', function (Blueprint $table) {
            $table->index('status', 'idx_maintenances_status');
            $table->index(['company_id', 'status'], 'idx_maintenances_company_status');
            $table->index('vehicle_id', 'idx_maintenances_vehicle_id');
        });

        // Indexes para delivery_events
        Schema::table('delivery_events', function (Blueprint $table) {
            $table->index('created_at', 'idx_delivery_events_created_at');
        });

        // Indexes para companies
        Schema::table('companies', function (Blueprint $table) {
            $table->index('is_active', 'idx_companies_is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex('idx_vehicles_status');
            $table->dropIndex('idx_vehicles_company_status');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex('idx_drivers_is_available');
            $table->dropIndex('idx_drivers_company_available');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex('idx_deliveries_status');
            $table->dropIndex('idx_deliveries_tracking_code');
            $table->dropIndex('idx_deliveries_company_status');
            $table->dropIndex('idx_deliveries_driver_id');
            $table->dropIndex('idx_deliveries_vehicle_id');
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropIndex('idx_maintenances_status');
            $table->dropIndex('idx_maintenances_company_status');
            $table->dropIndex('idx_maintenances_vehicle_id');
        });

        Schema::table('delivery_events', function (Blueprint $table) {
            $table->dropIndex('idx_delivery_events_created_at');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex('idx_companies_is_active');
        });
    }
};
