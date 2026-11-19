@php
    $navActive = fn (...$patterns) => request()->routeIs(...$patterns);

    $groups = [
        [
            'label' => 'Commercial',
            'icon' => 'bi-graph-up-arrow',
            'open' => $navActive('gestions_ventes.*', 'gestions_bons_commandes.*', 'bons_commandes.*', 'gestions_achats.*', 'ventes.print', 'ventes.solder', 'ventes.printPaiement', 'ventes.getStock', 'achats.print', 'achats.solder', 'achats.printPaiement'),
            'items' => [
                ['route' => 'gestions_ventes.index', 'icon' => 'bi-receipt', 'label' => 'Journal des ventes', 'match' => ['gestions_ventes.*', 'ventes.print', 'ventes.solder', 'ventes.printPaiement', 'ventes.getStock']],
                ['route' => 'gestions_bons_commandes.index', 'icon' => 'bi-file-earmark-text', 'label' => 'Bons de commande', 'match' => ['gestions_bons_commandes.*', 'bons_commandes.*']],
                ['route' => 'gestions_achats.index', 'icon' => 'bi-bag-check', 'label' => 'Journal des achats', 'match' => ['gestions_achats.*', 'achats.print', 'achats.solder', 'achats.printPaiement']],
            ],
        ],
        [
            'label' => 'Stock',
            'icon' => 'bi-box-seam',
            'open' => $navActive('stocks.articles', 'stocks.articles.print', 'gestions_entrees.*', 'gestions_sorties.*', 'gestions_transferts.*', 'gestions_inventaires.*', 'entrees.*', 'sorties.*', 'transferts.*'),
            'items' => [
                ['route' => 'stocks.articles', 'icon' => 'bi-bag', 'label' => 'Articles', 'match' => ['stocks.articles', 'stocks.articles.print']],
                ['route' => 'gestions_entrees.index', 'icon' => 'bi-box-arrow-in-down', 'label' => 'Entrées', 'match' => ['gestions_entrees.*', 'entrees.*']],
                ['route' => 'gestions_sorties.index', 'icon' => 'bi-box-arrow-up', 'label' => 'Sorties', 'match' => ['gestions_sorties.*', 'sorties.*']],
                ['route' => 'gestions_transferts.index', 'icon' => 'bi-arrow-left-right', 'label' => 'Transferts', 'match' => ['gestions_transferts.*', 'transferts.*']],
                ['route' => 'gestions_inventaires.index', 'icon' => 'bi-clipboard2-check', 'label' => 'Inventaire', 'match' => ['gestions_inventaires.*']],
            ],
        ],
        [
            'label' => 'Trésorerie',
            'icon' => 'bi-cash-coin',
            'open' => $navActive('ventes.non-soldees', 'achats.non-soldes', 'gestions_depenses.*', 'gestions_encaissements.*', 'gestions_decaissements.*', 'encaissements.*', 'decaissements.*'),
            'items' => [
                ['route' => 'ventes.non-soldees', 'icon' => 'bi-cart-x', 'label' => 'Ventes non soldées', 'match' => ['ventes.non-soldees']],
                ['route' => 'achats.non-soldes', 'icon' => 'bi-bag-x', 'label' => 'Achats non soldés', 'match' => ['achats.non-soldes']],
                ['route' => 'gestions_encaissements.index', 'icon' => 'bi-arrow-down-circle', 'label' => 'Encaissements', 'match' => ['gestions_encaissements.*', 'encaissements.*']],
                ['route' => 'gestions_decaissements.index', 'icon' => 'bi-arrow-up-circle', 'label' => 'Décaissements', 'match' => ['gestions_decaissements.*', 'decaissements.*']],
                ['route' => 'gestions_depenses.index', 'icon' => 'bi-wallet2', 'label' => 'Dépenses', 'match' => ['gestions_depenses.*']],
            ],
        ],
        [
            'label' => 'Référentiel',
            'icon' => 'bi-sliders',
            'open' => $navActive('gestions_clients.*', 'gestions_fournisseurs.*', 'gestions_articles.*', 'gestions_depots.*'),
            'items' => [
                ['route' => 'gestions_clients.index', 'icon' => 'bi-people', 'label' => 'Clients', 'match' => ['gestions_clients.*', 'clients.*']],
                ['route' => 'gestions_fournisseurs.index', 'icon' => 'bi-truck', 'label' => 'Fournisseurs', 'match' => ['gestions_fournisseurs.*', 'fournisseurs.*']],
                ['route' => 'gestions_articles.index', 'icon' => 'bi-bag', 'label' => 'Articles', 'match' => ['gestions_articles.*']],
                ['route' => 'gestions_depots.index', 'icon' => 'bi-diagram-3', 'label' => 'Magazins', 'match' => ['gestions_depots.*', 'depots.*']],
            ],
        ],
        [
            'label' => 'Paramètres',
            'icon' => 'bi-gear',
            'open' => $navActive('entetes.*', 'parametres', 'parametres.update', 'gestions_utilisateurs.*', 'utilisateurs.*'),
            'items' => [
                ['route' => 'entetes.index', 'icon' => 'bi-building', 'label' => 'Infos société', 'match' => ['entetes.*', 'parametres', 'parametres.update']],
                ['route' => 'gestions_utilisateurs.index', 'icon' => 'bi-person-circle', 'label' => 'Utilisateurs', 'match' => ['gestions_utilisateurs.*', 'utilisateurs.*']],
            ],
        ],
    ];

    if (! \App\Support\Acces::estAdmin()) {
        $groups = array_values(array_filter($groups, fn ($group) => $group['label'] !== 'Paramètres'));
    }
@endphp

<aside id="appSidebar" class="app-sidebar" aria-label="Navigation principale">
    <a href="{{ $depotActif ? route('dashboard') : route('depots.accueil') }}" class="sidebar-brand">
        <span class="brand-mark">
            <img src="{{ asset('images/logo.png') }}" alt="EKDF eau Bisimilah">
        </span>
        <span class="brand-text">
            <strong>Market Place</strong>
            <small>Stocks & trésorerie</small>
        </span>
    </a>

    <nav class="sidebar-scroll">
        <p class="nav-section">Pilotage</p>
        @unless (\App\Support\Acces::estMagazin())
        <a href="{{ route('depots.accueil') }}"
           class="nav-link {{ $navActive('depots.accueil') ? 'is-active' : '' }}"
           data-section="Pilotage"
           @if($navActive('depots.accueil')) aria-current="page" @endif>
            <i class="bi bi-building"></i>
            <span class="nav-label">Magazins</span>
        </a>
        @endunless
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ $navActive('dashboard') ? 'is-active' : '' }}"
           data-section="Pilotage"
           @if($navActive('dashboard')) aria-current="page" @endif>
            <i class="bi bi-grid-1x2"></i>
            <span class="nav-label">Tableau de bord</span>
        </a>

        @foreach ($groups as $group)
            <div class="nav-group {{ $group['open'] ? 'is-open' : '' }}">
                <button type="button" class="nav-group-btn" aria-expanded="{{ $group['open'] ? 'true' : 'false' }}">
                    <i class="bi {{ $group['icon'] }}"></i>
                    <span class="nav-label">{{ $group['label'] }}</span>
                    <i class="bi bi-chevron-down nav-chevron"></i>
                </button>
                <ul class="nav-sub">
                    <li class="nav-flyout-title">{{ $group['label'] }}</li>
                    @foreach ($group['items'] as $item)
                        @php $active = $navActive(...$item['match']); @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="nav-link {{ $active ? 'is-active' : '' }}"
                               data-section="{{ $group['label'] }}"
                               @if($active) aria-current="page" @endif>
                                <i class="bi {{ $item['icon'] }}"></i>
                                <span class="nav-label">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        @if (\App\Support\Acces::estAdmin())
        <a href="{{ route('logs.index') }}"
           class="nav-link {{ $navActive('logs.index') ? 'is-active' : '' }}"
           data-section="Logs"
           @if($navActive('logs.index')) aria-current="page" @endif>
            <i class="bi bi-journal-text"></i>
            <span class="nav-label">Logs</span>
        </a>
        @endif
    </nav>

    <div class="sidebar-foot">
        <a href="{{ route('help') }}" class="nav-link {{ $navActive('help') ? 'is-active' : '' }}" data-section="Aide">
            <i class="bi bi-question-circle"></i>
            <span class="nav-label">Aide</span>
        </a>
        <button type="button" class="sidebar-collapse" id="sidebarCollapse" title="Réduire le menu">
            <i class="bi bi-layout-sidebar-inset"></i>
            <span class="sidebar-foot-label">Réduire</span>
        </button>
    </div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop" hidden></div>
