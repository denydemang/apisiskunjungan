<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationCheckRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Location\Coordinate;
use Location\Distance\Vincenty;

class LocationCheckController extends Controller
{
    private const IPINFO_TOKEN = "a8b0217ec1c8b7"; // Ganti dengan token asli
    private const VPN_PROVIDERS = [
        "Amazon", "Google", "Microsoft", "DigitalOcean", "Linode",
        "OVH", "Hetzner", "Contabo", "Alibaba", "Leaseweb", "Fastly", "Cloudflare"
    ];

    public function check(LocationCheckRequest $request)
    {
        $validated = $request->validated();

        try {
            $userLat = (float)$validated['latitude'];
            $userLon = (float)$validated['longitude'];
            $isRooted = $validated['root_jailbreak'] ?? false;

            // Simpan gambar
            $imagePath = $this->handleImageUpload($validated['image_base64'] ?? null);

            // Ambil info IP client
            $ipData = $this->fetchIpInfo($request);

            if (!isset($ipData['loc'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak dapat mengambil lokasi dari IP'
                ], 500);
            }

            // Ambil koordinat dari IP
            [$ipLat, $ipLon] = array_map('floatval', explode(',', $ipData['loc']));

            // Hitung jarak antar koordinat GPS dan IP
            $distanceKm = $this->calculateDistance($userLat, $userLon, $ipLat, $ipLon);

            // Deteksi VPN
            $isVpn = $this->isVpnConnection($ipData['org'] ?? '');

            // Buat response
            $response = [
                'gps_location' => [$userLat, $userLon],
                'ip_location' => [$ipLat, $ipLon],
                'distance_km' => round($distanceKm, 2),
                'vpn_detected' => $isVpn,
                'ip' => $ipData['ip'] ?? '',
                'org' => $ipData['org'] ?? '',
                'image_saved' => !is_null($imagePath),
                'root_jailbreak' => $isRooted
            ];

            if ($isVpn || $distanceKm > config('location.max_distance', 2)) {
                return response()->json(array_merge([
                    'status' => 'warning',
                    'message' => 'Lokasi mencurigakan: Kemungkinan fake GPS atau VPN terdeteksi.'
                ], $response), 200);
            }

            return response()->json(array_merge([
                'status' => 'success',
                'message' => 'Lokasi valid: Tidak terdeteksi fake GPS.'
            ], $response));

        } catch (\Throwable $e) {
            Log::error('Location check error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function handleImageUpload(?string $base64): ?string
    {
        if (empty($base64)) return null;

        try {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
            $filename = 'location_images/' . uniqid() . '.jpg';
            Storage::disk('local')->put($filename, $imageData);
            return $filename;
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    private function fetchIpInfo(Request $request): array
    {
        // Ambil IP client dari header atau default Laravel
        $clientIp = $request->header('X-Forwarded-For') ?? $request->ip();
        Log::info("Client IP Detected: " . $clientIp);

        $client = new Client(['timeout' => 5.0]);

        try {
            $response = $client->get("https://ipinfo.io/{$clientIp}/json", [
                'query' => ['token' => self::IPINFO_TOKEN]
            ]);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('IP info fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $coord1 = new Coordinate($lat1, $lon1);
        $coord2 = new Coordinate($lat2, $lon2);
        return $coord1->getDistance($coord2, new Vincenty()) / 1000; // dalam kilometer
    }

    private function isVpnConnection(string $org): bool
    {
        $orgLower = strtolower($org);

        foreach (self::VPN_PROVIDERS as $provider) {
            if (str_contains($orgLower, strtolower($provider))) {
                return true;
            }
        }

        return false;
    }
}
