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

            // DROP COLUMN natif indisponible sur SQLite < 3.35 sans doctrine/dbal :
            // on laisse la colonne obsolète (nullable, inutilisée) au lieu de crasher.
            if ($this->canDropColumns()) {
                Schema::table('fournisseurs', function (Blueprint $table) {
                    $table->dropColumn('adresse');
                });
            }
        }
    }

    private function canDropColumns(): bool
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            return true;
        }

        if (class_exists('Doctrine\DBAL\Connection')) {
            return true;
        }

        $version = DB::selectOne('select sqlite_version() as v')->v ?? '0';

        return version_compare($version, '3.35.0', '>=');
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
