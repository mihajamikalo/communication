<?php

namespace App\Http\Controllers;

use App\Models\ProjetActivite;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index(Request $request)
    {
        $activites = ProjetActivite::with(['user', 'carte.liste'])
            ->orderByDesc('created_at')
            ->paginate(40)
            ->withQueryString();

        return view('activites.index', [
            'title' => 'Activité',
            'subtitle' => 'Historique des actions sur la gestion de projet',
            'activites' => $activites,
        ]);
    }
}
