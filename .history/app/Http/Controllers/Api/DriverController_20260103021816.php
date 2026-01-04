<?php

namespace App\Http\Controllers\Api;

use App\DTOs\DriverDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Services\DriverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct(
        private DriverService $service
    ) {}

    /**
     * Listar motoristas
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

        $filters = [
            'status' => $request->input('status'),
            'search' => $request->input('search'),
            'cnh_category' => $request->input('cnh_category'),
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

    /**
     * Exibir motorista específico
     */
    public function show(string $id): JsonResponse  // ✅ MUDADO DE int PARA string
    {
        $driver = $this->service->findById((int) $id);  // ✅ CONVERTE PARA int

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Motorista não encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $driver
        ]);
    }

    /**
     * Criar novo motorista
     */
    public function store(StoreDriverRequest $request): JsonResponse
    {
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

    /**
     * Atualizar motorista
     */
    public function update(UpdateDriverRequest $request, string $id): JsonResponse  // ✅ MUDADO DE int PARA string
    {
        $dto = DriverDTO::fromRequest(
            $request->validated(),
            auth()->user()->company_id,
            (int) $id  // ✅ CONVERTE PARA int
        );

        $updated = $this->service->update((int) $id, $dto);  // ✅ CONVERTE PARA int

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

    /**
     * Deletar motorista
     */
    public function destroy(string $id): JsonResponse  // ✅ MUDADO DE int PARA string
    {
        $deleted = $this->service->delete((int) $id);  // ✅ CONVERTE PARA int

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

    /**
     * Listar motoristas disponíveis
     */
    public function available(): JsonResponse
    {
        $drivers = $this->service->getAvailable();

        return response()->json([
            'success' => true,
            'data' => $drivers
        ]);
    }
}
