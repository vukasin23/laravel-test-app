<?php

namespace App\Http\Controllers;

use App\Models\Phase;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Hall;
use App\Models\PhaseItem;
use App\Models\HallAttendance;
use App\Models\HallElementPlan;
use Illuminate\Support\Facades\DB;
class ManagerDashboardController extends Controller
{

        public function index()
    {
        $today = Carbon::today()->toDateString();

        $totalElements       = 0;
        $totalCompleted      = 0;
        $totalExpectedWorkers = 0;
        $totalPresentWorkers  = 0;
        $phasesForSummary     = [];
        $dashboardData        = [];

        // Učitaj sve hale sa fazama, stavkama, voormanom i attendance za danas
        $halls = Hall::with([
            'phases' => fn($q)          => $q->orderBy('order'),
            'phases.items' => fn($q)    => $q->where('date', $today),
            'voorman',
            'attendances' => fn($q)     => $q->where('date', $today),
        ])->get();

        foreach ($halls as $hall) {
            // --- attendance ---
            $attendance = $hall->attendances->first();
            $expected   = $attendance->expected_count ?? 0;
            $present    = $attendance->present_count  ?? 0;
            $totalExpectedWorkers += $expected;
            $totalPresentWorkers  += $present;

            // --- planirani broj elemenata danas ---
            $plannedCount = HallElementPlan::where('hall_id', $hall->id)
                ->where('date', $today)
                ->value('planned_count')
                ?? $hall->total;  // fallback
            $hallTotal     = $plannedCount;
            $hallCompleted = 0;
            $hallPhases    = [];

            // --- obrada faza ---
            foreach ($hall->phases as $phase) {
                // faza se uvek odnosi na isti skup elemenata,
                // brojimo koliko je is_done za danas
                $done    = $phase->items->where('is_done', true)->count();
                $total   = $plannedCount;
                $percent = $total > 0 ? round($done / $total * 100) : 0;

                $color = $percent >= 80
                    ? '#10b981'
                    : ($percent >= 50 ? '#f59e0b' : '#ef4444');

                $hallCompleted += $done;

                // za total summary
                $phasesForSummary[$phase->name]['done']  =
                    ($phasesForSummary[$phase->name]['done']  ?? 0) + $done;
                $phasesForSummary[$phase->name]['total'] =
                    ($phasesForSummary[$phase->name]['total'] ?? 0) + $total;

                $hallPhases[] = [
                    'name'  => $phase->name,
                    'done'  => $done,
                    'total' => $total,
                    'color' => $color,
                ];
            }

            $totalElements  += $hallTotal;
            $totalCompleted += $hallCompleted;

            $dashboardData[] = [
                'id' => $hall->id,
                'name'             => $hall->name,
                'voorman'          => trim((string)($hall->voorman?->first_name . ' ' . $hall->voorman?->last_name)),
                'voorman_phone'    => $hall->voorman?->PhoneNumber,
                'worker_count'     => $present,
                'expected_workers' => $expected,
                'planned'          => $hallTotal,
                'completed'        => $hallCompleted,
                'phases'           => $hallPhases,
            ];
        }

        // izdvajamo HAL-1 i HAL-2 za zeleni header
        $hallA_total = collect($dashboardData)->firstWhere('name','HAL-1')['planned'] ?? 0;
        $hallB_total = collect($dashboardData)->firstWhere('name','HAL-2')['planned'] ?? 0;


        $completedElementsCount = \DB::table('phase_items')
            ->whereDate('date', now()->toDateString())
            ->where('is_done', 1)
            ->whereIn('phase_id', [1,2,3,4,5])
            ->select('number')
            ->groupBy('number')
            ->havingRaw('COUNT(DISTINCT phase_id) = 5')
            ->unionAll(
                \DB::table('phase_items')
                    ->whereDate('date', now()->toDateString())
                    ->where('is_done', 1)
                    ->whereIn('phase_id', [6,7,8,9,10])
                    ->select('number')
                    ->groupBy('number')
                    ->havingRaw('COUNT(DISTINCT phase_id) = 5')
            )
            ->get()
            ->count();
        // phases_summary u redosledu
        $phaseOrder = ['Uithalen','Uitzetten','Wapening','Vlechten','Storten/Afwerken'];
        $phases_summary = [];
        foreach ($phasesForSummary as $name => $values) {
            $done  = $values['done']  ?? 0;
            $total = $values['total'] ?? 0;
            $phases_summary[] = [
                'name'  => $name,
                'done'  => $done,
                'total' => $total
            ];
        }
        $completedPerHall = DB::table('phase_items as pi')
    ->join('phases as p', 'pi.phase_id', '=', 'p.id')
    ->whereDate('pi.date', now()->toDateString())
    ->where('pi.is_done', 1)
    ->select('p.hall_id', 'pi.number', DB::raw('COUNT(DISTINCT pi.phase_id) as phase_count'))
    ->groupBy('p.hall_id', 'pi.number')
    ->having('phase_count', '=', 5)
    ->get()
    ->groupBy('hall_id')
    ->map(fn($group) => $group->count());

        return view('manager-dashboard', [
            'halls'            => $dashboardData,
            'total_elements'   => $totalElements,
            'total_completed'  => $totalCompleted,
            'total_workers'    => $totalPresentWorkers,
            'total_expected'   => $totalExpectedWorkers,
            'phases_summary'   => $phases_summary,
            'hallA_total'      => $hallA_total,
            'hallB_total'      => $hallB_total,
            'completedElementsCount' => $completedElementsCount,
            'completedPerHall'=>$completedPerHall,
        ]);
    }

}
