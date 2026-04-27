<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Attributs modifiables en mass assignment.
     * user_id est volontairement EXCLU pour sécurité (défini par le controller).
     * reference est EXCLU car généré automatiquement par l'Observer.
     */
    protected $fillable = [
        'client_name',
        'client_phone',
        'product_description',
        'amount',
        'status',
        'notes',
    ];

    /**
     * Valeurs par défaut des attributs lors de la création en mémoire.
     * Synchronisées avec les DEFAULT de la migration.
     */
    protected $attributes = [
        'status' => 'nouveau',
    ];

    /**
     * Conversion automatique des types en lecture/écriture.
     */
    protected function casts(): array
    {
        return [
            'amount'  => 'integer',
            'status'  => OrderStatus::class,
        ];
    }

    /**
     * La clé utilisée pour le Route Model Binding.
     * Permet d'utiliser /orders/{reference} au lieu de /orders/{id}.
     */
    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    // ─────────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────────

    /**
     * Le vendeur propriétaire de la commande.
     * une commande appartient à un seul utilisateur (vendeur), mais un utilisateur peut avoir plusieurs commandes.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─────────────────────────────────────────────
    // HELPERS MÉTIER
    // ─────────────────────────────────────────────

    /**
     * Génère le lien WhatsApp pré-rempli vers le client.
     */
    public function whatsappUrl(): string
    {
        $phone = preg_replace('/\D/', '', $this->client_phone);

        $message = sprintf(
            "Bonjour %s 👋\n\nVotre commande *%s* :\n%s\n\nMontant : %s FCFA\n\nMerci 🙏",
            $this->client_name,
            $this->reference,
            $this->product_description,
            number_format($this->amount, 0, ',', ' ')
        );

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    }

    /**
     * Fait passer la commande au statut suivant (si possible).
     * Retourne true si avancement réussi, false si déjà en état final.
     */
    public function advanceStatus(): bool
    {
        $next = $this->status->nextStatus();

        if ($next === null) {
            return false;
        }

        $this->update(['status' => $next]);

        return true;
    }
}
