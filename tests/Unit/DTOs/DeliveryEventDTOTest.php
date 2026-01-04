<?php

namespace Tests\Unit\DTOs;

use App\DTOs\DeliveryEventDTO;
use Tests\TestCase;

class DeliveryEventDTOTest extends TestCase
{
    /** @test */
    public function it_creates_dto_from_request_data()
    {
        $data = [
            'status' => 'Saiu para entrega',
            'latitude' => -23.5505199,
            'longitude' => -46.6333094,
            'observation' => 'Motorista a caminho do destino',
        ];

        $dto = DeliveryEventDTO::fromRequest($data, deliveryId: 1);

        $this->assertEquals(1, $dto->delivery_id);
        $this->assertEquals('Saiu para entrega', $dto->status);
        $this->assertEquals(-23.5505199, $dto->latitude);
        $this->assertEquals(-46.6333094, $dto->longitude);
        $this->assertEquals('Motorista a caminho do destino', $dto->observation);
    }

    /** @test */
    public function it_converts_dto_to_array()
    {
        $dto = new DeliveryEventDTO(
            delivery_id: 1,
            status: 'Entrega realizada',
            latitude: -23.5505199,
            longitude: -46.6333094,
            observation: 'Cliente assinou comprovante',
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['delivery_id']);
        $this->assertEquals('Entrega realizada', $array['status']);
        $this->assertEquals(-23.5505199, $array['latitude']);
        $this->assertEquals(-46.6333094, $array['longitude']);
        $this->assertEquals('Cliente assinou comprovante', $array['observation']);
        $this->assertArrayHasKey('created_at', $array);
    }

    /** @test */
    public function it_handles_nullable_coordinates()
    {
        $data = [
            'status' => 'Aguardando coleta',
        ];

        $dto = DeliveryEventDTO::fromRequest($data, deliveryId: 1);

        $this->assertNull($dto->latitude);
        $this->assertNull($dto->longitude);
        $this->assertNull($dto->observation);
    }

    /** @test */
    public function it_sets_created_at_automatically()
    {
        $dto = new DeliveryEventDTO(
            delivery_id: 1,
            status: 'Teste',
            latitude: null,
            longitude: null,
            observation: null,
        );

        $array = $dto->toArray();

        $this->assertNotNull($array['created_at']);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $array['created_at']);
    }

    /** @test */
    public function it_converts_coordinates_to_float()
    {
        $data = [
            'status' => 'Teste',
            'latitude' => '-23.5505199', // String
            'longitude' => '-46.6333094', // String
        ];

        $dto = DeliveryEventDTO::fromRequest($data, deliveryId: 1);

        $this->assertIsFloat($dto->latitude);
        $this->assertIsFloat($dto->longitude);
        $this->assertEquals(-23.5505199, $dto->latitude);
        $this->assertEquals(-46.6333094, $dto->longitude);
    }
}
