<?php

namespace App\Support;

use App\Models\User;

class Acces
{
    public const CAISSE = 'caisse';

    public const MAGAZIN = 'magazin';

    public const ADMIN = 'administrateur';

    public const ROLES = [
        self::CAISSE => 'Caisse',
        self::MAGAZIN => 'Magazin',
        self::ADMIN => 'Administrateur',
    ];

    public const DOCUMENTS_LECTURE_CAISSE = [
        'bon',
        'facture',
        'entree',
        'sortie',
        'inventaire',
        'depense',
    ];

    public static function user(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    public static function libelle(?string $role): string
    {
        return self::ROLES[$role] ?? 'Utilisateur';
    }

    public static function estAdmin(): bool
    {
        return self::user()?->role === self::ADMIN;
    }

    public static function estMagazin(): bool
    {
        return self::user()?->role === self::MAGAZIN;
    }

    public static function estCaisse(): bool
    {
        return self::user()?->role === self::CAISSE;
    }

    public static function peutCreer(): bool
    {
        return self::estAdmin() || self::estCaisse();
    }

    public static function peutModifier(string $module): bool
    {
        if (self::estAdmin()) {
            return true;
        }

        if (self::estCaisse()) {
            return ! in_array($module, self::DOCUMENTS_LECTURE_CAISSE, true);
        }

        return false;
    }
}
