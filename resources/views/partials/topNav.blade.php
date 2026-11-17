@php
    $user = auth()->user();
    $initial = strtoupper(mb_substr($user->prenom ?? $user->nom ?? 'U', 0, 1));
    $fullName = trim(($user->prenom ?? '') . ' ' . ($user->nom ?? 'Utilisateur'));
    $role = \App\Support\Acces::libelle($user->role ?? null);

    $route = optional(request()->route())->getName() ?? '';
    $pageTitle = match (true) {
        $route === 'dashboard' => 'Tableau de bord',
        $route === 'depots.accueil' => 'Magazins',
        str_starts_with($route, 'gestions_ventes') || str_starts_with($route, 'ventes.') => 'Ventes',
        str_starts_with($route, 'gestions_bons_commandes') || str_starts_with($route, 'bons_commandes.') => 'Bons de commande',
        str_starts_with($route, 'gestions_achats') || str_starts_with($route, 'achats.') => 'Achats',
        str_starts_with($route, 'gestions_entrees') || str_starts_with($route, 'entrees.') => 'Entrées de stock',
        str_starts_with($route, 'gestions_sorties') || str_starts_with($route, 'sorties.') => 'Sorties de stock',
        str_starts_with($route, 'gestions_transferts') || str_starts_with($route, 'transferts.') => 'Transferts',
        str_starts_with($route, 'gestions_inventaires') => 'Inventaire',
        str_starts_with($route, 'gestions_encaissements') || str_starts_with($route, 'encaissements.') => 'Encaissements',
        str_starts_with($route, 'gestions_decaissements') || str_starts_with($route, 'decaissements.') => 'Décaissements',
        str_starts_with($route, 'gestions_depenses') => 'Dépenses',
        str_starts_with($route, 'entetes') || $route === 'parametres' => 'Infos société',
        str_starts_with($route, 'gestions_clients') || str_starts_with($route, 'clients.') => 'Clients',
        str_starts_with($route, 'gestions_fournisseurs') || str_starts_with($route, 'fournisseurs.') => 'Fournisseurs',
        $route === 'articles.article_code_barre' || $route === 'articles.print_code_barre' => 'Codes-barres',
        str_starts_with($route, 'gestions_articles') => 'Articles',
        str_starts_with($route, 'gestions_depots') || str_starts_with($route, 'depots.') => 'Magazins',
        str_starts_with($route, 'gestions_banques') || str_starts_with($route, 'banques.') => 'Banques',
        str_starts_with($route, 'gestions_caisses') || str_starts_with($route, 'caisses.') => 'Caisse',
        str_starts_with($route, 'gestions_utilisateurs') || str_starts_with($route, 'utilisateurs.') => 'Utilisateurs',
        $route === 'help' => 'Aide',
        default => 'Market Place',
    };
@endphp

<header class="app-topbar">
    <div class="topbar-start">
        <button type="button" class="icon-btn" id="sidebarToggle" aria-label="Ouvrir ou réduire le menu">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-heading">
            <p class="topbar-kicker">Market Place</p>
            <h1>{{ $pageTitle }}</h1>
        </div>
    </div>

    <div class="topbar-search">
        <i class="bi bi-search"></i>
        <input id="shellSearch" type="search" placeholder="Rechercher un menu…" autocomplete="off" aria-label="Rechercher un menu">
        <kbd class="d-none d-md-inline">/</kbd>
        <div id="shellSearchResults" class="search-results" hidden></div>
    </div>

    <div class="topbar-end">
        @if ($depotActif)
            @if (\App\Support\Acces::estMagazin())
                <span class="depot-chip" title="Magazin affecté">
                    <i class="bi bi-building"></i> {{ $depotActif->designation }}
                </span>
            @else
                <a href="{{ route('depots.quitter') }}" class="depot-chip" title="Changer de magazin">
                    <i class="bi bi-building"></i> {{ $depotActif->designation }}
                </a>
            @endif
        @endif
        <time class="topbar-date d-none d-xl-inline" datetime="{{ now()->toDateString() }}">
            {{ \Illuminate\Support\Carbon::now()->locale('fr')->translatedFormat('l j F') }}
        </time>

        <div class="dropdown">
            <button class="icon-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notifications">
                <i class="bi bi-bell"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end shell-panel notif-panel">
                <div class="shell-panel-head">
                    <strong>Notifications</strong>
                </div>
                <div class="notif-empty">
                    <i class="bi bi-bell-slash"></i>
                    <p>Aucune notification pour le moment.</p>
                </div>
            </div>
        </div>

        <div class="dropdown">
            <button class="user-chip" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <span class="avatar">{{ $initial }}</span>
                <span class="user-meta d-none d-lg-flex">
                    <strong>{{ $user->prenom ?? 'Utilisateur' }}</strong>
                    <small>{{ $role }}</small>
                </span>
                <i class="bi bi-chevron-down d-none d-lg-inline"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end shell-panel user-menu">
                <div class="user-menu-head">
                    <span class="avatar avatar-lg">{{ $initial }}</span>
                    <div>
                        <strong>{{ $fullName }}</strong>
                        <small>{{ $role }}</small>
                    </div>
                </div>
                <a class="user-menu-item" href="{{ route('utilisateurs.profil') }}">
                    <i class="bi bi-person"></i> Mon profil
                </a>
                @if (\App\Support\Acces::estAdmin())
                <a class="user-menu-item" href="{{ route('parametres') }}">
                    <i class="bi bi-gear"></i> Paramètres
                </a>
                @endif
                <a class="user-menu-item" href="{{ route('help') }}">
                    <i class="bi bi-question-circle"></i> Aide
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="user-menu-item is-danger">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
