<?php
require_once __DIR__ . '/config.php';

class Api {

    private static function fetch(string $endpoint): ?array {
        $url = API_BASE_URL . $endpoint;
        $cacheKey = 'api_' . md5($url);
        $cacheFile = sys_get_temp_dir() . '/' . $cacheKey . '.json';

        // Serve from file cache if fresh
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL) {
            $raw = file_get_contents($cacheFile);
            if ($raw) return json_decode($raw, true);
        }

        // cURL request — server-side only, never visible to browser
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'User-Agent: SpidyUniverse/1.0',
            ],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || !$body) return null;

        // Cache to file
        file_put_contents($cacheFile, $body);

        return json_decode($body, true);
    }

    public static function getBatches(): array {
        $data = self::fetch('/API/batches');
        return $data ?? ['batches' => [], 'total' => 0];
    }

    public static function getBatchDetail(int $batchId): ?array {
        return self::fetch('/API/batch/' . intval($batchId));
    }

    public static function getStats(): array {
        $data = self::fetch('/API/stats');
        return $data['stats'] ?? ['total_batches' => 12, 'total_videos' => 1025];
    }

    public static function searchVideos(string $q, ?int $batchId = null): array {
        $ep = '/API/search?q=' . urlencode($q);
        if ($batchId) $ep .= '&batch_id=' . intval($batchId);
        $data = self::fetch($ep);
        return $data ?? ['results' => [], 'total_results' => 0];
    }
}
