<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected $apiKey;
    protected $baseUrl;
    protected $originPostalCode;

    public function __construct()
    {
        $this->apiKey = config('services.biteship.api_key');
        $this->baseUrl = config('services.biteship.base_url');
        $this->originPostalCode = config('services.biteship.origin.postal_code', '40111');
    }

    public function getRatesByPostalCode(array $params)
    {
        try {
            // Payload minimal untuk testing
            $payload = [
                'origin_postal_code' => '40111', // Gunakan kode pos yang valid di Biteship
                'destination_postal_code' => '40115', // Gunakan kode pos yang valid di Biteship
                'couriers' => ['jne'], // Coba 1 kurir dulu
                'items' => [
                    [
                        'name' => 'Test Product',
                        'description' => 'Test',
                        'value' => 100000,
                        'weight' => 1000,
                        'quantity' => 1,
                    ]
                ]
            ];

            Log::info('Biteship Test Payload:', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/rates/couriers', $payload);

            Log::info('Biteship Test Response Status: ' . $response->status());
            Log::info('Biteship Test Response Body: ' . $response->body());

            if ($response->successful()) {
                return $this->formatRatesResponse($response->json());
            }

            $errorBody = $response->json();
            return [
                'error' => $errorBody['message'] ?? $errorBody['error'] ?? 'Unknown error',
                'data' => [],
                'status' => $response->status(),
                'detail' => $errorBody
            ];

        } catch (\Exception $e) {
            Log::error('Biteship Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * Search location (autocomplete alamat)
     */
    public function searchLocation(string $query, string $country = 'ID')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/maps/addresses', [
                'q' => $query,
                'country' => $country,
                'limit' => 10,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Biteship Search Location Error: ' . $response->body());
            return ['error' => 'Gagal mencari alamat', 'data' => []];

        } catch (\Exception $e) {
            Log::error('Biteship Search Location Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * Get shipping rates (cek ongkir)
     */
    public function getRates(array $params)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/rates', [
                'origin_latitude' => $this->originLat,
                'origin_longitude' => $this->originLng,
                'destination_latitude' => $params['destination_lat'],
                'destination_longitude' => $params['destination_lng'],
                'couriers' => $params['couriers'] ?? ['jne', 'jnt', 'sicepat', 'pos', 'anteraja'],
                'items' => $this->formatItems($params['items'] ?? []),
            ]);

            if ($response->successful()) {
                return $this->formatRatesResponse($response->json());
            }

            Log::error('Biteship API Error: ' . $response->body());
            return ['error' => 'Gagal mendapatkan ongkir', 'data' => []];

        } catch (\Exception $e) {
            Log::error('Biteship Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * Format items untuk Biteship
     */
    protected function formatItems(array $items)
    {
        $formatted = [];
        foreach ($items as $item) {
            $weight = (int) ($item['weight'] ?? 1000);
            $quantity = (int) ($item['quantity'] ?? 1);
            
            $formatted[] = [
                'name' => $item['name'] ?? 'Produk',
                'description' => $item['description'] ?? '',
                'value' => (int) ($item['price'] ?? 0),
                'weight' => $weight,
                'quantity' => $quantity,
            ];
        }
        return $formatted;
    }

    /**
     * Format response rates
     */
    protected function formatRatesResponse(array $response)
    {
        $result = [];

        // Biteship response structure untuk /rates/couriers
        if (isset($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $courier) {
                $courierCode = $courier['courier_code'] ?? '';
                $courierName = $courier['courier_name'] ?? $courierCode;

                $services = [];
                if (isset($courier['services']) && is_array($courier['services'])) {
                    foreach ($courier['services'] as $service) {
                        $services[] = [
                            'service' => $service['service_code'] ?? $service['service_name'] ?? '',
                            'description' => $service['service_name'] ?? '',
                            'cost' => (int) ($service['price'] ?? 0),
                            'etd' => $service['duration'] ?? '-',
                        ];
                    }
                }

                $result[] = [
                    'code' => $courierCode,
                    'name' => $courierName,
                    'services' => $services,
                ];
            }
        }

        return $result;
    }

    /**
     * Create shipping order (Buat pesanan pengiriman)
     */
    public function createOrder(array $data)
    {
        try {
            $payload = [
                'origin_contact_name' => $data['origin_name'] ?? 'Toko',
                'origin_contact_phone' => $data['origin_phone'] ?? '08123456789',
                'origin_address' => $data['origin_address'] ?? config('app.name'),
                'origin_postal_code' => $this->originPostalCode,
                'destination_contact_name' => $data['shipping_name'],
                'destination_contact_phone' => $data['shipping_phone'],
                'destination_address' => $data['shipping_address'],
                'destination_postal_code' => $data['shipping_postal_code'],
                'courier_code' => $data['courier_code'],
                'courier_service_code' => $data['service_code'],
                'items' => $this->formatItems($data['items'] ?? []),
                'order_number' => $data['order_number'] ?? 'ORD-' . uniqid(),
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/orders', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Biteship Create Order Error: ' . $response->body());
            return ['error' => 'Gagal membuat pesanan pengiriman'];

        } catch (\Exception $e) {
            Log::error('Biteship Create Order Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Track shipment (Lacak paket)
     */
    public function trackOrder($orderId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/tracking/' . $orderId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Biteship Track Error: ' . $response->body());
            return ['error' => 'Gagal melacak paket'];

        } catch (\Exception $e) {
            Log::error('Biteship Track Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get waybill (Cetak resi)
     */
    public function getWaybill($orderId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/orders/' . $orderId . '/waybill');

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Biteship Waybill Error: ' . $response->body());
            return ['error' => 'Gagal mendapatkan resi'];

        } catch (\Exception $e) {
            Log::error('Biteship Waybill Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}