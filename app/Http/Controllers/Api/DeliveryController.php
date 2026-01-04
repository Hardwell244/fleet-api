<?php

namespace App\Http\Controllers\Api;

use App\DTOs\DeliveryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Services\DeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function __construct(
        private DeliveryService $service
    ) {}

    /**
     * Listar entregas
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

        $filters = [
            'status' => $request->input('status'),
            'vehicle_id' => $request->input('vehicle_id'),
            'driver_id' => $request->input('driver_id'),
            'search' => $request->input('search'),
        ];

        $deliveries = $this->service->list($perPage, $filters);

        return response()->json([
            'success' => true,
            'data' => $deliveries->items(),
            'meta' => [
                'current_page' => $deliveries->currentPage(),
                'per_page' => $deliveries->perPage(),
                'total' => $deliveries->total(),
                'last_page' => $deliveries->lastPage(),
            ]
        ]);
    }

    /**
     * Exibir entrega específica
     */
    public function show(string $id): JsonResponse
    {
        $delivery = $this->service->findById((int) $id);

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega não encontrada.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $delivery
        ]);
    }

    /**
     * Criar nova entrega
     */
    public function store(StoreDeliveryRequest $request): JsonResponse
    {
        $dto = DeliveryDTO::fromRequest(
            $request->validated(),
            auth()->user()->company_id
        );

        $delivery = $this->service->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'Entrega cadastrada com sucesso!',
            'data' => $delivery
        ], 201);
    }

    /**
     * Atualizar entrega
     */
    public function update(UpdateDeliveryRequest $request, string $id): JsonResponse
    {
        $dto = DeliveryDTO::fromRequest(
            $request->validated(),
            auth()->user()->company_id,
            (int) $id
        );

        $updated = $this->service->update((int) $id, $dto);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar entrega.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Entrega atualizada com sucesso!'
        ]);
    }

    /**
     * Deletar entrega
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->service->delete((int) $id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar entrega.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Entrega deletada com sucesso!'
        ]);
    }

    /**
     * Rastrear entrega por código
     */
    public function track(string $trackingCode): JsonResponse
    {
        $delivery = $this->service->findByTrackingCode($trackingCode);

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Código de rastreamento inválido.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $delivery
        ]);
    }

    /**
     * Listar entregas em trânsito
     */
    public function inTransit(): JsonResponse
    {
        $deliveries = $this->service->getInTransit();

        return response()->json([
            'success' => true,
            'data' => $deliveries
        ]);
    }

    /**
     * Listar entregas por veículo
     */
    public function byVehicle(string $vehicleId): JsonResponse
    {
        $deliveries = $this->service->getByVehicle((int) $vehicleId);

        return response()->json([
            'success' => true,
            'data' => $deliveries
        ]);
    }

    /**
     * Listar entregas por motorista
     */
    public function byDriver(string $driverId): JsonResponse
    {
        $deliveries = $this->service->getByDriver((int) $driverId);

        return response()->json([
            'success' => true,
            'data' => $deliveries
        ]);
    }
}
