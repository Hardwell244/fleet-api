<?php

namespace App\Http\Controllers\Api;

use App\DTOs\DeliveryEventDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryEventRequest;
use App\Services\DeliveryEventService;
use App\Services\DeliveryService;
use Illuminate\Http\JsonResponse;

class DeliveryEventController extends Controller
{
    public function __construct(
        private DeliveryEventService $service,
        private DeliveryService $deliveryService
    ) {}

    /**
     * Criar novo evento de entrega
     */
    public function store(StoreDeliveryEventRequest $request, string $deliveryId): JsonResponse
    {
        // Verificar se a entrega pertence à empresa do usuário
        $delivery = $this->deliveryService->findByIdWithoutScope((int) $deliveryId);

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega não encontrada.'
            ], 404);
        }

        if ($delivery->company_id !== auth()->user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para acessar esta entrega.'
            ], 403);
        }

        $dto = DeliveryEventDTO::fromRequest(
            $request->validated(),
            (int) $deliveryId
        );

        $event = $this->service->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'Evento registrado com sucesso!',
            'data' => $event
        ], 201);
    }

    /**
     * Listar eventos de uma entrega
     */
    public function index(string $deliveryId): JsonResponse
    {
        $events = $this->service->getByDelivery((int) $deliveryId);

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Listar eventos recentes
     */
    public function recent(): JsonResponse
    {
        $events = $this->service->getRecent(24);

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }
}
