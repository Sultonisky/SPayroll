<header class="header header-sticky mb-4 p-0">
    <div class="container-fluid border-bottom px-4" style="height: 64px;">
        <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px">
            <i class="fas fa-bars icon icon-lg"></i>
        </button>
        
        <ul class="header-nav ms-auto">
            {{-- Notifications --}}
            <li class="nav-item dropdown">
                <a class="nav-link" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell icon icon-lg"></i>
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge badge-pill bg-danger">
                            {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end pt-0 shadow">
                    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">
                        Pusat Notifikasi
                    </div>
                    @forelse (auth()->user()->unreadNotifications->take(5) as $notification)
                        <a class="dropdown-item d-flex align-items-center" href="{{ $notification->data['url'] ?? '#' }}">
                            <div class="me-3">
                                <div class="icon-circle bg-{{ $notification->data['type'] ?? 'primary' }} text-white p-2 rounded-circle">
                                    <i class="fas {{ $notification->data['type'] == 'success' ? 'fa-check' : ($notification->data['type'] == 'warning' ? 'fa-exclamation-triangle' : 'fa-info') }}"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-body-secondary">{{ $notification->created_at->diffForHumans() }}</div>
                                <div class="fw-bold">{{ $notification->data['title'] }}</div>
                                <div class="small text-truncate" style="max-width: 200px;">{{ $notification->data['message'] }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="dropdown-item text-center small text-body-secondary">Tidak ada notifikasi baru</div>
                    @endforelse
                    
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('notifications.markAllRead') }}" method="POST" id="mark-all-read-form">
                            @csrf
                            <button type="submit" class="dropdown-item text-center small text-body-secondary border-0 bg-transparent w-100">
                                Tandai semua telah dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </li>
        </ul>

        <ul class="header-nav">
            <li class="nav-item dropdown">
                <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button" aria-expanded="false" data-coreui-toggle="dropdown">
                    <i class="fas fa-adjust icon icon-lg theme-icon-active"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem">
                    <li>
                        <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="light">
                            <i class="fas fa-sun icon me-3"></i> Light
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="dark">
                            <i class="fas fa-moon icon me-3"></i> Dark
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item d-flex align-items-center active" type="button" data-coreui-theme-value="auto">
                            <i class="fas fa-circle-half-stroke icon me-3"></i> Auto
                        </button>
                    </li>
                </ul>
            </li>
            <li class="nav-item py-1">
                <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            {{-- User Profile --}}
            <li class="nav-item dropdown">
                <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-md">
                        <img class="avatar-img rounded-circle" src="{{ auth()->user()->foto_url ?? asset('assets/images/logo.svg') }}">
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end pt-0 shadow">
                    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">
                        {{ auth()->user()->name ?? auth()->user()->nama }}
                        <div class="small fw-bold text-danger">{{ auth()->user()->role }}</div>
                    </div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-user icon me-2 text-body-secondary"></i>
                        Profil Saya
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="" onclick="event.preventDefault(); document.getElementById('keluar-app').submit();">
                        <i class="fas fa-sign-out-alt icon me-2 "></i>
                        Keluar
                    </a>
                </div>
            </li>
        </ul>
    </div>
    <div class="container-fluid px-4 py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a>
                </li>
                @php
                    $segments = request()->segments();
                    $url = '';
                    // Hapus 'admin' dari breadcrumb jika ada di segmen pertama
                    if (isset($segments[0]) && $segments[0] == 'admin') {
                        array_shift($segments);
                    }
                @endphp
                @foreach ($segments as $index => $segment)
                    @php
                        $url .= '/' . $segment;
                        $isLast = $index === count($segments) - 1;
                        $label = str_replace('-', ' ', $segment);
                        $label = ucwords($label);
                        
                        // Custom labels if needed
                        if ($segment == 'lpya') $label = 'LPYA';
                    @endphp

                    @if ($isLast)
                        <li class="breadcrumb-item active">
                            <span>@yield('title', $label)</span>
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            @if (Route::has('admin.' . $segment . '.index'))
                                <a href="{{ route('admin.' . $segment . '.index') }}" class="text-decoration-none">{{ $label }}</a>
                            @else
                                <span class="text-body-secondary">{{ $label }}</span>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
</header>