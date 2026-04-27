<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Données de démo réalistes pour le contexte ivoirien.
     */
    public function definition(): array
    {
        // Prénoms féminins ivoiriens (les vendeuses ont souvent des clientes femmes)
        $prenoms = [
            'Fatou', 'Aminata', 'Mariam', 'Akissi', 'Aïcha', 'Kadidja',
            'Awa', 'Salimata', 'Hawa', 'Nafissatou', 'Adjoua', 'Affoué',
            'Brigitte', 'Christelle', 'Diane', 'Emma', 'Grâce', 'Joëlle',
        ];

        // Noms de famille
        $noms = [
            'Koné', 'Traoré', 'Bamba', 'Diallo', 'Touré', 'Camara',
            'Ouattara', 'Coulibaly', 'Diakité', 'Kouassi', 'Yao', 'Kouadio',
            'Konan', 'N\'Guessan', 'Bakayoko', 'Soro', 'Cissé', 'Doumbia',
        ];

        // Produits typiques d'une vendeuse de mode/cosmétiques
        $produits = [
            'Robe wax rouge taille M',
            'Robe wax verte taille L',
            'Ensemble pagne moderne',
            'Sac à main cuir noir',
            'Sac bandoulière imprimé',
            'Foulard wax assorti',
            'Boucles d\'oreilles dorées',
            'Bracelet fantaisie argenté',
            'Rouge à lèvres mat',
            'Crème éclaircissante',
            'Huile de karité 250ml',
            'Parfum femme 50ml',
            'Tissu wax 6 yards',
            'Chaussures plates noires',
            'Sandales tropicales',
            'Top crop blanc',
            'Jupe longue bohème',
            'Caftan brodé',
            'Pagne complet',
            'Bijoux ensemble (collier + bracelet)',
        ];

        // Préfixes téléphone CI : 01, 05, 07
        $prefixes = ['01', '05', '07'];
        $prefix = $prefixes[array_rand($prefixes)];

        return [
            // user_id sera défini par le Seeder via relation
            'client_name' => $prenoms[array_rand($prenoms)] . ' ' . $noms[array_rand($noms)],
            'client_phone' => '+225 ' . $prefix . ' ' . $this->generatePhoneSegments(),
            'product_description' => $produits[array_rand($produits)],
            'amount' => $this->faker->numberBetween(5, 100) * 500, // de 2500 à 50000 FCFA
            'status' => $this->faker->randomElement(OrderStatus::values()),
            'notes' => $this->faker->boolean(30)
                ? $this->faker->randomElement([
                    'Livraison Cocody samedi',
                    'Paiement par Wave',
                    'Client fidèle',
                    'Première commande',
                    'À livrer après 18h',
                    'Récupération en boutique',
                ])
                : null,
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Génère 4 segments de 2 chiffres pour le téléphone : XX XX XX XX
     */
    protected function generatePhoneSegments(): string
    {
        return collect(range(1, 4))
            ->map(fn () => str_pad((string) random_int(0, 99), 2, '0', STR_PAD_LEFT))
            ->implode(' ');
    }

    // ───────────────────────────────────────────
    // ÉTATS (states) — pour personnaliser facilement
    // ───────────────────────────────────────────

    /**
     * Force le statut "nouveau".
     * Usage : Order::factory()->nouveau()->create()
     */
    public function nouveau(): static
    {
        return $this->state(fn () => ['status' => OrderStatus::Nouveau]);
    }

    /**
     * Force le statut "livré".
     */
    public function livre(): static
    {
        return $this->state(fn () => ['status' => OrderStatus::Livre]);
    }

    /**
     * Force pour un user spécifique.
     * Usage : Order::factory()->forUser($awa)->create()
     */
    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }
}
