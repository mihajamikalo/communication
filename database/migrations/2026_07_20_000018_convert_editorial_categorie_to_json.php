<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('editorial_events')->select('id', 'categorie')->get();

        foreach ($rows as $row) {
            $raw = $row->categorie;
            $decoded = json_decode((string) $raw, true);

            if (is_array($decoded)) {
                continue;
            }

            DB::table('editorial_events')->where('id', $row->id)->update([
                'categorie' => json_encode($raw ? [$raw] : []),
            ]);
        }
    }

    public function down(): void
    {
        $rows = DB::table('editorial_events')->select('id', 'categorie')->get();

        foreach ($rows as $row) {
            $decoded = json_decode((string) $row->categorie, true);

            if (! is_array($decoded)) {
                continue;
            }

            DB::table('editorial_events')->where('id', $row->id)->update([
                'categorie' => $decoded[0] ?? 'facebook',
            ]);
        }
    }
};
