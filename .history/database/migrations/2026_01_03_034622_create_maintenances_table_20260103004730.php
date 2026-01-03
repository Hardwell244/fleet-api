<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['preventive', 'corrective', 'inspection']);
            $table->text('description');
            $table->decimal('cost', 10, 2)->default(0);
            $table->date('scheduled_date');
            $table->dateTime('completed_at')->nullable();
            $table->string('workshop')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
