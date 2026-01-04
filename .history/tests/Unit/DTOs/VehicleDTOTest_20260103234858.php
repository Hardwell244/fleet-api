<?php

namespace Tests\Unit\DTOs;

use App\DTOs\VehicleDTO;
use Tests\TestCase;

class VehicleDTOTest extends TestCase
{
    /** @test */
    public function it_creates_dto_from_request_data()
    {
        $data = [
            'plate' => 'ABC1234',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2023,
            'type' => 'car',
            'status' => 'available',
            'fuel_capacity' => 50.0,
            'current_km' => 1000.0,
        ];

        $dto = VehicleDTO::fromRequest($data, companyId: 1);

        $this->assertEquals(1, $dto->company_id);
        $this->assertEquals('ABC1234', $dto->plate);
        $this->assertEquals('Toyota', $dto->brand);
        $this->assertEquals('Corolla', $dto->model);
        $this->assertEquals(2023, $dto->year);
        $this->assertEquals('car', $dto->type);
        $this->assertEquals('available', $dto->status);
        $this->assertEquals(50.0, $dto->fuel_capacity);
        $this->assertEquals(1000.0, $dto->current_km);
    }

    /** @test */
    public function it_converts_dto_to_array()
    {
        $dto = new VehicleDTO(
            id: null,
            company_id: 1,
            plate: 'ABC1234',
            brand: 'Toyota',
            model: 'Corolla',
            year: 2023,
            type: 'car',
            status: 'available',
            fuel_capacity: 50.0,
            current_km: 1000.0,
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['company_id']);
        $this->assertEquals('ABC1234', $array['plate']);
        $this->assertEquals('Toyota', $array['brand']);
    }

    /** @test */
    public function it_uppercases_plate_automatically()
    {
        $data = [
            'plate' => 'abc1234',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2023,
            'type' => 'car',
        ];

        $dto = VehicleDTO::fromRequest($data, companyId: 1);

        $this->assertEquals('ABC1234', $dto->plate);
    }
}
