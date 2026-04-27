<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforme un Model Order en représentation JSON.
 *
 * Ce fichier est le CONTRAT public de l'API pour les commandes.
 * Toute modification ici impacte le front.
 */
class OrderResource extends JsonResource
{
    /**
     * Structure JSON de sortie.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Identifiant public (pas l'id interne)
            'reference' => $this->reference,

            // Informations client
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,

            // Détails commande
            'product_description' => $this->product_description,
            'amount' => $this->amount,
            'amount_formatted' => number_format($this->amount, 0, ',', ' ') . ' FCFA',
            'notes' => $this->notes,

            // Statut enrichi (tout ce dont le front a besoin)
            'status' => [
                'value' => $this->status->value,// valeur brute du staut (ex : "en_cours")
                'label' => $this->status->label(),// label lisible du statut (ex : "En cours")
                'emoji' => $this->status->emoji(),// emoji representant le statut (ex : "⏳")
                'color' => $this->status->color(),//
                'next_action_label' => $this->status->nextActionLabel(),// label de l'action suivante possible (ex : "Marquer comme livré")
                'is_final' => $this->status->isFinal(),// indique si le statut est un état final (ex : "livré" ou "annulé"), utile pour désactiver les actions dans le front
                'can_be_cancelled' => $this->status->canBeCancelled(),// indique si la commande peut être annulée depuis son statut actuel, utile pour afficher ou non le bouton d'annulation dans le front
            ],

            // Action WhatsApp prête à l'emploi
            'whatsapp_url' => $this->whatsappUrl(),

            // Timestamps au format ISO 8601
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
