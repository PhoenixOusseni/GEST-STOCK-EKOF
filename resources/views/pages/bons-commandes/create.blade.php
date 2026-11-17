@extends('layouts.master')

@section('content')
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Bon de commande</h3>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gestions_bons_commandes.index') }}">Bons de commande</a></li>
                        <li class="breadcrumb-item active">Nouveau</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('gestions_bons_commandes.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Nouveau bon de commande</h5>
                        <hr>
                        @include('pages.bons-commandes._alerts')
                        @include('pages.bons-commandes._form', [
                            'formAction' => route('gestions_bons_commandes.store'),
                            'formMethod' => 'POST',
                        ])
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                @include('pages.bons-commandes._side')
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('pages.bons-commandes._scripts')
@endsection
