<!DOCTYPE html>
<html lang="fr">

<head>
    @include('partials.style')
    <script>
        try {
            if (localStorage.getItem('mp-sidebar') === 'collapsed') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    </script>
</head>

<body class="app-body">
    @include('partials.sidebar')

    <div class="app-main">
        @include('partials.topNav')

        <main id="main" class="app-content">
            @if (session('success') || session('error'))
                <div class="app-toast-wrap">
                    <div class="toast align-items-center text-white {{ session('success') ? 'bg-success' : 'bg-danger' }} border-0"
                        role="alert" aria-live="assertive" aria-atomic="true" id="statusToast" data-bs-delay="5000">
                        <div class="d-flex">
                            <div class="toast-body">
                                @if (session('success'))
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <strong>{{ session('success') }}</strong>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        <strong>{{ session('error') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="app-page">
                @yield('content')
            </div>

            @include('partials.footer')
        </main>
    </div>

    <a href="#" class="back-to-top align-items-center justify-content-center" aria-label="Haut de page">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    @include('partials.script')
    @yield('scripts')

    @if (session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toastElement = document.getElementById('statusToast');
                if (toastElement && window.bootstrap) {
                    bootstrap.Toast.getOrCreateInstance(toastElement).show();
                }
            });
        </script>
    @endif
</body>

</html>
