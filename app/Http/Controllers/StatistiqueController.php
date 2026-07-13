<?php

namespace App\Http\Controllers;

use App\Services\MetaInsightsService;

class StatistiqueController extends Controller
{
    public function index(MetaInsightsService $meta)
    {
        $data = $meta->dashboard();

        $title = 'Statistiques';
        $subtitle = $data['api_connected']
            ? 'Engagement Facebook & Instagram — données Meta Graph API'
            : 'Engagement Facebook & Instagram — connexion API à configurer';

        return view('statistiques.index', [
            'title' => $title,
            'subtitle' => $subtitle,
            'topPosts' => $data['topPosts'],
            'engagementMensuel' => $data['engagementMensuel'],
            'kpis' => $data['kpis'],
            'apiError' => $data['error'] ?? null,
        ]);
    }
}
