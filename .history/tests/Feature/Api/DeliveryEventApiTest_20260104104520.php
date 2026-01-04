<?php

namespace Tests\Feature\Api;

use App\Models\Delivery;
use App\Models\DeliveryEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryEventApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_delivery_event()
    {
        $user = $this->authenticatedUser();
        $delivery = Delivery::factory()->create(['company_id' => $user->company_id]);

        $data = [
            'status' => 'Saiu para entrega',
            'latitude' => -23.5505199,
            'longitude' => -46.6333094,
            'observation' => 'Motorista a caminho',
        ];

        $response = $this->postJson("/api/v1/deliveries/{$delivery->id}/events", $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('delivery_events', [
            'delivery_id' => $delivery->id,
            'status' => 'Saiu para entrega',
        ]);
    }

    /** @test */
    public function user_can_list_delivery_events()
    {
        $user = $this->authenticatedUser();
        $delivery = Delivery::factory()->create(['company_id' => $user->company_id]);

        DeliveryEvent::factory()->count(5)->create(['delivery_id' => $delivery->id]);

        $response = $this->getJson("/api/v1/deliveries/{$delivery->id}/events");

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function user_cannot_create_event_for_other_company_delivery()
    {
        $user = $this->authenticatedUser();
        $otherCompanyDelivery = Delivery::factory()->create(); // Outra empresa

        $response = $this->postJson("/api/v1/deliveries/{$otherCompanyDelivery->id}/events", [
            'status' => 'Tentativa de hack',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('delivery_events', [
            'status' => 'Tentativa de hack',
        ]);
    }

    /** @test */
    public function user_can_get_recent_delivery_events()
    {
        $user = $this->authenticatedUser();
        $delivery = Delivery::factory()->create(['company_id' => $user->company_id]);

        // Eventos recentes (últimas 24h)
        DeliveryEvent::factory()->count(3)->create([
            'delivery_id' => $delivery->id,
            'created_at' => now()->subHours(12),
        ]);

        // Eventos antigos (mais de 24h)
        DeliveryEvent::factory()->count(2)->create([
            'delivery_id' => $delivery->id,
            'created_at' => now()->subHours(30),
        ]);

        $response = $this->getJson('/api/v1/delivery-events/recent');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function delivery_event_requires_status()
    {
        $user = $this->authenticatedUser();
        $delivery = Delivery::factory()->create(['company_id' => $user->company_id]);

        $response = $this->postJson("/api/v1/deliveries/{$delivery->id}/events", [
            'latitude' => -23.5505199,
            'longitude' => -46.6333094,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }

    /** @test */
    public function coordinates_are_optional_in_delivery_event()
    {
        $user = $this->authenticatedUser();
        $delivery = Delivery::factory()->create(['company_id' => $user->company_id]);

        $response = $this->postJson("/api/v1/deliveries/{$delivery->id}/events", [
            'status' => 'Aguardando coleta',
        ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function events_are_ordered_by_created_at_desc()
    {
        $user = $this->authenticatedUser();
        $delivery = Delivery::factory()->create(['company_id' => $user->company_id]);

        $event1 = DeliveryEvent::factory()->create([
            'delivery_id' => $delivery->id,
            'status' => 'Primeiro',
            'created_at' => now()->subHours(2),
        ]);

        $event2 = DeliveryEvent::factory()->create([
            'delivery_id' => $delivery->id,
            'status' => 'Segundo',
            'created_at' => now()->subHour(),
        ]);

        $event3 = DeliveryEvent::factory()->create([
            'delivery_id' => $delivery->id,
            'status' => 'Terceiro',
            'created_at' => now(),
        ]);

        $response = $this->getJson("/api/v1/deliveries/{$delivery->id}/events");

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.status', 'Terceiro');
        $response->assertJsonPath('data.1.status', 'Segundo');
        $response->assertJsonPath('data.2.status', 'Primeiro');
    }
}
