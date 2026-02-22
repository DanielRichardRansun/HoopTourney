@extends('layouts.app2')

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-yellow-500/10 border border-yellow-500/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-yellow-500 text-2xl">edit_note</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Edit Hasil Pertandingan</h1>
        <p class="text-slate-400 text-sm">{{ $team1->name }} vs {{ $team2->name }}</p>
    </div>
</div>

<form action="{{ route('matchResults.update', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Quarter Scores --}}
    <div class="glass-panel rounded-2xl border border-[#393028] p-6 mb-6 shadow-xl">
        <div class="flex items-center gap-2 mb-5">
            <span class="material-symbols-outlined text-primary">scoreboard</span>
            <h3 class="text-white font-black uppercase tracking-wider text-sm">Edit Hasil Pertandingan per Kuarter</h3>
        </div>
        <div id="quarter-scores-container" class="space-y-4">
            @php $maxQuarter = $quarterResults->keys()->max() ?: 0; @endphp
            @for ($qNum = 1; $qNum <= $maxQuarter; $qNum++)
                @php $quarterData = $quarterResults->get($qNum); @endphp
                <div class="quarter-input-group glass-panel rounded-xl p-4 border border-[#393028]" data-quarter-number="{{ $qNum }}">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-primary text-xs font-bold uppercase tracking-widest">Kuarter {{ $qNum }}</label>
                        @if ($qNum > 1)
                            <button type="button" class="remove-quarter-btn size-7 rounded-lg bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <label class="text-slate-500 text-[10px] uppercase font-bold tracking-wider">{{ $team1->name }}</label>
                            <input type="number" name="quarter_scores[{{ $qNum }}][team1_score]" class="w-full bg-[#221914] border border-[#393028] text-white rounded-lg px-3 py-2.5 outline-none focus:border-primary transition-colors text-center font-bold" required min="0" value="{{ $quarterData->team1_score ?? 0 }}">
                        </div>
                        <span class="text-slate-600 font-black text-xs mt-4">VS</span>
                        <div class="flex-1">
                            <label class="text-slate-500 text-[10px] uppercase font-bold tracking-wider">{{ $team2->name }}</label>
                            <input type="number" name="quarter_scores[{{ $qNum }}][team2_score]" class="w-full bg-[#221914] border border-[#393028] text-white rounded-lg px-3 py-2.5 outline-none focus:border-primary transition-colors text-center font-bold" required min="0" value="{{ $quarterData->team2_score ?? 0 }}">
                        </div>
                    </div>
                </div>
            @endfor
        </div>
        <button type="button" id="add-quarter-button" class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded-xl font-bold uppercase tracking-wider text-xs hover:bg-blue-500 hover:text-white transition-all">
            <span class="material-symbols-outlined text-[16px]">add</span> Tambah Kuarter
        </button>
    </div>

    {{-- Player Stats --}}
    <div class="glass-panel rounded-2xl border border-[#393028] p-6 mb-6 shadow-xl">
        <div class="flex items-center gap-2 mb-5">
            <span class="material-symbols-outlined text-indigo-400">bar_chart</span>
            <h3 class="text-white font-black uppercase tracking-wider text-sm">Statistik Pemain per Kuarter</h3>
        </div>

        {{-- Team 1 --}}
        <h5 class="text-white font-black text-base mb-4 pb-2 border-b-2 border-primary/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[18px]">groups</span> {{ $team1->name }}
        </h5>
        @foreach($players1 as $player)
            <div class="glass-panel rounded-xl p-4 mb-4 border border-[#393028]">
                <h6 class="text-white font-bold text-sm mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-500 text-[16px]">person</span> {{ $player->name }}
                </h6>
                <div id="player-stats-{{ $player->id }}-container">
                    @for ($qNum = 1; $qNum <= $maxQuarter; $qNum++)
                        @php $stats = $playerStats[$player->id][$qNum] ?? new \stdClass(); @endphp
                        <div class="player-stats bg-[#221914] rounded-lg p-3 mb-2" data-quarter-number="{{ $qNum }}">
                            <label class="block text-primary text-[10px] font-bold uppercase tracking-widest mb-2">Kuarter {{ $qNum }}</label>
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-12 gap-2">
                                @foreach(['point'=>'PTS','fgm'=>'FGM','fga'=>'FGA','fta'=>'FTA','ftm'=>'FTM','orb'=>'ORB','drb'=>'DRB','stl'=>'STL','ast'=>'AST','blk'=>'BLK','pf'=>'PF','to'=>'TO'] as $key => $label)
                                    <div class="text-center">
                                        <span class="text-slate-500 text-[9px] font-bold uppercase block mb-1">{{ $label }}</span>
                                        <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][{{ $key }}]" min="0" value="{{ $stats->$key ?? 0 }}" class="w-full bg-[#181411] border border-[#393028] text-white rounded-md px-1 py-1.5 outline-none focus:border-primary text-center text-xs font-semibold">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endforeach

        {{-- Team 2 --}}
        <h5 class="text-white font-black text-base mb-4 pb-2 border-b-2 border-primary/30 flex items-center gap-2 mt-8">
            <span class="material-symbols-outlined text-primary text-[18px]">groups</span> {{ $team2->name }}
        </h5>
        @foreach($players2 as $player)
            <div class="glass-panel rounded-xl p-4 mb-4 border border-[#393028]">
                <h6 class="text-white font-bold text-sm mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-500 text-[16px]">person</span> {{ $player->name }}
                </h6>
                <div id="player-stats-{{ $player->id }}-container">
                    @for ($qNum = 1; $qNum <= $maxQuarter; $qNum++)
                        @php $stats = $playerStats[$player->id][$qNum] ?? new \stdClass(); @endphp
                        <div class="player-stats bg-[#221914] rounded-lg p-3 mb-2" data-quarter-number="{{ $qNum }}">
                            <label class="block text-primary text-[10px] font-bold uppercase tracking-widest mb-2">Kuarter {{ $qNum }}</label>
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-12 gap-2">
                                @foreach(['point'=>'PTS','fgm'=>'FGM','fga'=>'FGA','fta'=>'FTA','ftm'=>'FTM','orb'=>'ORB','drb'=>'DRB','stl'=>'STL','ast'=>'AST','blk'=>'BLK','pf'=>'PF','to'=>'TO'] as $key => $label)
                                    <div class="text-center">
                                        <span class="text-slate-500 text-[9px] font-bold uppercase block mb-1">{{ $label }}</span>
                                        <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][{{ $key }}]" min="0" value="{{ $stats->$key ?? 0 }}" class="w-full bg-[#181411] border border-[#393028] text-white rounded-md px-1 py-1.5 outline-none focus:border-primary text-center text-xs font-semibold">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endforeach
    </div>

    <button type="submit" class="w-full py-4 bg-gradient-to-r from-primary to-orange-400 text-[#181411] rounded-xl font-black uppercase tracking-wider text-sm hover:shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)] transition-all flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-[20px]">save</span> Simpan Perubahan
    </button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let quarterCount = {{ $maxQuarter > 0 ? $maxQuarter : 1 }};
        const addQuarterButton = document.getElementById('add-quarter-button');
        const quarterScoresContainer = document.getElementById('quarter-scores-container');
        const allPlayerStatContainers = document.querySelectorAll('[id^="player-stats-"][id$="-container"]');

        document.querySelectorAll('.remove-quarter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const quarterDiv = this.closest('.quarter-input-group');
                const qNumToRemove = parseInt(quarterDiv.getAttribute('data-quarter-number'));
                removeQuarter(qNumToRemove, quarterDiv);
            });
        });

        addQuarterButton.addEventListener('click', function() {
            quarterCount++;
            addQuarterInput(quarterCount);
            addPlayerStatsInputsForNewQuarter(quarterCount);
        });

        function addQuarterInput(qNum) {
            const quarterDiv = document.createElement('div');
            quarterDiv.classList.add('quarter-input-group', 'glass-panel', 'rounded-xl', 'p-4', 'border', 'border-[#393028]');
            quarterDiv.setAttribute('data-quarter-number', qNum);
            quarterDiv.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <label class="text-primary text-xs font-bold uppercase tracking-widest">Kuarter ${qNum}</label>
                    <button type="button" class="remove-quarter-btn size-7 rounded-lg bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <label class="text-slate-500 text-[10px] uppercase font-bold tracking-wider">{{ $team1->name }}</label>
                        <input type="number" name="quarter_scores[${qNum}][team1_score]" class="w-full bg-[#221914] border border-[#393028] text-white rounded-lg px-3 py-2.5 outline-none focus:border-primary transition-colors text-center font-bold" required min="0" placeholder="0">
                    </div>
                    <span class="text-slate-600 font-black text-xs mt-4">VS</span>
                    <div class="flex-1">
                        <label class="text-slate-500 text-[10px] uppercase font-bold tracking-wider">{{ $team2->name }}</label>
                        <input type="number" name="quarter_scores[${qNum}][team2_score]" class="w-full bg-[#221914] border border-[#393028] text-white rounded-lg px-3 py-2.5 outline-none focus:border-primary transition-colors text-center font-bold" required min="0" placeholder="0">
                    </div>
                </div>
            `;
            quarterScoresContainer.appendChild(quarterDiv);
            quarterDiv.querySelector('.remove-quarter-btn').addEventListener('click', function() {
                removeQuarter(qNum, quarterDiv);
            });
        }

        function addPlayerStatsInputsForNewQuarter(qNum) {
            allPlayerStatContainers.forEach(container => {
                const playerId = container.id.split('-')[2];
                container.appendChild(createPlayerQuarterStatsHtml(playerId, qNum));
            });
        }

        function createPlayerQuarterStatsHtml(playerId, qNum, stats = {}) {
            const div = document.createElement('div');
            div.classList.add('player-stats', 'bg-[#221914]', 'rounded-lg', 'p-3', 'mb-2');
            div.setAttribute('data-quarter-number', qNum);
            const fields = ['point','fgm','fga','fta','ftm','orb','drb','stl','ast','blk','pf','to'];
            const labels = ['PTS','FGM','FGA','FTA','FTM','ORB','DRB','STL','AST','BLK','PF','TO'];
            let html = `<label class="block text-primary text-[10px] font-bold uppercase tracking-widest mb-2">Kuarter ${qNum}</label><div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-12 gap-2">`;
            fields.forEach((f, i) => {
                html += `<div class="text-center"><span class="text-slate-500 text-[9px] font-bold uppercase block mb-1">${labels[i]}</span><input type="number" name="player_stats[${playerId}][${qNum}][${f}]" min="0" value="${stats[f] || ''}" class="w-full bg-[#181411] border border-[#393028] text-white rounded-md px-1 py-1.5 outline-none focus:border-primary text-center text-xs font-semibold"></div>`;
            });
            html += '</div>';
            div.innerHTML = html;
            return div;
        }

        function removeQuarter(qNumToRemove, quarterDiv) {
            quarterDiv.remove();
            allPlayerStatContainers.forEach(container => {
                const playerStatsForQuarter = container.querySelector(`.player-stats[data-quarter-number="${qNumToRemove}"]`);
                if (playerStatsForQuarter) { playerStatsForQuarter.remove(); }
            });
            let currentQuarter = 1;
            document.querySelectorAll('.quarter-input-group').forEach(qDiv => {
                const oldQuarterNumber = qDiv.getAttribute('data-quarter-number');
                if (oldQuarterNumber != currentQuarter) {
                    qDiv.setAttribute('data-quarter-number', currentQuarter);
                    qDiv.querySelector('label').textContent = `Kuarter ${currentQuarter}`;
                    qDiv.querySelectorAll('input').forEach(input => {
                        input.name = input.name.replace(`[${oldQuarterNumber}]`, `[${currentQuarter}]`);
                    });
                }
                let removeButton = qDiv.querySelector('.remove-quarter-btn');
                if (currentQuarter === 1) {
                    if (removeButton) removeButton.remove();
                } else {
                    if (!removeButton) {
                        removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.classList.add('remove-quarter-btn', 'size-7', 'rounded-lg', 'bg-red-500/10', 'border', 'border-red-500/30', 'flex', 'items-center', 'justify-center', 'text-red-400', 'hover:bg-red-500', 'hover:text-white', 'transition-all');
                        removeButton.innerHTML = '<span class="material-symbols-outlined text-[14px]">close</span>';
                        const headerDiv = qDiv.querySelector('.flex.items-center.justify-between');
                        if (headerDiv) headerDiv.appendChild(removeButton);
                        removeButton.addEventListener('click', function() {
                            removeQuarter(currentQuarter, qDiv);
                        });
                    }
                }
                currentQuarter++;
            });
            allPlayerStatContainers.forEach(container => {
                let currentPlayerQuarter = 1;
                container.querySelectorAll('.player-stats').forEach(psDiv => {
                    const oldPsQuarterNumber = psDiv.getAttribute('data-quarter-number');
                    if (oldPsQuarterNumber != currentPlayerQuarter) {
                        psDiv.setAttribute('data-quarter-number', currentPlayerQuarter);
                        psDiv.querySelector('label').textContent = `Kuarter ${currentPlayerQuarter}`;
                        psDiv.querySelectorAll('input').forEach(input => {
                            const playerId = input.name.split('[')[1].replace(']', '');
                            input.name = `player_stats[${playerId}][${currentPlayerQuarter}]${input.name.split(']')[2]}`;
                        });
                    }
                    currentPlayerQuarter++;
                });
            });
            quarterCount = currentQuarter - 1;
        }
    });
</script>
@endsection