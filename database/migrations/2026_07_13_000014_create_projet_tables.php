<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projet_etiquettes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('couleur', 32); // yellow, blue, red, green, cyan, purple
            $table->timestamps();
        });

        Schema::create('projet_cartes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('statut', 40)->default('a_faire');
            $table->unsignedInteger('position')->default(0);
            $table->date('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['statut', 'position']);
        });

        Schema::create('projet_carte_etiquette', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_carte_id')->constrained('projet_cartes')->cascadeOnDelete();
            $table->foreignId('projet_etiquette_id')->constrained('projet_etiquettes')->cascadeOnDelete();
            $table->unique(['projet_carte_id', 'projet_etiquette_id'], 'carte_etiquette_unique');
        });

        Schema::create('projet_carte_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_carte_id')->constrained('projet_cartes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['projet_carte_id', 'user_id'], 'carte_user_unique');
        });

        Schema::create('projet_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_carte_id')->constrained('projet_cartes')->cascadeOnDelete();
            $table->string('titre')->default('Checklist');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('projet_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_checklist_id')->constrained('projet_checklists')->cascadeOnDelete();
            $table->string('titre');
            $table->boolean('fait')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('projet_commentaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_carte_id')->constrained('projet_cartes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('contenu');
            $table->timestamps();
        });

        Schema::create('projet_pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_carte_id')->constrained('projet_cartes')->cascadeOnDelete();
            $table->string('nom');
            $table->string('path')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('projet_activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_carte_id')->constrained('projet_cartes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_activites');
        Schema::dropIfExists('projet_pieces_jointes');
        Schema::dropIfExists('projet_commentaires');
        Schema::dropIfExists('projet_checklist_items');
        Schema::dropIfExists('projet_checklists');
        Schema::dropIfExists('projet_carte_user');
        Schema::dropIfExists('projet_carte_etiquette');
        Schema::dropIfExists('projet_cartes');
        Schema::dropIfExists('projet_etiquettes');
    }
};
