<?php

namespace App\Enums;

/**
 * Représente le statut d'une commande dans son cycle de vie.
 *
 * Flux normal : Nouveau → Confirme → Paye → Livre
 * Flux alternatif : n'importe quel statut → Annule
 */
enum OrderStatus: string
{
    case Nouveau = 'nouveau';
    case Confirme = 'confirme';
    case Paye = 'paye';
    case Livre = 'livre';
    case Annule = 'annule';

    /**
     * Libellé français lisible par Awa.
     */
    public function label(): string// libelle a fficher dans l'UI en farncais
    {
        return match ($this) {
            self::Nouveau  => 'Nouveau',
            self::Confirme => 'Confirmé',
            self::Paye     => 'Payé',
            self::Livre    => 'Livré',
            self::Annule   => 'Annulé',
        };
    }

    /**
     * Emoji associé pour l'UI.
     */
    public function emoji(): string// emoji a afficher dans l'UI pour identifier rapidement le statut
    {
        return match ($this) {
            self::Nouveau  => '🆕',
            self::Confirme => '✅',
            self::Paye     => '💰',
            self::Livre    => '📦',
            self::Annule   => '❌',
        };
    }

    /**
     * Permet d'avoir une UI cohérente et facilement identifiable selon le statut.
     */
    public function color(): string// nom de couleur tailwind
    {
        return match ($this) {
            self::Nouveau  => 'amber',
            self::Confirme => 'blue',
            self::Paye     => 'emerald',
            self::Livre    => 'gray',
            self::Annule   => 'red',
        };
    }

    /**
     * Statut suivant dans le flux normal.
     * Retourne null si la commande est dans un état final.
     */
    public function nextStatus(): ?self// retourne le statut suivant dans le flux normal, ou null si on est dans un état final
    {
        return match ($this) {
            self::Nouveau  => self::Confirme,
            self::Confirme => self::Paye,
            self::Paye     => self::Livre,
            self::Livre    => null,// commande deja livree, pas de statut suivant
            self::Annule   => null,// commande annulee, pas de statut siovant
        };
    }

    /**
     * Libellé de l'action à afficher sur le bouton primaire.
     */
    public function nextActionLabel(): ?string// libellé de l'action à afficher sur le bouton primaire pour faire avancer la commande, ou null si pas d'action possible
    {
        return match ($this) {
            self::Nouveau  => 'Confirmer',
            self::Confirme => 'Marquer payé',
            self::Paye     => 'Marquer livré',
            self::Livre    => null,
            self::Annule   => null,
        };
    }

    /**
     * La commande est-elle dans un état final (non-modifiable) ?
     */
    public function isFinal(): bool// retourne true si la commande est dans un etat final (livree ou annulee), false sinon
    {
        return in_array($this, [self::Livre, self::Annule], true);
    }

    /**
     * Peut-on annuler une commande dans ce statut ?
     */
    public function canBeCancelled(): bool// retoure true si la commande peut etre annulee depuis ce statut, false sinon (une commande livree ou annulee ne peut pas etre annulee)
    {
        return ! in_array($this, [self::Livre, self::Annule], true);
    }

    /**
     * Retourne tous les statuts sous forme d'array (pour validation).
     */
    public static function values(): array // retourne tous les stauts sous forme d'array de string (pour validation dans les FormRequest)
    {
        return array_column(self::cases(), 'value');// ['nouveau', 'confirme', 'paye', 'livre', 'annule']
    }
}
