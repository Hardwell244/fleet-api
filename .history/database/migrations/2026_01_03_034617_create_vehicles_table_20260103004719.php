<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('plate', 7)->unique();
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->year('year');
            $table->enum('type', ['car', 'motorcycle', 'truck', 'van'])->default('car');
            $table->enum('status', ['available', 'in_use', 'maintenance', 'inactive'])->default('available');
            $table->decimal('fuel_capacity', 8, 2)->nullable();
            $table->decimal('current_km', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index('plate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
