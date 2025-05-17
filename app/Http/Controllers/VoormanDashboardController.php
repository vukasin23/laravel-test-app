<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;       // ← obavezno importuj Log
use App\Models\PhaseItem;                 // ← importuj model

class VoormanDashboardController extends Controller
{
    //
    public function index()
    {
        $hall = Auth::user()->hall;
        if (! $hall) {
            abort(403, 'Nemaš dodeljenu halu.');
        }

        // Učitaj faze sa stavkama za današnji datum
        $phases = $hall->phases()
            ->with(['items' => function($q) {
                $q->where('date', now()->toDateString());
            }])
            ->orderBy('order')
            ->get();

        // Dnevni plan: broj elemenata za danas
        $today = now()->toDateString();
        $planForToday = \App\Models\HallElementPlan::where('hall_id', $hall->id)
            ->where('date', $today)
            ->value('planned_count')
            ?? $hall->total;

        // Generiši brojeve od 1 do planiranog broja
        $numbers = collect(range(1, $planForToday));

        return view('voorman.dashboard', compact(
            'hall',
            'phases',
            'numbers',
            'planForToday'
        ));
    }

    public function updateItem(Request $request)
    {
        // Zapiši sve što je stiglo iz AJAX poziva
        Log::info('Voorman updateItem poziv', $request->all());

        // Validacija
        $request->validate([
            'phase_id' => 'required|exists:phases,id',
            'number'   => 'required|integer',
            'is_done'  => 'required|boolean',
        ]);

        Log::info('Voorman updateItem nakon validacije', $request->only(['phase_id','number','is_done']));

        // Priprema podataka
        $data = [
            'phase_id' => $request->phase_id,
            'number'   => $request->number,
            'date'     => now()->toDateString(),
        ];
        Log::info('Voorman updateItem pripremljeni podaci', $data);

        try {
            // Pronađi ili kreiraj
            $item = PhaseItem::firstOrNew($data);
            $item->is_done = $request->is_done;
            $item->save();

            Log::info('Voorman updateItem sačuvan item', $item->toArray());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Ako nešto pukne, zapiši grešku
            Log::error('Voorman updateItem greška pri snimanju', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => 'Save failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
