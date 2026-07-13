<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->string('fournisseur');
            $table->string('objet');
            $table->string('campagne')->nullable();
            $table->decimal('montant', 15, 2);
            $table->string('statut')->default('en_attente');
            $table->string('categorie')->nullable();
            $table->date('date_depense');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
