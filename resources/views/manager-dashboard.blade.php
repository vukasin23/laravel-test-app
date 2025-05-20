<x-app-layout>
    {{-- ✅ HEADER sekcija sa vremenom i total elementima --}}
     <div class="h-screen w-screen flex flex-col overflow-hidden">
    <div class="bg-white shadow rounded-lg p-6 mb-4 flex justify-between items-center">

        <div>
            <p id="clock" class="text-4xl font-mono text-gray-800">--:--:--</p>
        </div>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12">
    </div>

    {{-- ✅ Zeleni bar --}}
    <div class="bg-green-700 text-white rounded-lg p-4 mb-6 flex justify-between items-center">
        <div class="text-xl font-bold">PRODUCTIE OVERZICHT</div>

        <div class="text-left">
            <p class="text-sm">totaal elementen</p>
            <p><span class="font-bold text-white-600">{{ $completedElementsCount }}</span> / <span class="font-bold text-white-600">{{ $total_elements }}</span></p>

        </div>
        <div class="flex gap-6 text-lg">
            <p>HAL-1 / {{ $hallA_total }}</p>
            <p>HAL-2 / {{ $hallB_total }}</p>
        </div>
        <div>{{ now()->translatedFormat('l d F Y') }}</div>
    </div>

    {{-- ✅ Manager info --}}
    <div class="bg-white shadow rounded-lg p-4 mb-6 text-sm text-gray-700">
        <p class="font-bold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
        <p class="text-gray-500">productie manager – {{ auth()->user()->PhoneNumber ?? 'n/a' }}</p>
    </div>

    {{-- ✅ Total Voortgang (overview blok) --}}
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="font-bold text-center mb-6">Totale Voortgang (Alle Hallen)</h3>

        <div class="flex justify-around mb-6">
            <div class="text-center">
                <p><span class="font-bold text-green-600">{{ $completedElementsCount }}</span> / <span class="font-bold text-red-600">{{ $total_elements }}</span> <span class="font-bold text-black-600">Voltooid</span></p>
                <p class="text-sm text-gray-500">({{ $total_elements ? round($total_completed / $total_elements * 100) : 0 }}%)</p>
            </div>
            <div class="text-center">
                <p><span class="font-bold text-green-600">{{ $total_workers }}</span> / <span class="font-bold text-red-600">{{ $total_expected }}</span> <span class="font-bold text-black-600">Mendewerkers vandaag</span></p>
                <p class="text-sm text-gray-500">({{ $total_expected ? round($total_workers / $total_expected * 100) : 0 }}%)</p>
            </div>
        </div>

        {{-- ✅ Krugovi faza --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6 text-center">
            @php
                $phaseNames = ['Uithalen','Voorzieningen','Kozijnen','Vlechten','Storten/Afwerken'];
            @endphp
            @foreach($phases_summary as $i => $data)
                @php
                    $pct = $data['total'] ? round($data['done'] / $data['total'] * 100) : 0;
                    $stroke = $pct >= 80 ? '#059669' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
                @endphp
                <div>
                    <div class="relative w-20 h-20 mx-auto">
                        <svg viewBox="0 0 36 36" class="w-full h-full rotate-[-90deg]">
                            <circle cx="18" cy="18" r="15.9155" stroke="#e5e7eb" stroke-width="3.8" fill="none"/>
                            <circle cx="18" cy="18" r="15.9155" stroke="{{ $stroke }}" stroke-width="3.8"
                                    stroke-dasharray="{{ $pct }},100" stroke-linecap="round" fill="none"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center font-bold text-gray-800">{{ $pct }}%</div>
                    </div>
                    <p class="mt-1 text-sm font-semibold">{{ $phaseNames[$i] }}</p>
                    <p class="text-xs">voltooid {{ $data['done'] }}/{{ $data['total'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ✅ Blokovi po hali --}}
    @foreach($halls as $hall)
        @php
            $hc      = $hall['completed'];
            $ht      = $hall['planned'];
            $hp      = $ht ? round($hc / $ht * 100) : 0;
            $present = $hall['worker_count'];
            $expected= $hall['expected_workers'];
            $absent  = $expected - $present;
            $attPct  = $expected ? round($present / $expected * 100) : 0;
        @endphp

        <div class="bg-green-100 border border-green-200 rounded-lg p-6 mb-6 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h4 class="text-lg font-bold">{{ $hall['name'] }}</h4>
                    <p class="text-lg font-bold">{{ $ht }}</p>
                    <p class="text-sm text-gray-700 mt-1">voorman: {{ $hall['voorman'] }} – {{ $hall['voorman_phone'] }}</p>
                </div>
                <div class="font-semibold text-center">
                    Voltooid {{ $completedPerHall[$hall['id']] ?? 0 }} / {{ $ht }} ({{ $hp }}%)
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-700 whitespace-nowrap">
                    <span>Medewerkers:</span>
                    <span class="text-green-600 font-semibold">{{ $present }}</span>
                    <span>&ndash;</span>
                    <span class="text-red-600 font-semibold">{{ $absent }}</span>
                    <span class="text-xs">({{ $attPct }}%)</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6 text-center">
                @foreach($hall['phases'] as $phase)
                    @php
                        $pd = $phase['done'];
                        $pt = $phase['total'];
                        $pp = $pt ? round($pd / $pt * 100) : 0;
                        $stroke = $pp >= 80 ? '#059669' : ($pp >= 50 ? '#f59e0b' : '#ef4444');
                    @endphp
                    <div>
                        <div class="relative w-20 h-20 mx-auto">
                            <svg viewBox="0 0 36 36" class="w-full h-full rotate-[-90deg]">
                                <circle cx="18" cy="18" r="15.9155" stroke="#e5e7eb" stroke-width="3.8" fill="none"/>
                                <circle cx="18" cy="18" r="15.9155" stroke="{{ $stroke }}" stroke-width="3.8"
                                        stroke-dasharray="{{ $pp }},100" stroke-linecap="round" fill="none"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center font-bold text-gray-800">{{ $pp }}%</div>
                        </div>
                        <p class="mt-1 text-sm font-semibold">{{ $phase['name'] }}</p>
                        <p class="text-xs">voltooid {{ $pd }}/{{ $pt }}</p>
                    </div>
                @endforeach
            </div>
        </div>
          </div>
    @endforeach

    {{-- ✅ Sat --}}
    <script>
        function updateClock() {
            document.getElementById('clock').textContent =
                new Date().toLocaleTimeString('nl-NL',{hour12:false});
        }
        setInterval(updateClock,1000);
        updateClock();

          setTimeout(() => {
        location.reload();
    }, 15000);
    </script>
</x-app-layout>
