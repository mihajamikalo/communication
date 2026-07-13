<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('editorial_events', function (Blueprint $table) {
            $table->string('visuel_path')->nullable()->after('texte_publication');
            $table->string('visuel_nom')->nullable()->after('visuel_path');
        });
    }

    public function down(): void
    {
        Schema::table('editorial_events', function (Blueprint $table) {
            $table->dropColumn(['visuel_path', 'visuel_nom']);
        });
    }
};
