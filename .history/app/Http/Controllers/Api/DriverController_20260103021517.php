<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Services\DriverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct(
        private DriverService $driverService
    ) {}

    /**
     * Lista motoristas com paginação e filtros
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only(['status', 'cnh_category', 'search']);

        $drivers = $this->driverService->list($perPage, $filters);

        return response()->json([
            'success' => true,
            'data' => $drivers->items(),
            'meta' => [
                'current_page' => $drivers->currentPage(),
                'last_page' => $drivers->lastPage(),
                'per_page' => $drivers->perPage(),
                'total' => $drivers->total(),
            ]
        ]);
    }

    /**
     * Exibe um motorista específico
     */
    public function show(string $id): JsonResponse
    {
        $driver = $this->driverService->findById((int) $id);

        return response()->json([
            'success' => true,
            'data' => $driver
        ]);
    }

    /**
     * Cria um novo motorista
     */
    public function store(StoreDriverRequest $request): JsonResponse
    {
        $driver = $this->driverService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Motorista criado com sucesso',
            'data' => $driver
        ], 201);
    }

    /**
     * Atualiza um motorista
     */
    public function update(UpdateDriverRequest $request, string $id): JsonResponse
    {
        $driver = $this->driverService->update((int) $id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Motorista atualizado com sucesso',
            'data' => $driver
        ]);
    }

    /**
     * Remove um motorista
     */
    public function destroy(string $id): JsonResponse
    {
        $this->driverService->delete((int) $id);

        return response()->json([
            'success' => true,
            'message' => 'Motorista removido com sucesso'
        ]);
    }

    /**
     * Lista motoristas disponíveis
     */
    public function available(Request $request): JsonResponse
    {
        $drivers = $this->driverService->getAvailable();

        return response()->json([
            'success' => true,
            'data' => $drivers
        ]);
    }
}
