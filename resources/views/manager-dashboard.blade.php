<x-app-layout>
    <div class="flex flex-col h-screen w-screen overflow-hidden text-sm leading-tight p-2">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-2">
            <p id="clock" class="text-xl font-mono text-gray-800">--:--:--</p>
            <img src="{{ asset('images/logo.png') }}" class="h-8" />
        </div>

        {{-- Green bar --}}
        <div class="bg-green-700 text-white p-2 flex justify-between items-center text-xs mb-2 rounded">
            <div class="font-bold">PRODUCTIE OVERZICHT</div>
            <div>{{ $completedElementsCount }} / {{ $total_elements }}</div>
            <div class="flex gap-2"><p>HAL-1: {{ $hallA_total }}</p><p>HAL-2: {{ $hallB_total }}</p></div>
            <div>{{ now()->translatedFormat('d.m.Y') }}</div>
        </div>

        {{-- Manager Info --}}
        <div class="text-xs text-gray-700 mb-2">
            <p class="font-bold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
            <p class="text-gray-500">manager – {{ auth()->user()->PhoneNumber ?? 'n/a' }}</p>
        </div>

        {{-- Total overview --}}
        <div class="mb-2">
            <div class="flex justify-around text-xs">
                <div>{{ $completedElementsCount }} / {{ $total_elements }} kompletirano</div>
                <div>{{ $total_workers }} / {{ $total_expected }} radnici ({{ $total_expected ? round($total_workers / $total_expected * 100) : 0 }}%)</div>
            </div>

            {{-- Krugovi --}}
            <div class="grid grid-cols-5 gap-2 mt-2">
                @foreach($phases_summary as $i => $data)
                    @php
                        $pct = $data['total'] ? round($data['done'] / $data['total'] * 100) : 0;
                        $stroke = $pct >= 80 ? '#059669' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
                        $names = ['Uithalen','Voorzieningen','Kozijnen','Vlechten','Storten'];
                    @endphp
                    <div class="text-center">
                        <div class="relative w-14 h-14 mx-auto">
                            <svg viewBox="0 0 36 36" class="w-full h-full rotate-[-90deg]">
                                <circle cx="18" cy="18" r="15.9155" stroke="#e5e7eb" stroke-width="3.8" fill="none"/>
                                <circle cx="18" cy="18" r="15.9155" stroke="{{ $stroke }}" stroke-width="3.8"
                                        stroke-dasharray="{{ $pct }},100" stroke-linecap="round" fill="none"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center font-bold text-xs">{{ $pct }}%</div>
                        </div>
                        <p class="text-xs">{{ $names[$i] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- HALL blokovi kao grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2 overflow-auto flex-grow">
            @foreach($halls as $hall)
                @php
                    $hp = $hall['planned'] ? round($hall['completed'] / $hall['planned'] * 100) : 0;
                @endphp
                <div class="bg-green-100 p-2 rounded shadow text-xs">
                    <div class="flex justify-between mb-1">
                        <div>
                            <p class="font-bold">{{ $hall['name'] }}</p>
                            <p>{{ $hall['planned'] }} planirano</p>
                        </div>
                        <div>✔ {{ $hall['completed'] }} ({{ $hp }}%)</div>
                    </div>
                    <p>Voorman: {{ $hall['voorman'] }} / {{ $hall['voorman_phone'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Clock + Refresh --}}
        <script>
            function updateClock() {
                document.getElementById('clock').textContent =
                    new Date().toLocaleTimeString('nl-NL',{hour12:false});
            }
            setInterval(updateClock, 1000);
            updateClock();
            setTimeout(() => location.reload(), 15000);
        </script>
    </div>
</x-app-layout>
