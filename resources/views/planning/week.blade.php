<x-app-layout>
    <div class="px-6 py-8 max-w-6xl mx-auto">
        {{-- Naslov --}}
        <div class="text-center py-4">
            <h1 class="text-green-700 text-xl font-bold uppercase">Input - Werkvoorbereider</h1>
        </div>

        {{-- Izbor sedmice --}}
        <form id="week-form"
              action="{{ route('planning.week.edit') }}"
              method="GET"
              class="flex items-center mb-8 space-x-4">
            <label for="week" class="font-semibold">Week:</label>
            <input type="week" id="week" name="week" value="{{ $weekIso }}" class="border rounded px-2 py-1"
                   onchange="document.getElementById('week-form').submit()">
        </form>

        {{-- Poruka o uspehu --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 p-4 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- Forma za sve hale --}}
        <form action="{{ route('planning.week.update') }}" method="POST">
            @csrf
            <input type="hidden" name="week" value="{{ $weekIso }}">

            @foreach($halls as $hall)
                <div class=" rounded-lg shadow border overflow-hidden">
                    {{-- Zeleni header kartice hale --}}
                    <div class="bg-green-700 text-white px-6 py-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold">{{ strtoupper($hall->name) }}</h3>
                            <div class="text-sm">Voorman: {{ $hall->voorman->first_name }} {{ $hall->voorman->last_name }}</div>
                        </div>
                        {{-- Radnici i razlika - placeholder, ti ubaciš prave podatke --}}
                        <div class="text-center">
                            <div class="text-2xl font-bold">20</div>
                            <div class="text-red-500 font-bold">-1</div>
                        </div>
                    </div>

                    {{-- Tabela sa danima --}}
                    <div class="bg-green-100">
                        <div class="grid grid-cols-7 text-center font-semibold border-b border-green-700">
                            @foreach($dates as $date)
                                <div class="py-2 border-r last:border-r-0">{{ $date->translatedFormat('D') }}</div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7 text-center border-b border-green-700">
                            @foreach($dates as $date)
                                <div class="py-1 border-r last:border-r-0 text-xs">{{ $date->translatedFormat('d-m-Y') }}</div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7 text-center">
                            @foreach($dates as $date)
                                @php
                                    $ds = $date->toDateString();
                                    $existing = $allPlans[$hall->id][$ds] ?? '';
                                @endphp
                                <div class="p-2 border-r last:border-r-0">
                                    <input type="number"
                                           name="planned[{{ $hall->id }}][{{ $ds }}]"
                                           value="{{ old("planned.{$hall->id}.{$ds}", $existing) }}"
                                           min="0"
                                           class="w-full text-center border border-gray-300 rounded py-1"
                                    >
                                    @error("planned.{$hall->id}.{$ds}")
                                    <span class="text-red-600 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class=" mb-10 flex flex-col items-start text-right">
                    {{-- Info o voormanu --}}
                    <div class="text-sm">voorman: {{ $hall->voorman->first_name }} {{ $hall->voorman->last_name }}</div>
                    <div class="text-sm">{{ $hall->voorman->phone }}</div>
                </div>
            @endforeach

            {{-- Dugme za čuvanje --}}
            <div class="text-right">
                <button type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded">
                    Sla plan op voor alle hallen
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
