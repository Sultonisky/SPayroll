<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="At-Tarbiyah Admin Dashboard">
    <meta name="author" content="At-Tarbiyah">
    <title>S-Payroll - Dashboard</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" sizes="16x16" href="{{ asset('assets/images/logo.svg') }}">

    {{-- CoreUI & Vendors Styles --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@6.2.5/dist/simplebar.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.0.0/dist/css/coreui.min.css" rel="stylesheet">
    
    {{-- FontAwesome --}}
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    @stack('styles')

    {{-- Custom Admin CSS --}}
    <link href="{{ asset('assets/dashboard/style/coreui.css') }}" rel="stylesheet">

    {{-- CoreUI Config & Color Modes --}}
    <script src="{{ asset('assets/dashboard/js/config.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/color-modes.js') }}"></script>
    
    <style>
        .sidebar-brand-icon i {
            font-size: 2rem;
        }
        .header-nav .nav-link {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    </style>
</head>

<body>
    {{-- Sidebar --}}
    @include('layouts.sidebar')

    <div class="wrapper d-flex flex-column min-vh-100">
        {{-- Header/Navbar --}}
        @include('layouts.navbar')

        <div class="body flex-grow-1">
            <div class="container-lg px-4">
                {{-- Alert Component --}}
                <x-alert />

                {{-- Main Content --}}
                @yield('contents')
            </div>
        </div>

        {{-- Footer --}}
        @include('layouts.footer')
    </div>

    {{-- Form Logout --}}
    <form id="keluar-app" action="{{ route('admin.logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    {{-- Scripts --}}
    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    {{-- CoreUI & Vendors Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/simplebar@6.2.5/dist/simplebar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.0.0/dist/js/coreui.bundle.min.js"></script>

    {{-- DataTables & Buttons (Bootstrap 5) --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    {{-- Support Libraries for DataTables --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/custom-admin.js') }}"></script>

    @stack('scripts')
</body>

</html>
