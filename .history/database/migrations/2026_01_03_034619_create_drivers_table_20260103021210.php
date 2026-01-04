<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('cpf', 11)->unique();
            $table->string('cnh', 11)->unique();
            $table->enum('cnh_category', ['A', 'B', 'AB', 'C', 'D', 'E']);
            $table->date('cnh_expires_at');
            $table->string('phone', 15);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            $table->index(['company_id', 'is_available']);
            $table->index('cpf');
            $table->index('cnh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
