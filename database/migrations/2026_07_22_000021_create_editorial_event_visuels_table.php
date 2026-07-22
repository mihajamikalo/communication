<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_event_visuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('editorial_event_id')->constrained('editorial_events')->cascadeOnDelete();
            $table->string('path');
            $table->string('nom')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['editorial_event_id', 'position']);
        });

        $events = DB::table('editorial_events')
            ->whereNotNull('visuel_path')
            ->where('visuel_path', '!=', '')
            ->get(['id', 'visuel_path', 'visuel_nom']);

        foreach ($events as $event) {
            DB::table('editorial_event_visuels')->insert([
                'editorial_event_id' => $event->id,
                'path' => $event->visuel_path,
                'nom' => $event->visuel_nom,
                'position' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('editorial_event_visuels');
    }
};
