<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>MARKET PLACE | Connexion</title>
    <meta content="Système de gestion de stocks et de trésorerie" name="description">
    <link href="{{ asset('images/logo_gcm.jpeg') }}" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        :root {
            --navy: #0c3d78;
            --navy-deep: #08284f;
            --red: #b42318;
            --ink: #172033;
            --muted: #5d6b80;
            --line: #e3e9f2;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: "Source Sans 3", "Segoe UI", sans-serif;
            color: var(--ink);
            background: #eef2f7;
        }

        .auth {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(280px, 42%) 1fr;
        }

        .auth-aside {
            background: var(--navy-deep);
            color: #fff;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .auth-aside::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: var(--red);
        }

        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .auth-brand img {
            width: 196px;
            height: 58px;
            object-fit: contain;
            background: #fff;
            border-radius: 6px;
            padding: 4px 8px;
        }

        .auth-brand strong {
            display: block;
            font-family: "Source Serif 4", Georgia, serif;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
        }

        .auth-brand span {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.72);
        }

        .auth-aside h1 {
            margin-top: 64px;
            font-family: "Source Serif 4", Georgia, serif;
            font-size: 2.4rem;
            font-weight: 600;
            line-height: 1.15;
            letter-spacing: -0.03em;
            max-width: 11ch;
        }

        .auth-aside p {
            margin-top: 16px;
            max-width: 34ch;
            color: rgba(255, 255, 255, 0.78);
            font-size: 1.02rem;
            line-height: 1.5;
        }

        .auth-points {
            list-style: none;
            margin-top: 36px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .auth-points li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.88);
        }

        .auth-points i {
            width: 28px;
            height: 28px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: grid;
            place-items: center;
            font-size: 0.85rem;
        }

        .auth-aside small {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.78rem;
        }

        .auth-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
        }

        .auth-kicker {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--red);
        }

        .auth-card h2 {
            margin-top: 6px;
            font-family: "Source Serif 4", Georgia, serif;
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: -0.03em;
        }

        .auth-card .lead {
            margin: 8px 0 28px;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .alert-box {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 0.86rem;
            border: 1px solid #fecdd3;
            background: #fff5f5;
            color: #9f1239;
        }

        .alert-box.is-success {
            border-color: #c5d4ea;
            background: #f4f7fb;
            color: var(--navy);
        }

        .alert-box ul { margin: 4px 0 0 16px; }

        .alert-box button {
            margin-left: auto;
            border: 0;
            background: none;
            color: inherit;
            cursor: pointer;
        }

        .field { margin-bottom: 16px; }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .field-wrap { position: relative; }

        .field-wrap i.f-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #8b98ab;
        }

        .field-wrap input {
            width: 100%;
            height: 46px;
            padding: 0 42px 0 38px;
            border: 1px solid #d5deea;
            border-radius: 5px;
            background: #fff;
            font: inherit;
            font-size: 0.95rem;
            color: var(--ink);
            outline: none;
        }

        .field-wrap input:focus {
            border-color: var(--navy);
            box-shadow: 0 0 0 3px rgba(12, 61, 120, 0.12);
        }

        .field-wrap input.is-invalid { border-color: var(--red); }

        .field-error {
            display: block;
            margin-top: 6px;
            color: var(--red);
            font-size: 0.78rem;
        }

        .btn-toggle-pw {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: none;
            color: #8b98ab;
            cursor: pointer;
            padding: 4px 6px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0 22px;
            font-size: 0.88rem;
            color: var(--muted);
        }

        .remember input { accent-color: var(--navy); }

        .btn-submit {
            width: 100%;
            height: 48px;
            border: 0;
            border-radius: 5px;
            background: var(--navy);
            color: #fff;
            font: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover { background: #0a3264; }
        .btn-submit:disabled { opacity: 0.75; cursor: wait; }
        .btn-spinner { display: none; }
        .btn-submit.loading .btn-text { display: none; }
        .btn-submit.loading .btn-spinner { display: inline-flex; align-items: center; gap: 8px; }

        .spinner-ring {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .auth-note {
            margin-top: 22px;
            font-size: 0.78rem;
            color: #8b98ab;
        }

        @media (max-width: 860px) {
            .auth { grid-template-columns: 1fr; }
            .auth-aside { padding: 28px 24px 32px; }
            .auth-aside h1 { margin-top: 28px; font-size: 1.8rem; }
            .auth-points, .auth-aside small { display: none; }
        }
    </style>
</head>

<body>
    <main class="auth">
        <section class="auth-aside">
            <div>
                <div class="auth-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="EKDF eau Bisimilah">
                    <div>
                        <strong>Market Place</strong>
                        <span>Stocks & trésorerie</span>
                    </div>
                </div>
                <h1>Gérez chaque magazin avec précision.</h1>
                <p>Ventes, achats, stock et encaissements, réunis dans un seul espace.</p>
                <ul class="auth-points">
                    <li><i class="bi bi-receipt"></i> Factures et bons de commande</li>
                    <li><i class="bi bi-box-seam"></i> Stock réel par magazin</li>
                    <li><i class="bi bi-cash-coin"></i> Encaissements et dépenses</li>
                </ul>
            </div>
            <small>&copy; {{ date('Y') }} Market Place</small>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-kicker">Espace privé</div>
                <h2>Connexion</h2>
                <p class="lead">Entrez votre numéro et votre mot de passe.</p>

                @if ($errors->any())
                    <div class="alert-box">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div>
                            <strong>Identifiants incorrects</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" onclick="this.closest('.alert-box').remove()" aria-label="Fermer"><i class="bi bi-x-lg"></i></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert-box">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" onclick="this.closest('.alert-box').remove()" aria-label="Fermer"><i class="bi bi-x-lg"></i></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert-box is-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" onclick="this.closest('.alert-box').remove()" aria-label="Fermer"><i class="bi bi-x-lg"></i></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login_admin') }}" id="loginForm">
                    @csrf
                    <div class="field">
                        <label for="telephone">Numéro de téléphone</label>
                        <div class="field-wrap">
                            <i class="bi bi-telephone f-icon"></i>
                            <input type="text" name="telephone" id="telephone" placeholder="00 00 00 00"
                                value="{{ old('telephone') }}"
                                class="{{ $errors->has('telephone') ? 'is-invalid' : '' }}"
                                required autocomplete="username">
                        </div>
                        @error('telephone')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Mot de passe</label>
                        <div class="field-wrap">
                            <i class="bi bi-lock f-icon"></i>
                            <input type="password" name="password" id="password" placeholder="Votre mot de passe"
                                class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                                required autocomplete="current-password">
                            <button type="button" class="btn-toggle-pw" onclick="togglePw()" title="Afficher ou masquer">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <label class="remember">
                        <input type="checkbox" name="remember" value="true" id="rememberMe">
                        Se souvenir de moi
                    </label>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="btn-text">Se connecter</span>
                        <span class="btn-spinner"><span class="spinner-ring"></span> Connexion…</span>
                    </button>
                </form>

                <p class="auth-note">Développé par Mokutech</p>
            </div>
        </section>
    </main>

    <script>
        function togglePw() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            icon.classList.toggle('bi-eye', visible);
            icon.classList.toggle('bi-eye-slash', !visible);
        }

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });
    </script>
</body>

</html>
