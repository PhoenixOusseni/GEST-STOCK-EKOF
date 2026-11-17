# Composants Réutilisables SAFRECO GSM

Ce document décrit tous les composants Blade réutilisables disponibles dans l'application.

## 1. Card Component
Affiche une carte avec titre, contenu et footer optionnels.

```blade
<x-card title="Titre de la carte" class="mb-4">
    Contenu de la carte
    @slot('footer')
        Contenu du footer
    @endslot
</x-card>
```

### Props:
- `title`: Titre de la carte (optionnel)
- `class`: Classes CSS personnalisées
- `footer`: Contenu du footer (slot optionnel)

---

## 2. Button Component
Rendu un bouton avec styles modernes.

```blade
<x-button type="submit" variant="primary" size="lg" icon="save">
    Enregistrer
</x-button>
```

### Props:
- `type`: Type du bouton (button, submit, reset) - défaut: button
- `variant`: Couleur (primary, secondary, success, warning, danger, light)
- `size`: Taille (sm, lg)
- `icon`: Icône Bootstrap Icons (sans préfixe "bi-")
- `disabled`: Désactiver le bouton
- `dataLoading`: Texte à afficher pendant le chargement
- `class`: Classes CSS personnalisées

---

## 3. Form Group Component
Composant de groupe de formulaire réutilisable avec validation.

```blade
<x-form-group 
    type="text" 
    name="email" 
    label="Adresse E-mail"
    placeholder="exemple@mail.com"
    required
    help="Utilisez votre adresse email professionnelle"
/>
```

### Props:
- `type`: Type d'input (text, email, password, textarea, select)
- `name`: Nom de l'input
- `label`: Étiquette du champ
- `placeholder`: Texte d'aide
- `value`: Valeur initiale
- `required`: Champ obligatoire
- `help`: Texte d'aide sous le champ
- `rows`: Nombre de lignes pour textarea
- `options`: Array pour select
- `disabled`: Désactiver le champ
- `class`: Classes CSS personnalisées

---

## 4. Alert Component
Affiche une alerte avec icône et titre optionnel.

```blade
<x-alert type="success" title="Succès !" icon="check-circle-fill">
    L'opération a été complétée avec succès.
</x-alert>

<x-alert type="danger" title="Erreur !" icon="exclamation-triangle-fill">
    Une erreur est survenue. Veuillez réessayer.
</x-alert>
```

### Props:
- `type`: Type (success, danger, warning, info)
- `title`: Titre optionnel
- `icon`: Icône Bootstrap Icons
- `class`: Classes CSS personnalisées

---

## 5. Badge Component
Affiche un badge de statut avec icône optionnelle.

```blade
<x-badge type="success" icon="check-circle">
    Actif
</x-badge>

<x-badge type="warning" icon="exclamation-circle">
    En attente
</x-badge>
```

### Props:
- `type`: Type (primary, success, warning, danger, light)
- `icon`: Icône Bootstrap Icons (optionnel)
- `class`: Classes CSS personnalisées

---

## 6. Page Header Component
En-tête de page avec titre, sous-titre et boutons d'action.

```blade
<x-page-header 
    title="Gestion des Articles"
    subtitle="Gérez votre catalogue de produits"
    :breadcrumbs="[
        'Accueil' => route('dashboard'),
        'Configuration' => '#',
        'Articles' => '#'
    ]"
>
    @slot('action')
        <x-button variant="primary" icon="plus">
            Créer un article
        </x-button>
    @endslot
</x-page-header>
```

### Props:
- `title`: Titre de la page
- `subtitle`: Sous-titre optionnel
- `breadcrumbs`: Array de breadcrumbs [label => url]
- `action`: Slot pour les boutons d'action

---

## 7. Table Component
Composant pour afficher des tableaux avec DataTables.

```blade
<x-table 
    :columns="['ID', 'Nom', 'Email', 'Date']"
    id="usersTable"
    :actions="true"
>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at->format('d/m/Y') }}</td>
            <td>
                <x-action-buttons 
                    :editUrl="route('users.edit', $user)"
                    :deleteUrl="route('users.delete', $user)"
                />
            </td>
        </tr>
    @endforeach
</x-table>
```

### Props:
- `columns`: Array des colonnes
- `id`: ID unique du tableau (optionnel)
- `actions`: Afficher colonne actions (optionnel)
- `emptyMessage`: Message quand vide
- `class`: Classes CSS personnalisées

---

## 8. Modal Component
Composant pour afficher une fenêtre modale.

```blade
<x-modal 
    id="exampleModal"
    title="Créer un nouvel utilisateur"
    size="lg"
>
    <form>
        <x-form-group type="text" name="name" label="Nom" required />
        <x-form-group type="email" name="email" label="Email" required />
    </form>
    
    @slot('footer')
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Annuler
        </button>
        <x-button type="submit" variant="primary">
            Créer
        </x-button>
    @endslot
</x-modal>
```

### Props:
- `id`: ID unique de la modale
- `title`: Titre de la modale
- `size`: Taille (sm, lg, xl)
- `footer`: Slot pour les boutons d'action

---

## 9. Stats Card Component
Affiche une carte de statistiques avec valeur et icône.

```blade
<x-stats-card 
    label="Total Ventes"
    value="2,450"
    color="#10b981"
    colorRgb="16, 185, 129"
    icon="graph-up"
    subtitle="↑ 12% depuis le mois dernier"
/>
```

### Props:
- `label`: Libellé du statut
- `value`: Valeur à afficher
- `color`: Couleur hexadécimale
- `colorRgb`: Couleur RGB pour l'arrière-plan
- `icon`: Icône Bootstrap Icons
- `subtitle`: Texte descriptif optionnel
- `class`: Classes CSS personnalisées

---

## 10. Action Buttons Component
Groupe de boutons d'action pour les lignes de tableau.

```blade
<x-action-buttons 
    :editUrl="route('users.edit', $user->id)"
    :viewUrl="route('users.show', $user->id)"
    :deleteUrl="route('users.destroy', $user->id)"
/>
```

### Props:
- `editUrl`: URL pour éditer
- `viewUrl`: URL pour voir
- `deleteUrl`: URL pour supprimer

---

## 11. Breadcrumb Component
Composant pour afficher le chemin de navigation.

```blade
<x-breadcrumb 
    :items="[
        'Accueil' => route('dashboard'),
        'Articles' => route('articles.index'),
        'Vue article' => '#'
    ]"
/>
```

### Props:
- `items`: Array de breadcrumbs [label => url]
- `class`: Classes CSS personnalisées

---

## Exemples d'utilisation

### Formulaire complet
```blade
<x-card title="Créer un utilisateur">
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        
        <x-form-group 
            type="text" 
            name="name" 
            label="Nom complet"
            placeholder="Jean Dupont"
            required
        />
        
        <x-form-group 
            type="email" 
            name="email" 
            label="Email"
            required
        />
        
        <x-form-group 
            type="select" 
            name="role" 
            label="Rôle"
            :options="['admin' => 'Administrateur', 'user' => 'Utilisateur']"
            required
        />
        
        <div class="d-flex gap-2">
            <x-button type="submit" variant="primary" icon="save">
                Créer
            </x-button>
            <x-button type="reset" variant="light">
                Réinitialiser
            </x-button>
        </div>
    </form>
</x-card>
```

### Page de listing
```blade
<x-page-header 
    title="Gestion des Utilisateurs"
    subtitle="Gérez les comptes utilisateurs"
    :breadcrumbs="[
        'Accueil' => route('dashboard'),
        'Utilisateurs' => '#'
    ]"
>
    @slot('action')
        <x-button variant="primary" icon="plus">
            Créer un utilisateur
        </x-button>
    @endslot
</x-page-header>

<x-table 
    :columns="['ID', 'Nom', 'Email', 'Rôle']"
    id="usersTable"
    :actions="true"
>
    @forelse ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <x-badge type="primary">{{ ucfirst($user->role) }}</x-badge>
            </td>
            <td>
                <x-action-buttons 
                    :editUrl="route('users.edit', $user)"
                    :deleteUrl="route('users.destroy', $user)"
                />
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center py-4">
                <p class="text-muted">Aucun utilisateur trouvé</p>
            </td>
        </tr>
    @endforelse
</x-table>
```

---

## Colours Disponibles

### Types de variantes:
- `primary` (Bleu) - #2563eb
- `secondary` (Gris) - #1f2937
- `success` (Vert) - #10b981
- `warning` (Orange) - #f59e0b
- `danger` (Rouge) - #ef4444
- `light` (Clair) - #f9fafb

---

## Icônes Bootstrap Icons

Consultez la documentation officielle: https://icons.getbootstrap.com/

Exemples courants:
- `save`, `edit`, `trash`, `plus`, `minus`
- `check-circle`, `exclamation-triangle`, `info-circle`
- `arrow-up`, `arrow-down`, `house`, `menu-button-wide`
- `bell`, `envelope`, `gear`, `lock`, `unlock`
