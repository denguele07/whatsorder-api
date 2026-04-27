<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            // === IDENTIFIANTS ===
            $table->id();
            $table->string('reference', 20)->unique();

            // === RELATION VENDEUR (propriétaire) ===
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // === INFOS CLIENT (dénormalisées) ===
            $table->string('client_name');
            $table->string('client_phone', 20);

            // === COMMANDE ===
            $table->string('product_description', 500);
            $table->unsignedInteger('amount');

            // === STATUT & DONNÉES OPTIONNELLES ===
            $table->string('status', 20)->default('nouveau');
            $table->text('notes')->nullable();

            // === TIMESTAMPS & SOFT DELETE ===
            $table->timestamps();
            $table->softDeletes();

            // === INDEX DE PERFORMANCE ===
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
