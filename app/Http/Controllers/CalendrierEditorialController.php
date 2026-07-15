<?php

namespace App\Http\Controllers;

use App\Models\EditorialEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CalendrierEditorialController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', '2weeks');
        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : now()->startOfDay();

        [$rangeStart, $rangeEnd] = $this->resolveRange($date, $view);

        $events = EditorialEvent::query()
            ->whereDate('date_debut', '<=', $rangeEnd)
            ->whereRaw('COALESCE(date_fin, date_debut) >= ?', [$rangeStart->toDateString()])
            ->orderBy('date_debut')
            ->get()
            ->map(fn (EditorialEvent $event) => $this->mapEvent($event));

        $categories = collect(EditorialEvent::CATEGORIES)->map(fn ($meta, $key) => [
            'key' => $key,
            'label' => $meta['label'],
            'color_name' => $meta['color_name'],
            'color' => $meta['color'],
        ])->values();

        $title = 'Calendrier éditorial';
        $subtitle = 'Planification des contenus et actions de communication';

        return view('calendrier-editorial.index', [
            'title' => $title,
            'subtitle' => $subtitle,
            'view' => $view,
            'currentDate' => $date->toDateString(),
            'rangeStart' => $rangeStart->toDateString(),
            'rangeEnd' => $rangeEnd->toDateString(),
            'rangeLabel' => $this->formatRangeLabel($rangeStart, $rangeEnd),
            'events' => $events,
            'categories' => $categories,
            'moisLabel' => $date->locale('fr')->isoFormat('MMMM YYYY'),
            'statuts' => [
                'planifie' => 'Planifié',
                'en_cours' => 'En cours',
                'publie' => 'Publié',
                'annule' => 'Annulé',
            ],
            'typesContenu' => EditorialEvent::TYPES_CONTENU,
            'canValidate' => auth()->user()?->canValidateEditorial() ?? false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);

        if ($request->hasFile('visuel')) {
            $file = $request->file('visuel');
            $validated['visuel_path'] = $file->store('editorial-visuels', 'public');
            $validated['visuel_nom'] = $file->getClientOriginalName();
        }

        EditorialEvent::create($validated);

        return $this->redirectToCalendar($request)
            ->with('success', 'Contenu ajouté au calendrier.');
    }

    public function update(Request $request, EditorialEvent $editorialEvent)
    {
        $validated = $this->validateEvent($request);

        if ($request->hasFile('visuel')) {
            if ($editorialEvent->visuel_path) {
                Storage::disk('public')->delete($editorialEvent->visuel_path);
            }

            $file = $request->file('visuel');
            $validated['visuel_path'] = $file->store('editorial-visuels', 'public');
            $validated['visuel_nom'] = $file->getClientOriginalName();
        } elseif ($request->boolean('remove_visuel')) {
            if ($editorialEvent->visuel_path) {
                Storage::disk('public')->delete($editorialEvent->visuel_path);
            }

            $validated['visuel_path'] = null;
            $validated['visuel_nom'] = null;
        }

        $editorialEvent->update($validated);

        return $this->redirectToCalendar($request)
            ->with('success', 'Contenu mis à jour.');
    }

    public function destroy(Request $request, EditorialEvent $editorialEvent)
    {
        $editorialEvent->delete();

        return $this->redirectToCalendar($request)
            ->with('success', 'Contenu supprimé du calendrier.');
    }

    private function validateEvent(Request $request): array
    {
        $isFacebookFi = $request->input('categorie') === 'facebook'
            && $request->input('type_contenu') === 'FI';

        $rules = [
            'titre' => ['required', 'string', 'max:255'],
            'categorie' => ['required', Rule::in(array_keys(EditorialEvent::CATEGORIES))],
            'type_contenu' => ['required', Rule::in(array_keys(EditorialEvent::TYPES_CONTENU))],
            'booster' => ['nullable', 'boolean'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'statut' => ['required', Rule::in(['planifie', 'en_cours', 'publie', 'annule'])],
            'valide' => ['nullable', 'boolean'],
            'texte_publication' => ['required', 'string', 'max:5000'],
            'visuel' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ];

        if ($isFacebookFi && $request->boolean('booster')) {
            $rules['date_fin'] = ['required', 'date', 'after_or_equal:date_debut'];
        }

        $validated = $request->validate($rules, [
            'texte_publication.required' => 'Le texte de publication est obligatoire.',
            'type_contenu.required' => 'Veuillez choisir FI ou FP.',
            'date_fin.required' => 'La date de fin est obligatoire lorsque Booster est activé.',
            'visuel.mimes' => 'Le visuel doit être une image (jpg, png, webp, gif).',
            'visuel.max' => 'Le visuel ne doit pas dépasser 5 Mo.',
        ]);

        unset($validated['visuel']);

        $validated['booster'] = $isFacebookFi && $request->boolean('booster');
        $validated['valide'] = auth()->user()?->canValidateEditorial()
            ? $request->boolean('valide')
            : false;

        if (! $isFacebookFi) {
            $validated['booster'] = false;
        }

        if (! $validated['booster']) {
            $validated['date_fin'] = $validated['date_fin'] ?? null;
        }

        return $validated;
    }

    private function redirectToCalendar(Request $request)
    {
        $date = $request->input('return_date', $request->input('date_debut', now()->toDateString()));
        $view = $request->input('return_view', '2weeks');

        return redirect()->route('calendrier-editorial', [
            'date' => $date,
            'view' => $view,
        ]);
    }

    private function mapEvent(EditorialEvent $event): array
    {
        return [
            'id' => $event->id,
            'titre' => $event->titre,
            'categorie' => $event->categorie,
            'label' => $event->categorie_meta['label'],
            'color' => $event->categorie_meta['color'],
            'text' => $event->categorie_meta['text'],
            'type_contenu' => $event->type_contenu,
            'booster' => (bool) $event->booster,
            'date_debut' => $event->date_debut->toDateString(),
            'date_fin' => ($event->date_fin ?? $event->date_debut)->toDateString(),
            'statut' => $event->statut,
            'valide' => (bool) $event->valide,
            'texte_publication' => $event->texte_publication,
            'visuel_url' => $event->visuel_url,
            'visuel_nom' => $event->visuel_nom,
            'update_url' => route('calendrier-editorial.update', $event),
            'delete_url' => route('calendrier-editorial.destroy', $event),
        ];
    }

    private function resolveRange(Carbon $date, string $view): array
    {
        return match ($view) {
            'day' => [$date->copy(), $date->copy()],
            'week' => [$date->copy()->startOfWeek(Carbon::MONDAY), $date->copy()->endOfWeek(Carbon::SUNDAY)],
            'month' => [$date->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY), $date->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY)],
            'list' => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()],
            default => [
                $date->copy()->startOfWeek(Carbon::MONDAY),
                $date->copy()->startOfWeek(Carbon::MONDAY)->addDays(13),
            ],
        };
    }

    private function formatRangeLabel(Carbon $start, Carbon $end): string
    {
        if ($start->isSameDay($end)) {
            return $start->locale('fr')->isoFormat('D MMMM YYYY');
        }

        if ($start->isSameMonth($end)) {
            return $start->locale('fr')->isoFormat('D')
                .' – '
                .$end->locale('fr')->isoFormat('D MMMM YYYY');
        }

        return $start->locale('fr')->isoFormat('D MMM')
            .' – '
            .$end->locale('fr')->isoFormat('D MMM YYYY');
    }
}
