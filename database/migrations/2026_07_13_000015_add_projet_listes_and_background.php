<?php

use App\Models\ProjetCarte;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('projet_tableaux')) {
            Schema::create('projet_tableaux', function (Blueprint $table) {
                $table->id();
                $table->string('nom')->default('Communication');
                $table->string('background_path')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('projet_listes')) {
            Schema::create('projet_listes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('projet_tableau_id')->constrained('projet_tableaux')->cascadeOnDelete();
                $table->string('nom');
                $table->string('slug', 60)->nullable();
                $table->unsignedInteger('position')->default(0);
                $table->timestamps();
                $table->index(['projet_tableau_id', 'position']);
            });
        }

        $tableauId = DB::table('projet_tableaux')->value('id');
        if (! $tableauId) {
            $tableauId = DB::table('projet_tableaux')->insertGetId([
                'nom' => 'Communication',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $listeIds = DB::table('projet_listes')->pluck('id', 'slug')->all();
        if (count($listeIds) === 0) {
            $position = 0;
            foreach (ProjetCarte::STATUTS as $slug => $nom) {
                $listeIds[$slug] = DB::table('projet_listes')->insertGetId([
                    'projet_tableau_id' => $tableauId,
                    'nom' => $nom,
                    'slug' => $slug,
                    'position' => $position++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (! Schema::hasColumn('projet_cartes', 'projet_liste_id')) {
            Schema::table('projet_cartes', function (Blueprint $table) {
                $table->foreignId('projet_liste_id')->nullable()->after('id')->constrained('projet_listes')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('projet_cartes', 'statut')) {
            $cartes = DB::table('projet_cartes')->select('id', 'statut', 'projet_liste_id')->get();
            foreach ($cartes as $carte) {
                if ($carte->projet_liste_id) {
                    continue;
                }
                $listeId = $listeIds[$carte->statut] ?? ($listeIds['a_faire'] ?? reset($listeIds) ?: null);
                if ($listeId) {
                    DB::table('projet_cartes')->where('id', $carte->id)->update([
                        'projet_liste_id' => $listeId,
                    ]);
                }
            }

            // SQLite: recreate table to drop statut + old index cleanly
            Schema::table('projet_cartes', function (Blueprint $table) {
                $table->dropIndex(['statut', 'position']);
            });

            Schema::table('projet_cartes', function (Blueprint $table) {
                $table->dropColumn('statut');
            });
        }

        $indexes = collect(DB::select("PRAGMA index_list('projet_cartes')"))->pluck('name');
        if (! $indexes->contains('projet_cartes_projet_liste_id_position_index')) {
            Schema::table('projet_cartes', function (Blueprint $table) {
                $table->index(['projet_liste_id', 'position']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('projet_cartes', 'statut')) {
            Schema::table('projet_cartes', function (Blueprint $table) {
                $table->string('statut', 40)->default('a_faire')->after('description');
            });
        }

        $listes = DB::table('projet_listes')->pluck('slug', 'id');
        foreach (DB::table('projet_cartes')->select('id', 'projet_liste_id')->get() as $carte) {
            DB::table('projet_cartes')->where('id', $carte->id)->update([
                'statut' => $listes[$carte->projet_liste_id] ?? 'a_faire',
            ]);
        }

        if (Schema::hasColumn('projet_cartes', 'projet_liste_id')) {
            Schema::table('projet_cartes', function (Blueprint $table) {
                $table->dropConstrainedForeignId('projet_liste_id');
            });
        }

        Schema::dropIfExists('projet_listes');
        Schema::dropIfExists('projet_tableaux');
    }
};
