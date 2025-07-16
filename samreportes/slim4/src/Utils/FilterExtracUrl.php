<?php

namespace App\Utils;

class FilterExtracUrl
{
    public function __construct() {}

    public static function extractUrl(string $url): ?array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);

        if ($path === false || $path === '' || $path === '/') {
            return null;
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        $subfolder = null;
        if (count($segments) >= 2) {
            $subfolder = $segments[count($segments) - 2];
        }

        $basename = basename($path);

        // This regex captures the unique ID between 'img-' and '.jpg' (or other common image extensions)
        // It's more specific now.
        $uniqueId = null;
        if (preg_match('/^img-([a-f0-9]+)\.(jpg|jpeg|png|gif|webp)$/i', $basename, $matches)) {
            $uniqueId = $matches[1];
        }

        return [
            'subfolder' => $subfolder,
            'basename' => $uniqueId,
        ];
    }
}
