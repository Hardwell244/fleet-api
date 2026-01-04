<?php

namespace App\Http\Controllers\Api;

use App\DTOs\MaintenanceDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Services\MaintenanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function __construct(
        private MaintenanceService $service
    ) {}

    /**
     * Listar manutenções
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

        $filters = [
            'status' => $request->input('status'),
            'type' => $request->input('type'),
            'vehicle_id' => $request->input('vehicle_id'),
            'search' => $request->input('search'),
        ];

        $maintenances = $this->service->list($perPage, $filters);

        return response()->json([
            'success' => true,
            'data' => $maintenances->items(),
            'meta' => [
                'current_page' => $maintenances->currentPage(),
                'per_page' => $maintenances->perPage(),
                'total' => $maintenances->total(),
                'last_page' => $maintenances->lastPage(),
            ]
        ]);
    }

    /**
     * Exibir manutenção específica
     */
    public function show(string $id): JsonResponse
    {
        $maintenance = $this->service->findById((int) $id);

        if (!$maintenance) {
            return response()->json([
                'success' => false,
                'message' => 'Manutenção não encontrada.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $maintenance
        ]);
    }

    /**
     * Criar nova manutenção
     */
    public function store(StoreMaintenanceRequest $request): JsonResponse
    {
        $dto = MaintenanceDTO::fromRequest(
            $request->validated(),
            auth()->user()->company_id
        );

        $maintenance = $this->service->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'Manutenção cadastrada com sucesso!',
            'data' => $maintenance
        ], 201);
    }

    /**
     * Atualizar manutenção
     */
    public function update(UpdateMaintenanceRequest $request, string $id): JsonResponse
    {
        $dto = MaintenanceDTO::fromRequest(
            $request->validated(),
            auth()->user()->company_id,
            (int) $id
        );

        $updated = $this->service->update((int) $id, $dto);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar manutenção.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Manutenção atualizada com sucesso!'
        ]);
    }

    /**
     * Deletar manutenção
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->service->delete((int) $id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar manutenção.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Manutenção deletada com sucesso!'
        ]);
    }

    /**
     * Listar manutenções pendentes
     */
    public function pending(): JsonResponse
    {
        $maintenances = $this->service->getPending();

        return response()->json([
            'success' => true,
            'data' => $maintenances
        ]);
    }

    /**
     * Listar manutenções por veículo
     */
    public function byVehicle(string $vehicleId): JsonResponse
    {
        $maintenances = $this->service->getByVehicle((int) $vehicleId);

        return response()->json([
            'success' => true,
            'data' => $maintenances
        ]);
    }
}
