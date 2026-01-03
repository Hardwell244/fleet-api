<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tracking_code', 20)->unique();
            $table->enum('status', [
                'pending',
                'assigned',
                'in_transit',
                'delivered',
                'failed',
                'cancelled'
            ])->default('pending');

            // Origem
            $table->string('origin_address');
            $table->decimal('origin_lat', 10, 7);
            $table->decimal('origin_lng', 10, 7);

            // Destino
            $table->string('destination_address');
            $table->decimal('destination_lat', 10, 7);
            $table->decimal('destination_lng', 10, 7);

            // Métricas
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->integer('estimated_time_minutes')->nullable();

            // Dados do cliente
            $table->string('recipient_name');
            $table->string('recipient_phone', 15);

            // Controle
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('picked_up_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->string('signature_url')->nullable();
            $table->string('photo_url')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index('tracking_code');
            $table->index(['origin_lat', 'origin_lng']);
            $table->index(['destination_lat', 'destination_lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
