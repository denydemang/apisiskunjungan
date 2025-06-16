<?php

return [
    /* Jarak maksimal (dalam km) antara lokasi GPS dan lokasi IP */
    'max_distance' => env('LOCATION_MAX_DISTANCE_KM', 2),

    /* Token untuk layanan ipinfo.io */
    'ipinfo_token' => env('IPINFO_TOKEN', 'a8b0217ec1c8b7'), // Default token contoh

    /* Daftar penyedia VPN/Cloud */
    'vpn_providers' => [
        'Amazon', 'Google', 'Microsoft', 'DigitalOcean', 'Linode',
        'OVH', 'Hetzner', 'Contabo', 'Alibaba', 'Leaseweb', 'Fastly', 'Cloudflare'
    ],
];