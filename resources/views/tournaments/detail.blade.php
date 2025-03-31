@extends('layouts.app2')

@section('title', 'Detail Lomba')

@section('content')
<head>
    <style>
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 50px 100px rgba(0, 0, 0, 0.4);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #000000;
            
        }

        .card {
            padding: 20px;
            border-radius: 10px;
            background: linear-gradient(135deg, #e0f7fa, rgba(0, 0, 255, 0.1));
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .column {
            flex: 1;
            min-width: 300px;
        }

        .card-title {
            font-size: 24px;
            margin-bottom: 10px;
            color: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }

        .card-text {
            font-size: 16px;
            color: #555;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
            font-size: 16px;
            color: #333;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .badge-ongoing {
            background-color: #28a745;
            color: white;
        }

        .badge-upcoming {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-scheduled {
            background-color: #0091ff;
            color: white;
        }

        .badge-completed {
            background-color: #6c757d;
            color: white;
        }

        .btn-edit {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .btn-edit:active {
            transform: translateY(0);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-success1 {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            /* background: linear-gradient(135deg, #3ce51e, #7bf542); */
            color: black;
            text-decoration: none;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .btn-success1:active {
            transform: translateY(0);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-disabled {
            pointer-events: none;
            background: #949494;
            color: white;
            opacity: 0.5;
            cursor: not-allowed;
        }
        .text-warning1 {
            margin-top: 15px;
            color: #000000;
            font-size: 14px;
        }


        /* POPUP */
        /* Modal Background */
.modal-container {
  display: none; 
  position: fixed; 
  z-index: 1000; 
  left: 0;
  top: 0;
  width: 100%; 
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  align-items: center; 
  justify-content: center;
}

/* Modal Box */
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
  text-align: center;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

/* Tombol dalam modal */
.modal-actions {
  margin-top: 15px;
  display: flex;
  justify-content: space-around;
}

.btn-cancel {
  background: #ccc;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  border: none;
}

.btn-confirm {
  background: #28a745;
  color: white;
  padding: 10px 15px;
  border-radius: 5px;
  text-decoration: none;
}

    </style>
</head>

<div class="container">
    <h2>Detail Lomba</h2>

    <div class="card">
        <div class="row">
            <div class="column">
                <h3 class="card-title" style="font-weight: bold !important;">{{ $tournament->name }}</h3>
                <p class="card-text">{{ $tournament->description }}</p>
            </div>
            <div class="column">
                <ul>
                    <li><strong>Organizer:</strong> {{ $tournament->organizer }}</li>
                    <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($tournament->start_date)->format('d M Y') }}</li>
                    <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($tournament->end_date)->format('d M Y') }}</li>
                    <li><strong>Status:</strong>
                        @if($tournament->status === 'ongoing')
                            <span class="badge badge-ongoing">Ongoing</span>
                        @elseif($tournament->status === 'upcoming')
                            <span class="badge badge-upcoming">Upcoming</span>
                        @elseif($tournament->status === 'scheduled')
                            <span class="badge badge-scheduled">Scheduled</span>
                        @else
                            <span class="badge badge-completed">Completed</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
        <div style="text-align: center;">
            @if(auth()->id() === $tournament->users_id)
                <a href="{{ route('tournament.edit', $tournament->id) }}" class="btn-edit">Edit Detail Tournament</a>

                @if($tournament->status === 'scheduled')
                <form action="{{ route('generate.schedule', $tournament->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-success1" onclick="return confirmGeneration(event)">Generate Bracket & Jadwal</button>
                </form>                
                @elseif($tournament->status === 'upcoming')
                    <a href="#" class="btn-success1 btn-disabled" disabled>Generate Bracket & Jadwal</a>
                    <p class="text-warning1">Untuk Generate Bracket dan Jadwal <br> Pastikan data daftar tim sudah fix dan ubah status tournament anda ke "Scheduled".</p>
                @endif
            @endif
        </div>
       <!-- Modal -->
        <div id="customModal" class="modal" style="display: none;">
            <div class="modal-content">
                <p>Apakah Anda yakin ingin menggenerate bracket dan jadwal? Data sebelumnya akan dihapus.</p>
                <div class="modal-actions">
                    <button class="btn-cancel" onclick="closeModal()">Batal</button>
                    <form action="{{ route('generate.bracket', $tournament->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-confirm">Ya, Generate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    function confirmGeneration(event) {
        event.preventDefault();
        if (confirm("Apakah Anda yakin ingin menggenerate bracket dan jadwal? Jika Anda sudah pernah melakukan generate sebelumnya, data sebelumnya akan dihapus dan dibuat ulang. Jika ini pertama kali, tidak ada masalah.")) {
            event.target.closest("form").submit();
        }
    }

    function openModal() {
        document.getElementById("customModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("customModal").style.display = "none";
    }
</script>