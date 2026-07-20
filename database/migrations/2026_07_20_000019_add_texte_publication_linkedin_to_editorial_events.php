<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('editorial_events', function (Blueprint $table) {
            if (! Schema::hasColumn('editorial_events', 'texte_publication_linkedin')) {
                $table->text('texte_publication_linkedin')->nullable()->after('texte_publication');
            }
        });
    }

    public function down(): void
    {
        Schema::table('editorial_events', function (Blueprint $table) {
            if (Schema::hasColumn('editorial_events', 'texte_publication_linkedin')) {
                $table->dropColumn('texte_publication_linkedin');
            }
        });
    }
};
