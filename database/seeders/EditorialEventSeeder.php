<?php

namespace Database\Seeders;

use App\Models\EditorialEvent;
use Illuminate\Database\Seeder;

class EditorialEventSeeder extends Seeder
{
    public function run(): void
    {
        if (EditorialEvent::query()->exists()) {
            return;
        }

        $editorialEvents = [
            [
                'titre' => 'Post Facebook MBA — Témoignage alumni',
                'categorie' => ['facebook'],
                'type_contenu' => 'FI',
                'booster' => true,
                'date_debut' => '2026-07-06',
                'date_fin' => '2026-07-12',
                'statut' => 'planifie',
                'valide' => false,
                'texte_publication' => 'Découvrez le parcours de nos alumni MBA. Une réussite inspirante à partager !',
            ],
            [
                'titre' => 'Story Instagram Portes Ouvertes',
                'categorie' => ['instagram'],
                'type_contenu' => 'FI',
                'booster' => false,
                'date_debut' => '2026-07-08',
                'statut' => 'publie',
                'valide' => true,
                'texte_publication' => 'J-10 avant les Portes Ouvertes ESCM. Inscrivez-vous dès maintenant.',
            ],
            [
                'titre' => 'Carrousel LinkedIn Bachelor Digital',
                'categorie' => ['linkedin'],
                'type_contenu' => 'FP',
                'booster' => false,
                'date_debut' => '2026-07-07',
                'statut' => 'planifie',
                'valide' => false,
                'texte_publication' => 'Le Bachelor Digital forme les talents de demain. Découvrez le programme.',
            ],
            [
                'titre' => 'TikTok — Vie étudiante campus',
                'categorie' => ['tiktok'],
                'type_contenu' => 'FI',
                'booster' => false,
                'date_debut' => '2026-07-11',
                'statut' => 'planifie',
                'valide' => false,
                'texte_publication' => 'Une journée type à ESCM en 30 secondes. #VieEtudiante',
            ],
            [
                'titre' => 'SMS Befiana — Relance Portes Ouvertes',
                'categorie' => ['befiana_sms'],
                'type_contenu' => 'FP',
                'booster' => false,
                'date_debut' => '2026-07-14',
                'statut' => 'planifie',
                'valide' => true,
                'texte_publication' => 'ESCM : Portes Ouvertes ce samedi. Infos et inscription sur escm.mg',
            ],
            [
                'titre' => 'Newsletter Brevo — Actualités juillet',
                'categorie' => ['brevo'],
                'type_contenu' => 'FP',
                'booster' => false,
                'date_debut' => '2026-07-09',
                'statut' => 'en_cours',
                'valide' => false,
                'texte_publication' => 'Votre newsletter ESCM de juillet : programmes, événements et actualités campus.',
            ],
            [
                'titre' => 'Facebook Ads — Boost Master Finance',
                'categorie' => ['facebook'],
                'type_contenu' => 'FI',
                'booster' => true,
                'date_debut' => '2026-07-10',
                'date_fin' => '2026-07-20',
                'statut' => 'en_cours',
                'valide' => true,
                'texte_publication' => 'Master Finance : candidatures ouvertes. Boost campagne Meta Ads.',
            ],
            [
                'titre' => 'Instagram Reel Master Finance',
                'categorie' => ['instagram'],
                'type_contenu' => 'FI',
                'booster' => false,
                'date_debut' => '2026-07-16',
                'statut' => 'planifie',
                'valide' => false,
                'texte_publication' => '3 raisons de choisir le Master Finance ESCM.',
            ],
        ];

        foreach ($editorialEvents as $event) {
            EditorialEvent::create($event);
        }
    }
}
