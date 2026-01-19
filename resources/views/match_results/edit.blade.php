@extends('layouts.app2')

@section('content')
<style>
    .container {
        max-width: 100%;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }
    .section-title {
    font-size: 24px;
    text-align: center;
    background-color: #1e3c72;
    color: white;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.score-container {
    flex-direction: column;
    align-items: center;
    margin-bottom: 30px;
    background-color: #fff;
    border: 2px solid #1e3c72;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    position: relative;
}

.score-content {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
}

.score-container h4 {
    margin-bottom: 15px;
    font-size: 24px;
    font-weight: bold;
}

.score-container label {
    font-size: 18px;
    font-weight: bold;
    color: #555;
}

.score-container input {
    width: 100px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    text-align: center;
}
    .team-stats {
        border: 2px solid #1e3c72;
        margin-bottom: 40px;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .team-stats h4 {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 15px;
    }
    .team-stats h5 {
        font-weight: bold !important;
        text-align: center;
        font-size: 20px;
        margin-bottom: 15px;
    }
    .player-stats {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding: 10px;
        background-color: #f1f1f1;
        border-radius: 5px;
    }
    .player-stats label {
        width: 100%;
        font-size: 15px;
        font-weight: bold;
        color: #444;
        margin-bottom: 10px;
    }
    .player-stats input {
        width: 60px;
        /* margin-right: 10px; */
        margin-bottom: 10px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        text-align: center;
    }
    .player-stats input::placeholder {
        color: #999;
    }
    .btn-submit {
        background-color: #1e3c72;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    .btn-submit:hover {
        background-color: #0056b3;
    }
    .btn-submit:active {
        background-color: #004080;
    }.quarter-input-group {
        /* display: flex; */
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: flex-end; /* Align items to the bottom */
    }
    .quarter-input-group > div {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .quarter-input-group label {
        margin-bottom: 5px;
        font-weight: bold;
    }
    .quarter-input-group input[type="number"] {
        width: 300px; /* Adjust width as needed */
        text-align: center;
    }
    .add-quarter-btn {
        margin-top: 10px;
        padding: 8px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .remove-quarter-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
        margin-left: 10px;
    }
    .player-quarter-stats {
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    .player-quarter-stats h6 {
        margin-top: 0;
        margin-bottom: 10px;
    }
</style>

<div class="container">
    <form action="{{ route('matchResults.update', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" method="POST">
        @csrf
        @method('PUT') {{-- Penting untuk metode UPDATE --}}

        <div class="score-container">
            <div class="section-title">Edit Hasil Pertandingan per Kuarter</div>
            <div id="quarter-scores-container">
                @php
                    $maxQuarter = $quarterResults->keys()->max() ?: 0;
                @endphp
                @for ($qNum = 1; $qNum <= $maxQuarter; $qNum++)
                    @php
                        $quarterData = $quarterResults->get($qNum);
                    @endphp
                    <div class="quarter-input-group" data-quarter-number="{{ $qNum }}">
                        <div>
                            <label>Kuarter {{ $qNum }}</label>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <input type="number" name="quarter_scores[{{ $qNum }}][team1_score]" class="form-control" required min="0" placeholder="{{ $team1->name }}" value="{{ $quarterData->team1_score ?? 0 }}">
                                <input type="number" name="quarter_scores[{{ $qNum }}][team2_score]" class="form-control" required min="0" placeholder="{{ $team2->name }}" value="{{ $quarterData->team2_score ?? 0 }}">
                            </div>
                        </div>
                        @if ($qNum > 1) {{-- Jangan izinkan menghapus kuarter pertama secara default --}}
                            <button type="button" class="remove-quarter-btn">X</button>
                        @endif
                    </div>
                @endfor
            </div>
            <button type="button" id="add-quarter-button" class="add-quarter-btn">Tambah Kuarter</button>
        </div>

        <div class="team-stats">
            <div class="section-title">Statistik Pemain per Kuarter</div>

            <h5>{{ $team1->name }}</h5>
            @foreach($players1 as $player)
                <div class="player-quarter-stats">
                    <h6>{{ $player->name }}</h6>
                    <div id="player-stats-{{ $player->id }}-container">
                        @for ($qNum = 1; $qNum <= $maxQuarter; $qNum++)
                            @php
                                $stats = $playerStats[$player->id][$qNum] ?? new \stdClass(); // Get existing stats or empty object
                            @endphp
                            <div class="player-stats" style="display: flex; gap: 10px; align-items: center; text-align: center; flex-wrap: wrap;" data-quarter-number="{{ $qNum }}">
                                <label style="font-weight: normal;">Kuarter {{ $qNum }}</label>
                                <div style="display: flex; flex-direction: column;">
                                    <span>Point</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][point]" min="0" value="{{ $stats->point ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>FGM</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fgm]" min="0" value="{{ $stats->fgm ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>FGA</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fga]" min="0" value="{{ $stats->fga ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>FTA</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fta]" min="0" value="{{ $stats->fta ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>FTM</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][ftm]" min="0" value="{{ $stats->ftm ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>ORB</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][orb]" min="0" value="{{ $stats->orb ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>DRB</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][drb]" min="0" value="{{ $stats->drb ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>STL</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][stl]" min="0" value="{{ $stats->stl ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>AST</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][ast]" min="0" value="{{ $stats->ast ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>BLK</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][blk]" min="0" value="{{ $stats->blk ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>PF</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][pf]" min="0" value="{{ $stats->pf ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>TO</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][to]" min="0" value="{{ $stats->to ?? 0 }}">
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endforeach

            <h5>{{ $team2->name }}</h5>
            @foreach($players2 as $player)
                <div class="player-quarter-stats">
                    <h6>{{ $player->name }}</h6>
                    <div id="player-stats-{{ $player->id }}-container">
                        @for ($qNum = 1; $qNum <= $maxQuarter; $qNum++)
                            @php
                                $stats = $playerStats[$player->id][$qNum] ?? new \stdClass();
                            @endphp
                            <div class="player-stats" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;" data-quarter-number="{{ $qNum }}">
                                <label style="font-weight: normal;">Kuarter {{ $qNum }}</label>
                                <div style="display: flex; flex-direction: column;">
                                    <span>Point</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][point]" min="0" value="{{ $stats->point ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>FGM</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fgm]" min="0" value="{{ $stats->fgm ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>FGA</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fga]" min="0" value="{{ $stats->fga ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>FTA</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fta]" min="0" value="{{ $stats->fta ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>FTM</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][ftm]" min="0" value="{{ $stats->ftm ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>ORB</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][orb]" min="0" value="{{ $stats->orb ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>DRB</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][drb]" min="0" value="{{ $stats->drb ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>STL</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][stl]" min="0" value="{{ $stats->stl ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>AST</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][ast]" min="0" value="{{ $stats->ast ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>BLK</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][blk]" min="0" value="{{ $stats->blk ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>PF</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][pf]" min="0" value="{{ $stats->pf ?? 0 }}">
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span>TO</span>
                                    <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][to]" min="0" value="{{ $stats->to ?? 0 }}">
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn-submit">Simpan Perubahan</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let quarterCount = {{ $maxQuarter > 0 ? $maxQuarter : 1 }}; // Initialize with existing quarters or 1
        const addQuarterButton = document.getElementById('add-quarter-button');
        const quarterScoresContainer = document.getElementById('quarter-scores-container');
        const allPlayerStatContainers = document.querySelectorAll('[id^="player-stats-"][id$="-container"]');

        // Add event listeners for initial "remove quarter" buttons
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
            quarterDiv.classList.add('quarter-input-group');
            quarterDiv.setAttribute('data-quarter-number', qNum);
            quarterDiv.innerHTML = `
                <div>
                    <label>Kuarter ${qNum}</label>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <input type="number" name="quarter_scores[${qNum}][team1_score]" class="form-control" required min="0" placeholder="{{ $team1->name }}">
                        <input type="number" name="quarter_scores[${qNum}][team2_score]" class="form-control" required min="0" placeholder="{{ $team2->name }}">
                    </div>
                </div>
                <button type="button" class="remove-quarter-btn">X</button>
            `;
            quarterScoresContainer.appendChild(quarterDiv);

            quarterDiv.querySelector('.remove-quarter-btn').addEventListener('click', function() {
                removeQuarter(qNum, quarterDiv);
            });
        }

        function addPlayerStatsInputsForNewQuarter(qNum) {
            allPlayerStatContainers.forEach(container => {
                const playerId = container.id.split('-')[2]; // Extract player ID
                container.appendChild(createPlayerQuarterStatsHtml(playerId, qNum));
            });
        }

        function createPlayerQuarterStatsHtml(playerId, qNum, stats = {}) {
            const div = document.createElement('div');
            div.classList.add('player-stats');
            div.style.cssText = "display: flex; gap: 10px; align-items: center; text-align: center; flex-wrap: wrap;";
            div.setAttribute('data-quarter-number', qNum);
            div.innerHTML = `
                <label style="font-weight: normal;">Kuarter ${qNum}</label>
                <div style="display: flex; flex-direction: column;">
                    <span>Point</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][point]" min="0" value="${stats.point || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FGM</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][fgm]" min="0" value="${stats.fgm || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FGA</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][fga]" min="0" value="${stats.fga || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FTA</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][fta]" min="0" value="${stats.fta || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FTM</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][ftm]" min="0" value="${stats.ftm || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>ORB</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][orb]" min="0" value="${stats.orb || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>DRB</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][drb]" min="0" value="${stats.drb || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>STL</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][stl]" min="0" value="${stats.stl || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>AST</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][ast]" min="0" value="${stats.ast || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>BLK</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][blk]" min="0" value="${stats.blk || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>PF</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][pf]" min="0" value="${stats.pf || ''}">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>TO</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][to]" min="0" value="${stats.to || ''}">
                </div>
            `;
            return div;
        }

        function removeQuarter(qNumToRemove, quarterDiv) {
            quarterDiv.remove(); // Remove quarter score input

            // Remove corresponding player stats for this quarter
            allPlayerStatContainers.forEach(container => {
                const playerStatsForQuarter = container.querySelector(`.player-stats[data-quarter-number="${qNumToRemove}"]`);
                if (playerStatsForQuarter) {
                    playerStatsForQuarter.remove();
                }
            });

            // Re-index remaining quarters (important for form submission)
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
                // Update button for removing quarter
                let removeButton = qDiv.querySelector('.remove-quarter-btn');
                if (currentQuarter === 1) { // Don't allow removing quarter 1
                    if (removeButton) removeButton.remove();
                } else {
                    if (!removeButton) { // Add back if it was removed
                        removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.classList.add('remove-quarter-btn');
                        removeButton.textContent = 'X';
                        qDiv.appendChild(removeButton);
                        removeButton.addEventListener('click', function() {
                            removeQuarter(currentQuarter, qDiv);
                        });
                    }
                }
                currentQuarter++;
            });

            // Re-index player stats accordingly
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

            quarterCount = currentQuarter - 1; // Update quarterCount after re-indexing
        }
    });
</script>
@endsection