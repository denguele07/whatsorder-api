<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Liste les commandes du vendeur connecté.
     *
     * GET /api/v1/orders
     * Query params optionnels :
     *   ?status=nouveau        → filtrer par statut
     *   ?search=Fatou          → recherche dans nom/téléphone client
     */
    public function index(Request $request)
    {
        $query = $request->user()->orders();// Commence par les commandes du vendeur connecte

        // Filtre par statut (existant)
        if ($request->filled('status')) {
            $query->where('status', $request->status);// Filtre par statut si fourni
        }

        // Filtre par recherche (nouveau)
        if ($request->filled('search')) {// Si le paramètre de recherche est présent
            $search = $request->search;// Recherche dans plusieurs champs (nom, téléphone, description, référence)
            $query->where(function ($q) use ($search) {// Utilise une sous-requête pour regrouper les conditions de recherche
                $q->where('client_name', 'like', "%{$search}%")// Recherche dans le nom du client
                    ->orWhere('client_phone', 'like', "%{$search}%")
                    ->orWhere('product_description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return OrderResource::collection($orders);
    }

    /**
     * Crée une nouvelle commande.
     *
     * POST /api/v1/orders
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $order = $request->user()
            ->orders()
            ->create($request->validated());

        return OrderResource::make($order)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Affiche une commande.
     *
     * GET /api/v1/orders/{order}
     * {order} est résolu automatiquement par sa `reference`.
     */
    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return OrderResource::make($order);
    }

    /**
     * Met à jour une commande.
     *
     * PATCH /api/v1/orders/{order}
     */
    public function update(UpdateOrderRequest $request, Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $order->update($request->validated());

        return OrderResource::make($order->fresh());
    }

    /**
     * Supprime une commande (soft delete).
     *
     * DELETE /api/v1/orders/{order}
     */
    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->json(null, 204);
    }

    /**
     * Fait avancer la commande au statut suivant.
     *
     * POST /api/v1/orders/{order}/advance
     */
    public function advance(Order $order): JsonResponse|OrderResource
    {
        $this->authorize('update', $order);

        if (! $order->advanceStatus()) {
            return response()->json([
                'message' => 'Cette commande est déjà dans un état final.',
            ], 422);
        }

        return OrderResource::make($order->fresh());
    }
}
