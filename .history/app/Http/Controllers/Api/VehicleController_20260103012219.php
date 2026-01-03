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

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only(['status', 'type', 'search']);

        $vehicles = $this->service->listPaginated($perPage, $filters);

        return response()->json($vehicles, 200);
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
        $dto = VehicleDTO::fromRequest($request->validated(), $companyId);
        $vehicle = $this->service->create($dto);

        return response()->json([
            'message' => 'Veículo cadastrado com sucesso!',
            'data' => $vehicle,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $vehicle = $this->service->findById($id);

        if (!$vehicle) {
            return response()->json([
                'message' => 'Veículo não encontrado.',
            ], 404);
        }

        return response()->json([
            'data' => $vehicle,
        ], 200);
    }

    public function update(UpdateVehicleRequest $request, int $id): JsonResponse
    {
        $vehicle = $this->service->findById($id);

        if (!$vehicle) {
            return response()->json([
                'message' => 'Veículo não encontrado.',
            ], 404);
        }

        $companyId = $request->user()->company_id;
        $dto = VehicleDTO::fromRequest($request->validated(), $companyId, $id);
        $this->service->update($vehicle, $dto);

        return response()->json([
            'message' => 'Veículo atualizado com sucesso!',
            'data' => $vehicle->fresh(),
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $vehicle = $this->service->findById($id);

        if (!$vehicle) {
            return response()->json([
                'message' => 'Veículo não encontrado.',
            ], 404);
        }

        try {
            $this->service->delete($vehicle);

            return response()->json([
                'message' => 'Veículo deletado com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
