<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->string('service')->nullable()->after('email');
        });

        if (Schema::hasColumn('fournisseurs', 'adresse')) {
            foreach (DB::table('fournisseurs')->orderBy('id')->get() as $row) {
                DB::table('fournisseurs')
                    ->where('id', $row->id)
                    ->update(['service' => $row->adresse]);
            }

            Schema::table('fournisseurs', function (Blueprint $table) {
                $table->dropColumn('adresse');
            });
        }
    }

    public function down(): void
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->text('adresse')->nullable()->after('email');
        });

        if (Schema::hasColumn('fournisseurs', 'service')) {
            foreach (DB::table('fournisseurs')->orderBy('id')->get() as $row) {
                DB::table('fournisseurs')
                    ->where('id', $row->id)
                    ->update(['adresse' => $row->service]);
            }

            Schema::table('fournisseurs', function (Blueprint $table) {
                $table->dropColumn('service');
            });
        }
    }
};
