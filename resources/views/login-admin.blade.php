<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SAFRECO - GSM | Connexion</title>
    <meta content="Système de gestion de stocks et de trésorerie - SAFRECO GSM" name="description">

    <!-- Favicon -->
    <link href="{{ asset('images/logo_gcm.jpeg') }}" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS (local) -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Bootstrap Icons (local) -->
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --green-600: #1976d2;
            --green-700: #1565c0;
            --slate-900: #0a0a0a;
            --slate-600: #3d3d3d;
            --slate-500: #6b6b6b;
            --slate-200: #e4e7e4;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
        }

        /* ── Watermark ── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 900px;
            max-width: 130vw;
            transform: translate(-50%, -50%) rotate(-8deg);
            opacity: 0.055;
            filter: grayscale(35%);
            pointer-events: none;
            user-select: none;
            z-index: 0;
        }

        .login-wrap {
            width: 100%;
            max-width: 408px;
            animation: fadeSlideUp 0.5s ease both;
            position: relative;
            z-index: 1;
        }

        /* ── Card ── */
        .login-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .06), 0 20px 48px rgba(15, 23, 42, .10);
            padding: 2.5rem 2.25rem;
        }

        /* ── Brand ── */
        .brand {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            object-fit: cover;
            display: block;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .12);
        }

        .brand-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.2px;
        }

        .brand-name span {
            color: var(--green-600);
        }

        /* ── Header ── */
        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.4px;
            margin-bottom: 0.3rem;
            text-align: center;
        }

        .form-subtitle {
            font-size: 0.85rem;
            color: var(--slate-500);
            text-align: center;
            margin-bottom: 1.75rem;
        }

        /* ── Field group ── */
        .field-group {
            margin-bottom: 1.1rem;
        }

        .field-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
            display: block;
        }

        .field-wrap {
            position: relative;
        }

        .field-wrap .f-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.15s;
            z-index: 1;
        }

        .field-wrap input {
            width: 100%;
            padding: 0.72rem 1rem 0.72rem 2.6rem;
            border: 1.5px solid var(--slate-200);
            border-radius: 9px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: var(--slate-900);
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .field-wrap input::placeholder {
            color: #c8d3e0;
        }

        .field-wrap input:focus {
            border-color: var(--green-600);
            box-shadow: 0 0 0 3.5px rgba(56, 142, 60, 0.12);
        }

        .field-wrap input:focus~.f-icon {
            color: var(--green-600);
        }

        .field-wrap input.is-invalid {
            border-color: #ef4444;
        }

        .field-error {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-toggle-pw {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: color 0.15s, background 0.15s;
            z-index: 2;
        }

        .btn-toggle-pw:hover {
            color: var(--green-600);
            background: rgba(56, 142, 60, 0.07);
        }

        /* ── Remember row ── */
        .remember-row {
            display: flex;
            align-items: center;
            margin-bottom: 1.4rem;
        }

        .check-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .check-label input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--green-600);
            border-radius: 4px;
            cursor: pointer;
        }

        .check-label span {
            font-size: 0.82rem;
            color: var(--slate-600);
            font-weight: 500;
        }

        /* ── Alerts ── */
        .alert-box {
            border-radius: 9px;
            padding: 0.75rem 0.9rem;
            font-size: 0.8rem;
            margin-bottom: 1.1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
        }

        .alert-box.is-error {
            background: #fff1f2;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }

        .alert-box.is-success {
            background: #f0fdf4;
            color: #0d47a1;
            border: 1px solid #bbf7d0;
        }

        .alert-box .a-icon {
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-box .a-close {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            opacity: 0.5;
            color: inherit;
            padding: 0;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .alert-box .a-close:hover {
            opacity: 1;
        }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%;
            padding: 0.8rem 1rem;
            background: var(--green-600);
            border: none;
            border-radius: 9px;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.15s;
            position: relative;
        }

        .btn-submit:hover {
            background: var(--green-700);
        }

        .btn-submit.loading .btn-text {
            opacity: 0;
        }

        .btn-submit.loading .btn-spinner {
            display: flex;
        }

        .btn-spinner {
            display: none;
            position: absolute;
            align-items: center;
            gap: 0.5rem;
        }

        .spinner-ring {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Footer ── */
        .login-footer {
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 1.5rem;
        }

        .login-footer a {
            color: var(--slate-500);
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            color: var(--green-600);
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>

<body>

    <img src="{{ asset('images/logo_laafi_tiim.jpeg') }}" alt="" class="watermark" aria-hidden="true">

    <div class="login-wrap">
        <div class="brand">
            <img src="{{ asset('images/logo_laafi_tiim.jpeg') }}" alt="MARKET PLACE" class="brand-logo">
            <div class="brand-name">MARKET <span>PLACE</span></div>
        </div>

        <div class="login-card">
            <h1 class="form-title">Connexion</h1>
            <p class="form-subtitle">Accédez à votre espace de gestion</p>

            {{-- ── Alerts ── --}}
            @if ($errors->any())
                <div class="alert-box is-error">
                    <i class="bi bi-exclamation-circle-fill a-icon"></i>
                    <div>
                        <strong>Identifiants incorrects</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button class="a-close" onclick="this.closest('.alert-box').remove()"><i
                            class="bi bi-x-lg"></i></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-box is-error">
                    <i class="bi bi-exclamation-circle-fill a-icon"></i>
                    <div><strong>Erreur : </strong>{{ session('error') }}</div>
                    <button class="a-close" onclick="this.closest('.alert-box').remove()"><i
                            class="bi bi-x-lg"></i></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert-box is-success">
                    <i class="bi bi-check-circle-fill a-icon"></i>
                    <div><strong>Succès : </strong>{{ session('success') }}</div>
                    <button class="a-close" onclick="this.closest('.alert-box').remove()"><i
                            class="bi bi-x-lg"></i></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login_admin') }}" id="loginForm">
                @csrf

                {{-- Telephone --}}
                <div class="field-group">
                    <label class="field-label" for="telephone">Numéro de téléphone</label>
                    <div class="field-wrap">
                        <i class="bi bi-telephone f-icon"></i>
                        <input type="text" name="telephone" id="telephone" placeholder="123-456-7890"
                            value="{{ old('telephone') }}"
                            class="{{ $errors->has('telephone') ? 'is-invalid' : '' }}" required autocomplete="telephone">
                    </div>
                    @error('telephone')
                        <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <label class="field-label" for="password">Mot de passe</label>
                    <div class="field-wrap">
                        <i class="bi bi-lock f-icon"></i>
                        <input type="password" name="password" id="password" placeholder="••••••••"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required
                            autocomplete="current-password" style="padding-right:2.75rem;">
                        <button type="button" class="btn-toggle-pw" onclick="togglePw()" title="Afficher/Masquer">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="remember-row">
                    <label class="check-label">
                        <input type="checkbox" name="remember" value="true" id="rememberMe">
                        <span>Se souvenir de moi</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="btn-text">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Se connecter
                    </span>
                    <span class="btn-spinner">
                        <span class="spinner-ring"></span>
                        Connexion en cours…
                    </span>
                </button>
            </form>
        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} MARKET PLACE · Développé par <a href="#">Mokutech</a>
        </div>
    </div>

    <script>
        function togglePw() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });
    </script>

</body>

</html>
