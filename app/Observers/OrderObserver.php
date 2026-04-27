<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Appelé AVANT l'insertion en DB.
     * On génère ici la reference publique unique.
     */
    public function creating(Order $order): void
    {
        // si la reference est deje definie (ex: lors d'une creation en memoire pour validation), on ne la regenere pas
        if (empty($order->reference)) {
            $order->reference = $this->generateReference();// génère une reference unique au format CMD-YYYY-XXXXXX
        }
    }

    /**
     * Génère une reference unique au format CMD-YYYY-XXXXXX.
     */
    protected function generateReference(): string
    {
        // Caractères ambigus exclus : 0/O, 1/I, L
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $random = '';// Génère une chaîne aléatoire de 6 caractères
            for ($i = 0; $i < 6; $i++) {
                $random .= $alphabet[random_int(0, strlen($alphabet) - 1)];// Utilise random_int pour une meilleure sécurité
            }

            $reference = 'CMD-' . date('Y') . '-' . $random;// Formate la reference

        } while (Order::where('reference', $reference)->exists());// Vérifie l'unicité de la reference en DB

        return $reference;// Retourne la reference générée
    }
}
