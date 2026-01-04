<?php

namespace App\Http\Controllers\Api;

use App\DTOs\DeliveryEventDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryEventRequest;
use App\Services\DeliveryEventService;
use Illuminate\Http\JsonResponse;

class DeliveryEventController extends Controller
{
    public function __construct(
        private DeliveryEventService $service
    ) {}

    /**
     * Criar novo evento de entrega
     */
    public function store(StoreDeliveryEventRequest $request, string $deliveryId): JsonResponse
    {
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
