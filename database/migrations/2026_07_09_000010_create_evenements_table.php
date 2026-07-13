<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('type');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('lieu')->nullable();
            $table->decimal('cout', 15, 2)->default(0);
            $table->string('statut')->default('planifie');
            $table->text('description')->nullable();
            $table->foreignId('depense_id')->nullable()->constrained('depenses')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
