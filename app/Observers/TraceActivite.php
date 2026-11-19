<?php

namespace App\Observers;

use App\Support\Journal;
use Illuminate\Database\Eloquent\Model;

class TraceActivite
{
    public function created(Model $model): void
    {
        $this->ecrire('Création', $model, null);
    }

    public function updated(Model $model): void
    {
        if ($model->wasRecentlyCreated) {
            return;
        }

        $changes = collect($model->getChanges())->except(['updated_at', 'created_at']);
        if ($changes->isEmpty()) {
            return;
        }

        $detail = 'Champs : ' . $changes->keys()->take(6)->implode(', ');
        $this->ecrire('Modification', $model, $detail);
    }

    public function deleted(Model $model): void
    {
        $this->ecrire('Suppression', $model, null);
    }

    private function ecrire(string $action, Model $model, ?string $detail): void
    {
        Journal::noter($action, $this->module($model), $this->reference($model), $detail);
    }

    private function module(Model $model): string
    {
        return match (class_basename($model)) {
            'Vente' => 'Facture',
            'BonCommande' => 'Bon de commande',
            'Entree' => 'Entrée',
            'Sortie' => 'Sortie',
            'Inventaire' => 'Inventaire',
            'Achat' => 'Achat',
            'Depense' => 'Dépense',
            'Encaissement' => 'Encaissement',
            'Decaissement' => 'Décaissement',
            'Transfert' => 'Transfert',
            'Article' => 'Article',
            'Client' => 'Client',
            'Fournisseur' => 'Fournisseur',
            'User' => 'Utilisateur',
            'Depot' => 'Magazin',
            'Banque' => 'Banque',
            'Caisse' => 'Caisse',
            default => class_basename($model),
        };
    }

    private function reference(Model $model): string
    {
        if ($model instanceof \App\Models\User) {
            return $model->nomComplet();
        }

        foreach ([
            'numero_vente',
            'numero_bon',
            'numero_entree',
            'numero_sortie',
            'numero_inventaire',
            'numero_achat',
            'code',
            'libelle',
            'designation',
            'raison_sociale',
            'nom',
        ] as $champ) {
            $valeur = $model->getAttribute($champ);
            if ($valeur) {
                return (string) $valeur;
            }
        }

        return '#' . $model->getKey();
    }
}
