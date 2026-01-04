<?php

namespace App\Http\Controllers\Api;

use App\DTOs\VehicleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct(
        private VehicleService $service
    ) {}

    /**
     * Listar veículos (filtrado por company automaticamente)
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Vehicle::class);

        $perPage = $request->input('per_page', 15);

        $filters = [
            'status' => $request->input('status'),
            'type' => $request->input('type'),
            'search' => $request->input('search'),
        ];

        $vehicles = $this->service->list($perPage, $filters);

        return response()->json([
            'success' => true,
            'data' => $vehicles->items(),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
                'last_page' => $vehicles->lastPage(),
            ]
        ]);
    }

    /**
     * Listar veículos disponíveis
     */
    public function available(): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Vehicle::class);

        $vehicles = $this->service->getAvailable();

        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }

    /**
     * Exibir veículo específico
     */
    public function show(string $id): JsonResponse
    {
        $vehicle = $this->service->findById((int) $id);

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Veículo não encontrado.'
            ], 404);
        }

        $this->authorize('view', $vehicle);

        return response()->json([
            'success' => true,
            'data' => $vehicle
        ]);
    }

    /**
     * Criar novo veículo
     */
    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $this->authorize('create', \App\Models\Vehicle::class);

        $dto = VehicleDTO::fromRequest(
            $request->validated(),
            auth()->user()->company_id
        );

        $vehicle = $this->service->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'Veículo cadastrado com sucesso!',
            'data' => $vehicle
        ], 201);
    }

    /**
     * Atualizar veículo
     */
    public function update(UpdateVehicleRequest $request, string $id): JsonResponse
    {
        $vehicle = $this->service->findById((int) $id);

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Veículo não encontrado.'
            ], 404);
        }

        $this->authorize('update', $vehicle);

        $dto = VehicleDTO::fromRequest(
            $request->validated(),
            auth()->user()->company_id,
            (int) $id
        );

        $updated = $this->service->update((int) $id, $dto);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar veículo.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Veículo atualizado com sucesso!'
        ]);
    }

    /**
     * Deletar veículo
     */
    public function destroy(string $id): JsonResponse
    {
        $vehicle = $this->service->findById((int) $id);

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Veículo não encontrado.'
            ], 404);
        }

        $this->authorize('delete', $vehicle);

        $deleted = $this->service->delete((int) $id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar veículo.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Veículo deletado com sucesso!'
        ]);
    }
}
