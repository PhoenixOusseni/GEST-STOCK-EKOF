@extends('layouts.master')
@section('content')
    <div class="pagetitle">
        <div>
            <h3>Logs</h3>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Logs</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('logs.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Utilisateur</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>
                                    {{ $user->nomComplet() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Action</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">Toutes</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Module</label>
                        <select name="module" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Référence</label>
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="N° de document">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-funnel"></i> Filtrer</button>
                        <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm">Effacer</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Traitements du système</h5>
                @if ($activites->isEmpty())
                    <div class="alert alert-info mb-0">Aucun traitement enregistré pour le moment. Les prochaines créations, modifications et suppressions apparaîtront ici.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Utilisateur</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Référence</th>
                                    <th>Détail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activites as $activite)
                                    <tr>
                                        <td>{{ $activite->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $activite->user?->nomComplet() ?? 'Non renseigné' }}</td>
                                        <td>{{ $activite->action }}</td>
                                        <td>{{ $activite->module }}</td>
                                        <td>{{ $activite->reference }}</td>
                                        <td>{{ $activite->detail ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $activites->links() }}
                @endif
            </div>
        </div>
    </section>
@endsection
