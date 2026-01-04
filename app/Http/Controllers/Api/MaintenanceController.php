<?php

namespace App\Http\Controllers\Api;

use App\DTOs\MaintenanceDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Models\Maintenance;
use App\Services\MaintenanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function __construct(
        private MaintenanceService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Maintenance::class);

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

    public function show(string $id): JsonResponse
    {
        $maintenance = $this->service->findByIdWithoutScope((int) $id);

        if (!$maintenance) {
            return response()->json([
                'success' => false,
                'message' => 'Manutenção não encontrada.'
            ], 404);
        }

        $this->authorize('view', $maintenance);

        return response()->json([
            'success' => true,
            'data' => $maintenance
        ]);
    }

    public function store(StoreMaintenanceRequest $request): JsonResponse
    {
        $this->authorize('create', Maintenance::class);

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

    public function update(UpdateMaintenanceRequest $request, string $id): JsonResponse
    {
        $maintenance = $this->service->findByIdWithoutScope((int) $id);

        if (!$maintenance) {
            return response()->json([
                'success' => false,
                'message' => 'Manutenção não encontrada.'
            ], 404);
        }

        $this->authorize('update', $maintenance);

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

    public function destroy(string $id): JsonResponse
    {
        $maintenance = $this->service->findByIdWithoutScope((int) $id);

        if (!$maintenance) {
            return response()->json([
                'success' => false,
                'message' => 'Manutenção não encontrada.'
            ], 404);
        }

        $this->authorize('delete', $maintenance);

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

    public function pending(): JsonResponse
    {
        $this->authorize('viewAny', Maintenance::class);

        $maintenances = $this->service->getPending();

        return response()->json([
            'success' => true,
            'data' => $maintenances
        ]);
    }

    public function byVehicle(string $vehicleId): JsonResponse
    {
        $this->authorize('viewAny', Maintenance::class);

        $maintenances = $this->service->getByVehicle((int) $vehicleId);

        return response()->json([
            'success' => true,
            'data' => $maintenances
        ]);
    }
}
