<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct(
        private VehicleService $vehicleService
    ) {}

    /**
     * Lista veículos com paginação e filtros
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only(['status', 'type', 'search']);

        $vehicles = $this->vehicleService->list($perPage, $filters);

        return response()->json([
            'success' => true,
            'data' => $vehicles->items(),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
            ]
        ]);
    }

    /**
     * Exibe um veículo específico
     */
    public function show(string $id): JsonResponse
    {
        $vehicle = $this->vehicleService->findById((int) $id);

        return response()->json([
            'success' => true,
            'data' => $vehicle
        ]);
    }

    /**
     * Cria um novo veículo
     */
    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $vehicle = $this->vehicleService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Veículo criado com sucesso',
            'data' => $vehicle
        ], 201);
    }

    /**
     * Atualiza um veículo
     */
    public function update(UpdateVehicleRequest $request, string $id): JsonResponse
    {
        $vehicle = $this->vehicleService->update((int) $id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Veículo atualizado com sucesso',
            'data' => $vehicle
        ]);
    }

    /**
     * Remove um veículo
     */
    public function destroy(string $id): JsonResponse
    {
        $this->vehicleService->delete((int) $id);

        return response()->json([
            'success' => true,
            'message' => 'Veículo removido com sucesso'
        ]);
    }

    /**
     * Lista veículos disponíveis
     */
    public function available(Request $request): JsonResponse
    {
        $vehicles = $this->vehicleService->getAvailable();

        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }
}
