<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campagnes', function (Blueprint $table) {
            $table->string('objectif')->nullable()->after('nom');
            $table->foreignId('depense_id')->nullable()->after('statut')->constrained('depenses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('campagnes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('depense_id');
            $table->dropColumn('objectif');
        });
    }
};
