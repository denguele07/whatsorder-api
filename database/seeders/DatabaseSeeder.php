<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Peuple la base avec des données de démo réalistes.
     */
    public function run(): void
    {

        // 1. AWA — vendeuse principale (compte démo)

        $awa = User::factory()->create([
            'name' => 'Awa Koffi',
            'email' => 'awa@whatsorder.ci',
            'password' => Hash::make('password'),
        ]);

        $this->seedOrdersForUser($awa, total: 25);


        // 2. MARIE — 2e vendeuse (test multi-tenant)

        $marie = User::factory()->create([
            'name' => 'Marie Ouattara',
            'email' => 'marie@whatsorder.ci',
            'password' => Hash::make('password'),
        ]);

        $this->seedOrdersForUser($marie, total: 10);

        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('✓ Seeding terminé !');
        $this->command->info('  → Awa : awa@whatsorder.ci / password');
        $this->command->info('  → Marie : marie@whatsorder.ci / password');
        $this->command->info('═══════════════════════════════════════════');
    }

    /**
     * Crée un set varié de commandes pour un user :
     * - distribution réaliste des statuts
     * - étalées sur 30 jours
     */
   protected function seedOrdersForUser(User $user, int $total): void
{
    // Distribution des statuts (en %) :
    // - 30% nouveau   (en attente de confirmation)
    // - 25% confirmé  (en attente de paiement)
    // - 20% payé      (en attente de livraison)
    // - 20% livré     (terminé)
    // -  5% annulé
    $distribution = [
        OrderStatus::Nouveau->value  => (int) round($total * 0.30),
        OrderStatus::Confirme->value => (int) round($total * 0.25),
        OrderStatus::Paye->value     => (int) round($total * 0.20),
        OrderStatus::Livre->value    => (int) round($total * 0.20),
        OrderStatus::Annule->value   => (int) round($total * 0.05),
    ];

    foreach ($distribution as $statusValue => $count) {
        if ($count > 0) {
            Order::factory()
                ->count($count)
                ->forUser($user)
                ->state(['status' => $statusValue])
                ->create();
        }
    }
}
}
