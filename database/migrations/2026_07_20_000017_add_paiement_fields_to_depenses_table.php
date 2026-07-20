<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            if (! Schema::hasColumn('depenses', 'mode_paiement')) {
                $table->string('mode_paiement')->nullable()->after('statut');
            }
            if (! Schema::hasColumn('depenses', 'reste_a_payer')) {
                $table->decimal('reste_a_payer', 15, 2)->nullable()->after('mode_paiement');
            }
        });
    }

    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            if (Schema::hasColumn('depenses', 'reste_a_payer')) {
                $table->dropColumn('reste_a_payer');
            }
            if (Schema::hasColumn('depenses', 'mode_paiement')) {
                $table->dropColumn('mode_paiement');
            }
        });
    }
};
