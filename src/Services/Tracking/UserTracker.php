<?php

namespace JarirAhmed\AuthMicroservice\Services\Tracking;

class UserTracker
{
    public function capture(string $ipAddress, string $userAgent): array
    {
        return [
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device'     => $this->parseDevice($userAgent),
            'os'         => $this->parseOs($userAgent),
            'browser'    => $this->parseBrowser($userAgent),
            'geo'        => $this->geolocate($ipAddress),
        ];
    }

    private function parseDevice(string $ua): string
    {
        if (preg_match('/tablet|ipad/i', $ua)) return 'Tablet';
        if (preg_match('/mobile|android|iphone/i', $ua)) return 'Mobile';
        return 'Desktop';
    }

    private function parseOs(string $ua): string
    {
        $map = [
            'Windows NT 10' => 'Windows 10',
            'Windows NT 6.3' => 'Windows 8.1',
            'Windows NT 6.1' => 'Windows 7',
            'Mac OS X'       => 'macOS',
            'Android'        => 'Android',
            'iPhone'         => 'iOS',
            'iPad'           => 'iPadOS',
            'Linux'          => 'Linux',
        ];
        foreach ($map as $pattern => $os) {
            if (str_contains($ua, $pattern)) return $os;
        }
        return 'Unknown';
    }

    private function parseBrowser(string $ua): string
    {
        $map = [
            'Edg'     => 'Edge',
            'OPR'     => 'Opera',
            'Chrome'  => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari'  => 'Safari',
        ];
        foreach ($map as $token => $browser) {
            if (str_contains($ua, $token)) return $browser;
        }
        return 'Unknown';
    }

    private function geolocate(string $ip): array
    {
        if (in_array($ip, ['127.0.0.1', '::1'], true)) {
            return ['country' => 'Local', 'city' => 'Local'];
        }

        $url = rtrim(config('auth-microservice.tracking.geolocation_api', 'http://ip-api.com/json'), '/') . '/' . $ip;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 3,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) return ['country' => null, 'city' => null];

        $data = json_decode($response, true);
        return [
            'country' => $data['country'] ?? null,
            'city'    => $data['city'] ?? null,
        ];
    }
}
