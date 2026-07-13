<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjetActivite;
use App\Models\ProjetCarte;
use App\Models\ProjetListe;
use App\Models\ProjetTableau;
use Illuminate\Http\Request;

class ProjetApiController extends Controller
{
    public function board()
    {
        $tableau = ProjetTableau::current();
        $tableau->load(['listes.cartes' => function ($q) {
            $q->with([
                'etiquettes',
                'membres',
                'checklists.items',
                'commentaires',
                'piecesJointes',
            ])->orderBy('position');
        }]);

        return response()->json([
            'tableau' => [
                'id' => $tableau->id,
                'nom' => $tableau->nom,
                'background_url' => $tableau->background_url,
            ],
            'listes' => $tableau->listes->map(function (ProjetListe $liste) {
                return [
                    'id' => $liste->id,
                    'nom' => $liste->nom,
                    'slug' => $liste->slug,
                    'position' => $liste->position,
                    'cartes' => $liste->cartes->map(fn (ProjetCarte $carte) => $this->cardSummary($carte)),
                ];
            }),
        ]);
    }

    public function show(ProjetCarte $projet)
    {
        $projet->load([
            'liste',
            'etiquettes',
            'membres',
            'checklists.items',
            'commentaires.user',
            'piecesJointes',
            'activites.user',
        ]);

        return response()->json($this->cardDetail($projet));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'projet_liste_id' => ['required', 'exists:projet_listes,id'],
        ]);

        $liste = ProjetListe::findOrFail($data['projet_liste_id']);
        $position = (int) ProjetCarte::where('projet_liste_id', $liste->id)->max('position') + 1;

        $carte = ProjetCarte::create([
            'titre' => $data['titre'],
            'projet_liste_id' => $liste->id,
            'position' => $position,
            'created_by' => $request->user()->id,
        ]);

        ProjetActivite::create([
            'projet_carte_id' => $carte->id,
            'user_id' => $request->user()->id,
            'message' => $request->user()->name.' a ajouté cette carte à '.$liste->nom,
        ]);

        return response()->json(['ok' => true, 'id' => $carte->id], 201);
    }

    public function update(Request $request, ProjetCarte $projet)
    {
        $data = $request->validate([
            'titre' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'projet_liste_id' => ['sometimes', 'exists:projet_listes,id'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date'],
        ]);

        $oldListeId = $projet->projet_liste_id;
        $projet->update($data);

        if (isset($data['projet_liste_id']) && (int) $data['projet_liste_id'] !== (int) $oldListeId) {
            $projet->load('liste');
            ProjetActivite::create([
                'projet_carte_id' => $projet->id,
                'user_id' => $request->user()->id,
                'message' => $request->user()->name.' a déplacé cette carte vers '.$projet->liste->nom,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function move(Request $request)
    {
        $data = $request->validate([
            'carte_id' => ['required', 'exists:projet_cartes,id'],
            'projet_liste_id' => ['required', 'exists:projet_listes,id'],
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:projet_cartes,id'],
        ]);

        $carte = ProjetCarte::findOrFail($data['carte_id']);
        $oldListeId = $carte->projet_liste_id;
        $liste = ProjetListe::findOrFail($data['projet_liste_id']);

        foreach ($data['ordered_ids'] as $index => $id) {
            ProjetCarte::where('id', $id)->update([
                'projet_liste_id' => $liste->id,
                'position' => $index,
            ]);
        }

        if ((int) $oldListeId !== (int) $liste->id) {
            ProjetActivite::create([
                'projet_carte_id' => $carte->id,
                'user_id' => $request->user()->id,
                'message' => $request->user()->name.' a déplacé cette carte vers '.$liste->nom,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function storeCommentaire(Request $request, ProjetCarte $projet)
    {
        $data = $request->validate([
            'contenu' => ['required', 'string'],
        ]);

        $commentaire = $projet->commentaires()->create([
            'user_id' => $request->user()->id,
            'contenu' => $data['contenu'],
        ]);

        return response()->json([
            'ok' => true,
            'commentaire' => [
                'id' => $commentaire->id,
                'contenu' => $commentaire->contenu,
                'user' => $request->user()->name,
                'initials' => $request->user()->initials(),
                'date' => $commentaire->created_at->locale('fr')->isoFormat('D MMM YYYY, HH:mm'),
            ],
        ], 201);
    }

    protected function cardSummary(ProjetCarte $carte): array
    {
        $progress = $carte->checklistProgress();

        return [
            'id' => $carte->id,
            'titre' => $carte->titre,
            'projet_liste_id' => $carte->projet_liste_id,
            'date_badge' => $carte->dateBadgeLabel(),
            'is_overdue' => $carte->isOverdue(),
            'is_done' => $carte->isDone(),
            'has_description' => filled($carte->description),
            'commentaires_count' => $carte->commentaires->count(),
            'pieces_count' => $carte->piecesJointes->count(),
            'checklist' => $progress,
            'etiquettes' => $carte->etiquettes->map(fn ($e) => [
                'id' => $e->id,
                'nom' => $e->nom,
                'couleur' => $e->couleur,
            ]),
            'membres' => $carte->membres->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'initials' => $u->initials(),
            ]),
        ];
    }

    protected function cardDetail(ProjetCarte $projet): array
    {
        return [
            'id' => $projet->id,
            'titre' => $projet->titre,
            'description' => $projet->description,
            'projet_liste_id' => $projet->projet_liste_id,
            'statut_label' => $projet->statut_label,
            'date_debut' => optional($projet->date_debut)->format('Y-m-d'),
            'date_fin' => optional($projet->date_fin)->format('Y-m-d'),
            'date_badge' => $projet->dateBadgeLabel(),
            'is_overdue' => $projet->isOverdue(),
            'is_done' => $projet->isDone(),
            'listes' => ProjetListe::orderBy('position')->get(['id', 'nom']),
            'etiquettes' => $projet->etiquettes->map(fn ($e) => [
                'id' => $e->id,
                'nom' => $e->nom,
                'couleur' => $e->couleur,
                'classes' => $e->classes,
            ]),
            'membres' => $projet->membres->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'initials' => $u->initials(),
            ]),
            'checklists' => $projet->checklists->map(fn ($c) => [
                'id' => $c->id,
                'titre' => $c->titre,
                'items' => $c->items->map(fn ($i) => [
                    'id' => $i->id,
                    'titre' => $i->titre,
                    'fait' => $i->fait,
                ]),
            ]),
            'commentaires' => $projet->commentaires->map(fn ($c) => [
                'id' => $c->id,
                'contenu' => $c->contenu,
                'user' => $c->user?->name,
                'initials' => $c->user?->initials(),
                'date' => $c->created_at->locale('fr')->isoFormat('D MMM YYYY, HH:mm'),
            ]),
            'pieces_jointes' => $projet->piecesJointes->map(fn ($p) => [
                'id' => $p->id,
                'nom' => $p->nom,
                'url' => $p->public_url,
            ]),
            'activites' => $projet->activites->map(fn ($a) => [
                'id' => $a->id,
                'message' => $a->message,
                'date' => $a->created_at->locale('fr')->isoFormat('D MMM YYYY'),
            ]),
            'checklist_progress' => $projet->checklistProgress(),
        ];
    }
}
