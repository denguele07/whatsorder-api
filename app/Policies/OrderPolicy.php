<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Peut lister SES commandes.
     * Appelée sur GET /api/orders
     */
    public function viewAny(User $user): bool
    {
        // Tout user authentifié peut lister SES commandes (filtrage fait en contrôleur)
        return true;
    }

    /**
     * Peut voir CETTE commande.
     * Appelée sur GET /api/orders/{order}
     */
    public function view(User $user, Order $order): bool
    {
        // Un user peut voir une commande s'il en est le proprietaire (user_id = id du user connecter)
        return $user->id === $order->user_id;
    }

    /**
     * Peut créer une nouvelle commande.
     * Appelée sur POST /api/orders
     */
    public function create(User $user): bool
    {
        // Tout user authentifié peut créer des commandes
        return true;
    }

    /**
     * Peut modifier CETTE commande.
     * Appelée sur PATCH /api/orders/{order}
     */
    public function update(User $user, Order $order): bool
    {
        // Un user peut modifier une commande s'il en est le proprietaire (user_id = id du user connecter)
        return $user->id === $order->user_id;
    }

    /**
     * Peut supprimer CETTE commande.
     * Appelée sur DELETE /api/orders/{order}
     */
    public function delete(User $user, Order $order): bool
    {
        // Un user peut supprimer une commande s'il en est le proprietaire (user_id = id du user connecter)
        return $user->id === $order->user_id;
    }

    /**
     * Peut restaurer une commande soft-deleted.
     */
    public function restore(User $user, Order $order): bool
    {
        // Un user peut restaurer une commande s'il en est le proprietaire (user_id = id du user connecter)
        return $user->id === $order->user_id;
    }

    /**
     * Peut supprimer DÉFINITIVEMENT (force delete).
     * Par sécurité : on n'autorise PERSONNE pour l'instant.
     * En prod, ce serait réservé à un admin.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        // par secuter on n'autorise personne a supprimer definitivement une commande (meme le proprietaire), cette action devrait etre reserver a un admin dans une future version
        return false;
    }
}
