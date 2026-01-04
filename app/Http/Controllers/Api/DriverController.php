<?php

namespace App\Http\Controllers\Api;

use App\DTOs\DriverDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use App\Services\DriverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct(
        private DriverService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Driver::class);

        $perPage = $request->input('per_page', 15);

        $filters = [
            'status' => $request->input('status'),
            'search' => $request->input('search'),
        ];

        $drivers = $this->service->list($perPage, $filters);

        return response()->json([
            'success' => true,
            'data' => $drivers->items(),
            'meta' => [
                'current_page' => $drivers->currentPage(),
                'per_page' => $drivers->perPage(),
                'total' => $drivers->total(),
                'last_page' => $drivers->lastPage(),
            ]
        ]);
    }

    public function available(): JsonResponse
    {
        $this->authorize('viewAny', Driver::class);

        $drivers = $this->service->getAvailable();

        return response()->json([
            'success' => true,
            'data' => $drivers
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $driver = $this->service->findByIdWithoutScope((int) $id);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Motorista não encontrado.'
            ], 404);
        }

        $this->authorize('view', $driver);

        return response()->json([
            'success' => true,
            'data' => $driver
        ]);
    }

    public function store(StoreDriverRequest $request): JsonResponse
    {
        $this->authorize('create', Driver::class);

        $dto = DriverDTO::fromRequest(
            $request->validated(),
            auth()->user()->company_id
        );

        $driver = $this->service->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'Motorista cadastrado com sucesso!',
            'data' => $driver
        ], 201);
    }

    public function update(UpdateDriverRequest $request, string $id): JsonResponse
    {
        $driver = $this->service->findByIdWithoutScope((int) $id);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Motorista não encontrado.'
            ], 404);
        }

        $this->authorize('update', $driver);

        $dto = DriverDTO::fromRequest(
            $request->validated(),
            auth()->user()->company_id,
            (int) $id
        );

        $updated = $this->service->update((int) $id, $dto);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar motorista.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Motorista atualizado com sucesso!'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $driver = $this->service->findByIdWithoutScope((int) $id);

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Motorista não encontrado.'
            ], 404);
        }

        $this->authorize('delete', $driver);

        $deleted = $this->service->delete((int) $id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar motorista.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Motorista deletado com sucesso!'
        ]);
    }
}
