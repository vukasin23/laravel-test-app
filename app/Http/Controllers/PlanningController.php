<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Models\HallElementPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;    // ← ne zaboravi

class PlanningController extends Controller
{
    public function editWeekAll(Request $request)
    {
        $weekIso = $request->input('week', Carbon::now()->format('o-\WW'));

        if (str_contains($weekIso, '-W')) {
            [$year, $weekNumber] = explode('-W', $weekIso);
        } else {
            $year       = Carbon::now()->year;
            $weekNumber = Carbon::now()->weekOfYear;
        }

        $startOfWeek = Carbon::now()
            ->setISODate((int)$year, (int)$weekNumber)
            ->startOfWeek();

        $dates = collect();
        for ($i = 0; $i < 7; $i++) {
            $dates->push($startOfWeek->copy()->addDays($i));
        }

        $halls = Hall::with('voorman')->get();


        $records = DB::table('hall_element_plans')
            ->whereIn('date', $dates->map->toDateString()->all())
            ->get(['hall_id', 'date', 'planned_count']);

        $allPlans = [];
        foreach ($records as $r) {
            $allPlans[$r->hall_id][$r->date] = $r->planned_count;
        }

        return view('planning.week',
            compact('weekIso','dates','halls','allPlans'));
    }

    public function updateWeekAll(Request $request)
    {
        $data = $request->validate([
            'week'        => 'required|string',
            'planned'     => 'required|array',
            'planned.*.*' => 'nullable|integer|min:0',
        ]);

        foreach ($data['planned'] as $hallId => $byDate) {
            foreach ($byDate as $date => $count) {
                // Prazni string ili null → 0
                $normalized = (is_null($count) || $count === '') ? 0 : (int)$count;

                DB::table('hall_element_plans')->updateOrInsert(
                    ['hall_id' => (int)$hallId, 'date' => $date],
                    [
                        'planned_count' => $normalized,
                        'entered_by'    => Auth::id(),
                        'updated_at'    => now(),
                        'created_at'    => now(),
                    ]
                );
            }
        }

        return redirect()
            ->route('planning.week.edit', ['week' => $data['week']])
            ->with('success', 'Plan je uspešno sačuvan.');
    }
}
