<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $deliveries = [
            [
                'company_id' => 1,
                'driver_id' => 1,
                'vehicle_id' => 1,
                'status' => 'in_transit',
                'origin_address' => 'Av. Paulista, 1000 - São Paulo, SP',
                'origin_lat' => -23.5613,
                'origin_lng' => -46.6565,
                'destination_address' => 'Rua Augusta, 500 - São Paulo, SP',
                'destination_lat' => -23.5558,
                'destination_lng' => -46.6619,
                'distance_km' => 2.5,
                'estimated_time_minutes' => 15,
                'recipient_name' => 'Ana Costa',
                'recipient_phone' => '11999887766',
                'assigned_at' => now()->subHours(2),
                'picked_up_at' => now()->subHour(),
            ],
            [
                'company_id' => 1,
                'driver_id' => 2,
                'vehicle_id' => 2,
                'status' => 'delivered',
                'origin_address' => 'Av. Faria Lima, 2000 - São Paulo, SP',
                'origin_lat' => -23.5745,
                'origin_lng' => -46.6891,
                'destination_address' => 'Rua Oscar Freire, 100 - São Paulo, SP',
                'destination_lat' => -23.5629,
                'destination_lng' => -46.6721,
                'distance_km' => 3.2,
                'estimated_time_minutes' => 20,
                'recipient_name' => 'Bruno Lima',
                'recipient_phone' => '11988776655',
                'assigned_at' => now()->subHours(5),
                'picked_up_at' => now()->subHours(4),
                'delivered_at' => now()->subHours(3),
            ],
            [
                'company_id' => 1,
                'status' => 'pending',
                'origin_address' => 'Av. Ibirapuera, 3000 - São Paulo, SP',
                'origin_lat' => -23.5947,
                'origin_lng' => -46.6641,
                'destination_address' => 'Rua Vergueiro, 800 - São Paulo, SP',
                'destination_lat' => -23.5688,
                'destination_lng' => -46.6398,
                'distance_km' => 4.1,
                'estimated_time_minutes' => 25,
                'recipient_name' => 'Carla Mendes',
                'recipient_phone' => '11977665544',
            ],
        ];

        foreach ($deliveries as $delivery) {
            $deliveryModel = Delivery::create($delivery);

            // Adicionar eventos para deliveries com status diferente de pending
            if ($deliveryModel->status !== 'pending') {
                $deliveryModel->addEvent('assigned', null, null, 'Entrega atribuída');

                if ($deliveryModel->picked_up_at) {
                    $deliveryModel->addEvent(
                        'picked_up',
                        $deliveryModel->origin_lat,
                        $deliveryModel->origin_lng,
                        'Pedido coletado'
                    );
                }

                if ($deliveryModel->delivered_at) {
                    $deliveryModel->addEvent(
                        'delivered',
                        $deliveryModel->destination_lat,
                        $deliveryModel->destination_lng,
                        'Entrega concluída com sucesso'
                    );
                }
            }
        }
    }
}
