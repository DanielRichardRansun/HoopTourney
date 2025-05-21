<!DOCTYPE html>
<html lang="en" class="no-js">

<style>
    /* Responsive sidebar hidden di mobile */
.responsive-sidebar {
    width: 250px;
    transition: all 0.3s ease;
    background-color: #2c3e50;
}

@media (max-width: 768px) {
    .page-sidebar-menu {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    height: auto !important;
    overflow-y: auto !important;
    padding-left: 0;
    margin-top: 60px; /* Agar tidak ketimpa tombol hamburger */
    background-color: #2c3e50;
}

.page-sidebar-menu li a {
    color: white !important;
    padding: 10px 20px;
    display: block;
}

.page-sidebar-menu li a:hover {
    background-color: #1a252f;
    text-decoration: none;
}
    .responsive-sidebar {
        position: fixed;
        top: 0;
        left: -250px; /* sembunyikan sidebar */
        height: 100%;
        z-index: 1040;
    }

    .responsive-sidebar.active {
        left: 0; /* tampilkan sidebar */
    }

    .page-content-wrapper {
        margin-left: 0 !important;
    }

    .mobile-toggle {
    z-index: 1051; /* lebih tinggi dari sidebar */
    position: fixed;
    top: 10px;
    left: 10px;
}

/* Tambahkan padding atas ke sidebar biar kontennya nggak ketiban hamburger */
.responsive-sidebar {
    padding-top: 60px; /* atau lebih jika perlu */
}

/* Atur tombol back (jika posisinya absolute) */
.back-button {
    position: relative;
    margin-bottom: 10px;
    margin-left: 20px;
}

/* Tambahkan sedikit margin antar menu */
.page-sidebar-menu > li {
    margin-bottom: 5px;
}
}
.back-button {
    margin-top: 10px;
    margin-left: 20px;
    z-index: 1;
}

</style>

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

<div class="page-container">

<div class="mobile-toggle d-md-none d-block p-2">
    <button class="btn" id="sidebarToggle" style="position: fixed; top: 10px; left: 10px; z-index: 1050;">
    <i class="fas fa-bars"></i>
</button>

</div>


    <div class="page-sidebar-wrapper responsive-sidebar" id="sidebar">
        <div class="page-sidebar navbar-collapse">
            <ul class="page-sidebar-menu">
                <!-- Sidebar Toggler (untuk tampilan mobile) -->
                <li class="sidebar-toggler-wrapper">
                    <div class="sidebar-toggler">
                        <i class="fa fa-bars"></i>
                    </div>
                </li>

                <div class="back-button">
    <a href="{{ url('/') }}" class="btn btn-primary">
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
        <div class="page-content" style="
            background-image: url('{{ asset('images/website_background3.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;">
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

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    });
</script>
</html>