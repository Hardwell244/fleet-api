<?php

namespace Tests\Unit\DTOs;

use App\DTOs\DriverDTO;
use Tests\TestCase;

class DriverDTOTest extends TestCase
{
    /** @test */
    public function it_creates_dto_from_request_data()
    {
        $data = [
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'cnh' => 'ABC123456',
            'cnh_category' => 'D',
            'cnh_expiry' => '2026-12-31',
            'phone' => '11999999999',
            'email' => 'joao@example.com',
            'status' => 'active',
        ];

        $dto = DriverDTO::fromRequest($data, companyId: 1);

        $this->assertEquals(1, $dto->company_id);
        $this->assertEquals('João Silva', $dto->name);
        $this->assertEquals('12345678901', $dto->cpf);
        $this->assertEquals('ABC123456', $dto->cnh);
        $this->assertEquals('D', $dto->cnh_category);
        $this->assertEquals('active', $dto->status);
    }

    /** @test */
    public function it_removes_cpf_formatting()
    {
        $data = [
            'name' => 'João Silva',
            'cpf' => '123.456.789-01',
            'cnh' => 'ABC123456',
            'cnh_category' => 'D',
            'cnh_expiry' => '2026-12-31',
        ];

        $dto = DriverDTO::fromRequest($data, companyId: 1);

        $this->assertEquals('12345678901', $dto->cpf);
    }
}
