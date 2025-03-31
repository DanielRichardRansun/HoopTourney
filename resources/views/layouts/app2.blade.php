<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8" />
    <title>HOOPTOURNEY | Tugas Akhir</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="MobileOptimized" content="320">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style-responsive.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/themes/default.css') }}" rel="stylesheet" type="text/css" id="style_color" />
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<div class="clearfix">
</div>
<div class="page-container">
    <div class="page-sidebar-wrapper">
        <div class="page-sidebar navbar-collapse collapse">
            <ul class="page-sidebar-menu">
                <!-- Sidebar Toggler (untuk tampilan mobile) -->
                <li class="sidebar-toggler-wrapper">
                    <div class="sidebar-toggler">
                        <i class="fa fa-bars"></i>
                    </div>
                    <div class="clearfix"></div>
                </li>

                <!-- Back Button -->
                <div class="back-button">
                    <a href="{{ url('/') }}" class="btn btn-primary" style="position: absolute; left: 20px; top: 10px;">
                        <i class="fa fa-arrow-left"></i> Home
                    </a>
                </div>

                <!-- Detail Tournament -->
                <li class="start {{ Request::is('tournament/detail/*') ? 'active' : '' }}">
                    <a href="{{ route('tournament.detail', $tournament->id ?? 0) }}">
                        <i class="fas fa-info-circle"></i>
                        <span class="title">Detail Tournament</span>
                    </a>
                </li>

                <!-- Bracket -->
                <li class="start {{ Request::is('dashboard/bracket/*') ? 'active' : '' }}">
                    <a href="{{ route('tournament.bracket', $tournament->id ?? 0) }}">
                        <i class="fas fa-project-diagram"></i>
                        <span class="title">Bracket</span>
                    </a>
                </li>

                <!-- Teams -->
                <li class="start {{ Request::is('dashboard/tournament/*/teams') ? 'active' : '' }}">
                    <a href="{{ route('tournament.teams', $tournament->id ?? 0) }}">
                        <i class="fas fa-users"></i>
                        <span class="title">Teams</span>
                    </a>
                </li>

                <!-- Klasemen -->
                <li class="start {{ Request::is('dashboard/klasemen/*') ? 'active' : '' }}">
                    <a href="{{ route('tournament.klasemen', $tournament->id ?? 0) }}">
                        <i class="fa fa-trophy"></i>
                        <span class="title">Klasemen</span>
                    </a>
                </li>

                <!-- Jadwal -->
                <li class="start {{ Request::is('dashboard/jadwal/*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.jadwal', $tournament->id ?? 0) }}">
                        <i class="fa fa-calendar-alt"></i>
                        <span class="title">Jadwal</span>
                    </a>
                </li>

                <!-- Statistik -->
                <li class="start {{ Request::is('dashboard/statistik/*') ? 'active' : '' }}">
                    <a href="{{ route('statistik', $tournament->id ?? 0) }}">
                        <i class="fas fa-chart-bar"></i>
                        <span class="title">Statistik</span>
                    </a>
                </li>

                @auth
                <li class="start">
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-power-off"></i>
                        <span class="title">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
                @endauth
            </ul>
        </div>
    </div>
    <div class="page-content-wrapper">
        <div class="page-content">
            @yield('content')
        </div>
    </div>
</div>
<div class="footer">
    <div class="footer-inner">
        2024 &copy; HOOPTOURNEY.
    </div>
    <div class="footer-tools">
        <span class="go-top">
            <i class="fa fa-angle-up"></i>
        </span>
    </div>
</div>
</script>

{{-- Side bar tampilan hp --}}
<script>
    document.querySelector('.sidebar-toggler').addEventListener('click', function() {
        var sidebar = document.querySelector('.page-sidebar-wrapper');
        sidebar.classList.toggle('active'); 
    });
</script>
</html>