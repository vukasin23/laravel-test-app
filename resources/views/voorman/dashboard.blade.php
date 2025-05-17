{{-- resources/views/voorman/dashboard.blade.php --}}

<x-app-layout>
    {{-- Header sekcija --}}
    <div class="bg-white shadow rounded-lg p-3 mb-6 flex items-center justify-between">
        <div class="text-left">
            <h1 class="text-3xl font-bold text-gray-800">{{ $hall->name }}</h1>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}</p>
            <p class="text-base text-gray-700">
                Voorman: <span class="font-semibold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
            </p>
        </div>
        <div class="text-center">
            <p class="text-6xl font-bold text-green-600">{{ $planForToday }}</p>
            <p class="text-sm text-gray-600">aantal elementen</p>
            <p id="clock" class="mt-1 text-2xl font-mono text-gray-800"></p>
        </div>
        <div class="text-right">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10">
        </div>
    </div>

    ```
    @php
        $phaseNames = ['Uithalen','Uitzetten','Wapening','Vlechten','Storten/Afwerken'];
        $total = $planForToday;
    @endphp

    {{-- Progress cirkule --}}
    <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm px-4 py-4 mb-4">
        <div class="grid grid-cols-6 gap-x-6 gap-y-8 text-center">
            <div class="flex flex-col items-center space-y-1">
                <img src="{{ asset('images/kucica.jpg') }}" alt="Home icon" class="w-10 h-10 text-green-600" />
                <p class="text-sm font-semibold text-green-600">Woningbouw met onze</p>
                <p class="text-sm font-semibold text-green-600">CO₂-arme casco's</p>
            </div>
            @foreach ($phases as $index => $phase)
                @php
                    $done = $phase->items->where('is_done', 1)->count();
                    $pct = $total > 0 ? intval(($done / $total) * 100) : 0;
                @endphp
                <div class="flex flex-col items-center space-y-2">
                    <div class="relative w-20 h-20">
                        <svg class="w-full h-full rotate-[-90deg]" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#e5e7eb" stroke-width="3.8"/>
                            <circle cx="18" cy="18" r="15.9155" fill="none"
                                    class="progress-circle"
                                    data-index="{{ $index }}"
                                    stroke="#10b981"
                                    stroke-width="3.8"
                                    stroke-dasharray="{{ $pct }},100"
                                    stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-sm font-bold text-gray-800 progress-percent" data-index="{{ $index }}">
                            {{ $pct }}%
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ $phaseNames[$index] }}</span>
                    <span class="text-xs text-gray-500 progress-number" data-index="{{ $index }}">{{ $done }}/{{ $total }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Tabela elemenata --}}
    <div class="bg-white shadow rounded-lg p-6">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm text-gray-700 text-center">
                <thead class="border-b">
                <tr>
                    <th class="w-1/6 py-2">AANTAL</th>
                    @foreach ($phases as $index => $phase)
                        <th class="w-1/6 py-2">FASE-{{ $index + 1 }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($numbers as $number)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 font-semibold">#{{ $number }}</td>
                        @foreach ($phases as $index => $phase)
                            @php $item = $phase->items->firstWhere('number', $number); @endphp
                            <td class="py-2">
                                <input type="checkbox"
                                       class="form-checkbox w-6 h-6 text-green-500"
                                       data-number="{{ $number }}"
                                       data-phase="{{ $phase->id }}"
                                       data-index="{{ $index }}"
                                    {{ $item && $item->is_done ? 'checked' : '' }}>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Legenda --}}
    <div class="mt-6 text-sm text-gray-600">
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-2"><div class="w-4 h-4 border border-gray-400 rounded-sm"></div><span>Niet Gereed</span></div>
            <div class="flex items-center space-x-2"><div class="w-4 h-4 bg-green-500 rounded-sm"></div><span>Voltooid</span></div>
        </div>
        <p class="mt-2 italic text-xs text-gray-500">Een onderdeel kan een fase niet voltooien totdat het vorige onderdeel die fase heeft gestart.</p>
    </div>

    {{-- Notifikacije --}}
    <div id="notification" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg hidden z-50"></div>
    <div id="error-notification" class="fixed top-5 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-lg hidden z-50"></div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Postavljanje globalnih varijabli
            const total = {{ $total }};
            const phases = @json($phases->pluck('id'));

            // Live clock
            setInterval(() => {
                document.getElementById('clock').innerText = new Date().toLocaleTimeString('nl-NL', { hour12: false });
            }, 1000);

            // Inicijalno ažuriranje kružića
            updateProgress();

            // Prikači event listener na sve checkbox-e
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', onCheckboxChange);
            });

            function onCheckboxChange(event) {
                const cb = event.target;
                const number = cb.dataset.number;
                const phaseId = cb.dataset.phase;
                const index = parseInt(cb.dataset.index);
                const isDone = cb.checked ? 1 : 0;

                // Provera prethodne faze
                if (isDone && index > 0) {
                    const prev = document.querySelector(`input[data-number="${number}"][data-index="${index-1}"]`);
                    if (!prev.checked) {
                        cb.checked = false;
                        showError('Morate prvo završiti prethodnu fazu!');
                        return;
                    }
                }

                // Ako se odčekira, poništi sve naredne
                if (!isDone) {
                    let nextIndex = index + 1;
                    let nextCb;
                    while ((nextCb = document.querySelector(`input[data-number="${number}"][data-index="${nextIndex}"]`))) {
                        if (nextCb.checked) {
                            nextCb.checked = false;
                            saveChange(nextCb.dataset.phase, number, 0);
                        }
                        nextIndex++;
                    }
                }

                // Sačuvaj trenutnu promenu
                saveChange(phaseId, number, isDone);
            }

            function saveChange(phase, number, isDone) {
                axios.post('{{ route('voorman.update.item') }}', {
                    phase_id: phase,
                    number: number,
                    is_done: isDone
                })
                    .then(() => {
                        showSuccess('Uspešno snimljeno ✅');
                        updateProgress();
                    })
                    .catch(() => showError('Greška pri snimanju.'));
            }

            function updateProgress() {
                phases.forEach((phaseId, idx) => {
                    const doneCount = document.querySelectorAll(`input[data-phase="${phaseId}"]:checked`).length;
                    const pct = total > 0 ? Math.round((doneCount / total) * 100) : 0;
                    const percentEl = document.querySelector(`.progress-percent[data-index="${idx}"]`);
                    const numberEl = document.querySelector(`.progress-number[data-index="${idx}"]`);
                    const circle   = document.querySelector(`.progress-circle[data-index="${idx}"]`);

                    if (percentEl) percentEl.innerText = pct + '%';
                    if (numberEl) numberEl.innerText = `${doneCount}/${total}`;
                    if (circle) {
                        circle.setAttribute('stroke-dasharray', `${pct},100`);
                        circle.setAttribute('stroke', pct === 100 ? '#059669' : '#10b981');
                    }
                });
            }

            function showSuccess(msg) {
                const n = document.getElementById('notification');
                n.innerText = msg;
                n.classList.remove('hidden');
                setTimeout(() => n.classList.add('hidden'), 2000);
            }
            function showError(msg) {
                const n = document.getElementById('error-notification');
                n.innerText = msg;
                n.classList.remove('hidden');
                setTimeout(() => n.classList.add('hidden'), 2000);
            }
        });
    </script>
    ```

</x-app-layout>
