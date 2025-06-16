<?php

if (!function_exists('viteAsset')) {
    function viteAsset($entry)
    {
        $manifestPath = public_path('assets/manifest.json');
        if (!file_exists($manifestPath)) return '';
        $manifest = json_decode(file_get_contents($manifestPath), true);
        return isset($manifest[$entry]) ? asset('assets/' . $manifest[$entry]['file']) : '';
    }
}

if (!function_exists('viteCss')) {
    function viteCss($entry)
    {
        $manifestPath = public_path('assets/manifest.json');
        if (!file_exists($manifestPath)) return [];
        $manifest = json_decode(file_get_contents($manifestPath), true);
        $cssFiles = $manifest[$entry]['css'] ?? [];
        return array_map(fn($css) => asset('assets/' . $css), $cssFiles);
    }
}
