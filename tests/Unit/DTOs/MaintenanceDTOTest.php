<?php

namespace Tests\Unit\DTOs;

use App\DTOs\MaintenanceDTO;
use Tests\TestCase;

class MaintenanceDTOTest extends TestCase
{
    /** @test */
    public function it_creates_dto_from_request_data()
    {
        $data = [
            'vehicle_id' => 1,
            'type' => 'preventive',
            'description' => 'Troca de óleo',
            'scheduled_date' => '2026-02-01',
            'completed_date' => null,
            'cost' => 350.00,
            'notes' => 'Trocar filtro também',
            'status' => 'scheduled',
        ];

        $dto = MaintenanceDTO::fromRequest($data, companyId: 1);

        $this->assertEquals(1, $dto->company_id);
        $this->assertEquals(1, $dto->vehicle_id);
        $this->assertEquals('preventive', $dto->type);
        $this->assertEquals('Troca de óleo', $dto->description);
        $this->assertEquals('2026-02-01', $dto->scheduled_date);
        $this->assertEquals(350.00, $dto->cost);
        $this->assertEquals('scheduled', $dto->status);
        $this->assertEquals('Trocar filtro também', $dto->notes);
    }

    /** @test */
    public function it_converts_dto_to_array()
    {
        $dto = new MaintenanceDTO(
            id: null,
            company_id: 1,
            vehicle_id: 1,
            type: 'preventive',
            description: 'Troca de óleo',
            scheduled_date: '2026-02-01',
            completed_date: null,
            cost: 350.00,
            status: 'scheduled',
            notes: null,
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['company_id']);
        $this->assertEquals(1, $array['vehicle_id']);
        $this->assertEquals('preventive', $array['type']);
        $this->assertEquals(350.00, $array['cost']);
        $this->assertEquals('scheduled', $array['status']);
    }

    /** @test */
    public function it_handles_nullable_fields()
    {
        $data = [
            'vehicle_id' => 1,
            'type' => 'corrective',
            'description' => 'Teste',
            'scheduled_date' => '2026-02-01',
        ];

        $dto = MaintenanceDTO::fromRequest($data, companyId: 1);

        $this->assertNull($dto->cost);
        $this->assertNull($dto->notes);
        $this->assertNull($dto->completed_date);
        $this->assertEquals('scheduled', $dto->status); // Status padrão
    }

    /** @test */
    public function it_sets_default_status_as_scheduled()
    {
        $data = [
            'vehicle_id' => 1,
            'type' => 'preventive',
            'description' => 'Teste',
            'scheduled_date' => '2026-02-01',
        ];

        $dto = MaintenanceDTO::fromRequest($data, companyId: 1);

        $this->assertEquals('scheduled', $dto->status);
    }

    /** @test */
    public function it_converts_cost_to_float()
    {
        $data = [
            'vehicle_id' => 1,
            'type' => 'preventive',
            'description' => 'Teste',
            'scheduled_date' => '2026-02-01',
            'cost' => '500.50', // String
        ];

        $dto = MaintenanceDTO::fromRequest($data, companyId: 1);

        $this->assertIsFloat($dto->cost);
        $this->assertEquals(500.50, $dto->cost);
    }
}
