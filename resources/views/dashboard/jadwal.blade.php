@extends('layouts.app2')

@section('content')
<style>
    .container {
        max-width: 100%;
    }
    .schedule-card {
        border: 2px solid #1e3c72;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
    }
    .schedule-title {
        font-weight: bold;
        text-align: center;
        background-color: #1e3c72;
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .team-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: center;
    }
    .team {
        flex: 1;
        font-size: 1.2em;
        padding: 10px;
        font-weight: bold;
    }
    .score {
        font-size: 1.5em;
        font-weight: bold;
        margin: 0 10px;
    display: inline-block; /
    }
    .details {
        text-align: center;
        margin-top: 10px;
    }
    .score-container {
    display: flex;
    justify-content: space-between;
    width: 100%;
    gap: 20px;
    }
    .score-box {
    font-size: 1.5rem;
    font-weight: bold; 
    padding: 10px 20px; 
    border-radius: 8px;
    text-align: center;
    min-width: 60px;
    }
    h2.text-center {
    margin-bottom: 20px;
}
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    width: 350px;
    border-radius: 8px;
    position: relative;
    text-align: center;
}

.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    cursor: pointer;
}

input {
    width: 100%;
    padding: 8px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.btn-save {
    background-color: #28a745;
    color: white;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
    width: 100%;
    border-radius: 5px;
}

.btn-save:hover {
    background-color: #218838;
}

/* Floating Form */
.modal {
    display: none; 
    position: fixed; 
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
}
.modal-content {
    background: #fff;
    width: 350px;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.2);
    position: relative;
}
.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    cursor: pointer;
}
input, select {
    width: 100%;
    padding: 8px;
    margin: 5px 0 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.modal-footer {
    display: flex;
    justify-content: space-between;
}
.btn {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn.cancel { background: #d9534f; color: white; }
.btn.save { background: #4CAF50; color: white; }
.btn.edit { background: #007BFF; color: white; }

.btn.cancel:hover { background: #c9302c; }
.btn.save:hover { background: #45a049; }
.btn.edit:hover { background: #0056b3; }

.status-badge {
    padding: 4px 12px;
    border-radius: 8px;
    font-weight: bold;
    display: inline-block;
    color: white;
}

.status-scheduled {
    background-color: #9b9b9b;
}

.status-postponed {
    background-color: #ffc107;
}

.status-cancelled {
    background-color: #dc3545;
}

.status-completed {
    background-color: #198754;
}


</style>
<div class="container mt-5">
    <h2 class="text-center" style="font-weight: bold;">Jadwal Lomba {{ $tournament->name }}</h2>

    <div class="text-center mb-4">
        <label for="filterStatus"><strong>Filter Status:</strong></label>
        <select id="filterStatus" class="form-control w-25 d-inline" onchange="filterSchedule()">
            <option value="all">All</option>
            <option value="Scheduled">Scheduled</option>
            <option value="Postponed">Postponed</option>
            <option value="Cancelled">Cancelled</option>
            <option value="Completed">Completed</option>
        </select>
    </div>

    
    @if($schedules->isEmpty())
        <div class="alert alert-warning text-center">No Schedule available in this tournament.</div>
    @else
        @foreach($schedules as $index => $schedule)
        @php
        $team1Class = '';
        $team2Class = '';
        if ($schedule->matchResult) {
            $score1 = $schedule->matchResult->team1_score;
            $score2 = $schedule->matchResult->team2_score;

            if ($score1 !== null && $score2 !== null) {
                if ($score1 == $score2) {
                    // Jika seri
                    $team1Class = 'bg-secondary text-white';
                    $team2Class = 'bg-secondary text-white';
                } elseif ($schedule->matchResult->winning_team_id == $schedule->team1->id) {
                    $team1Class = 'bg-success text-white';
                    $team2Class = 'bg-danger text-white';
                } elseif ($schedule->matchResult->winning_team_id == $schedule->team2->id) {
                    $team1Class = 'bg-danger text-white';
                    $team2Class = 'bg-success text-white';
                }
            }
        }
    @endphp

            <div class="schedule-card" data-status="{{ strtolower($schedule->status) }}">
                <div class="schedule-title">GAME {{ $index + 1 }}</div>
                <div class="team-container">
                    <div class="team">{{ $schedule->team1 ? $schedule->team1->name : 'TBD' }}</div>
                    <div class="d-flex justify-content-between">
                        <div class="score-container">
                            <div class="score-box {{ $team1Class }}">{{ $schedule->matchResult->team1_score ?? '-' }}</div>
                            <div class="score-box {{ $team2Class }}">{{ $schedule->matchResult->team2_score ?? '-' }}</div>
                        </div>
                    </div>                
                    <div class="team">{{ $schedule->team2 ? $schedule->team2->name : 'TBD' }}</div>
                </div>
                <div class="details">
                    <p>Waktu: {{ \Carbon\Carbon::parse($schedule->date)->format('d M Y (H:i)') }}</p>
                    <p>
                        <span class="status-badge 
                            @if($schedule->status == 'Scheduled') status-scheduled
                            @elseif($schedule->status == 'Postponed') status-postponed
                            @elseif($schedule->status == 'Cancelled') status-cancelled
                            @elseif($schedule->status == 'Completed') status-completed
                            @endif">
                            {{ ucfirst($schedule->status) }}
                        </span>
                    </p>
                    <p>Lokasi: {{ $schedule->location }}</p>
                
                    @if ($isAdmin)
    <div class="button-group">
        <a href="javascript:void(0);" class="btn btn-warning btn-custom" onclick="openModal({{ $schedule->id }})">
            <i class="fas fa-edit"></i> Edit Detail Jadwal
        </a>
        
        @if ($schedule->matchResult)
            <!-- Jika match_result sudah ada, tampilkan tombol Edit -->
            <a href="{{ route('matchResults.edit', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" 
               class="btn btn-primary btn-custom">
                <i class="fas fa-edit"></i> Edit Hasil Pertandingan
            </a>
        @else
            <!-- Jika match_result belum ada, tampilkan tombol Insert -->
            <a href="{{ route('matchResults.create', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" 
               class="btn btn-custom" style="background-color: #198754; border-color: #198754; color: white;">
                <i class="fas fa-file-alt"></i> Insert Hasil Pertandingan
            </a>                    
        @endif                    
    </div>
@endif

                </div>             
            </div>

            <!-- Floating Form EDIT -->
            <div id="editScheduleModal-{{ $schedule->id }}" class="modal" style="display: none;">

                <div class="modal-content">
                    <span class="close" onclick="closeModal({{ $schedule->id }})">&times;</span>
                    <h3 style="margin-bottom: 25px;">Edit Detail Jadwal</h3>
                    <form action="{{ route('schedule.update', $schedule->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group" style="margin-bottom: 3px;">
                            <label for="team1">Tim 1:</label>
                            @if(is_null($schedule->team1_id) || session('edit_team1')) 
                                <select name="team1_id">
                                    <option value="">Pilih Tim</option>
                                    @foreach($tournamentTeams as $team)
                                        <option value="{{ $team->id }}" 
                                            {{ old('team1_id', $schedule->team1_id) == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @php session(['edit_team1' => true]); @endphp
                            @else
                                <input type="hidden" name="team1_id" value="{{ $schedule->team1_id }}">
                                <span>{{ $schedule->team1->name }}</span>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="team2">Tim 2:</label>
                            @if(is_null($schedule->team2_id) || session('edit_team2')) 
                                <select name="team2_id">
                                    <option value="">Pilih Tim</option>
                                    @foreach($tournamentTeams as $team)
                                        <option value="{{ $team->id }}" 
                                            {{ old('team2_id', $schedule->team2_id) == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @php session(['edit_team2' => true]); @endphp
                            @else
                                <input type="hidden" name="team2_id" value="{{ $schedule->team2_id }}">
                                <span>{{ $schedule->team2->name }}</span>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="date">Tanggal:</label>
                            <input type="datetime-local" name="date" value="{{ \Carbon\Carbon::parse($schedule->date)->format('Y-m-d\TH:i') }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Lokasi:</label>
                            <input type="text" name="location" value="{{ $schedule->location }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select name="status" required>
                                <option value="Scheduled" {{ $schedule->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="Postponed" {{ $schedule->status == 'Postponed' ? 'selected' : '' }}>Postponed</option>
                                <option value="Cancelled" {{ $schedule->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="Completed" {{ $schedule->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-save">Simpan Perubahan</button>
                        
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection

<script>
    function openModal(scheduleId) {
        document.getElementById('editScheduleModal-' + scheduleId).style.display = "flex";
    }

    function closeModal(scheduleId) {
        document.getElementById('editScheduleModal-' + scheduleId).style.display = "none";
    }

    // Tutup modal jika klik di luar
    window.onclick = function(event) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }

    //Filter Status
    function filterSchedule() {
    let selectedStatus = document.getElementById("filterStatus").value.toLowerCase();
    let scheduleCards = document.querySelectorAll(".schedule-card");

    scheduleCards.forEach(card => {
        let status = card.getAttribute("data-status");
        if (selectedStatus === "all" || status === selectedStatus) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
}
</script>
