<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'lastmod' => now()],
            ['loc' => route('public.projects.index'), 'lastmod' => now()],
            ['loc' => route('public.units.index'), 'lastmod' => now()],
        ]);

        foreach (Project::all(['id', 'updated_at']) as $project) {
            $urls->push(['loc' => route('public.projects.show', $project), 'lastmod' => $project->updated_at]);
        }

        foreach (Building::all(['id', 'updated_at']) as $building) {
            $urls->push(['loc' => route('public.buildings.show', $building), 'lastmod' => $building->updated_at]);
        }

        foreach (Unit::all(['id', 'updated_at']) as $unit) {
            $urls->push(['loc' => route('public.units.show', $unit), 'lastmod' => $unit->updated_at]);
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }
}
