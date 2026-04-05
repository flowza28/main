<?php

if (! function_exists('format_bandwidth')) {
    function format_bandwidth(int $kbps): string
    {
        if ($kbps >= 1024) {
            return round($kbps / 1024, 2) . ' Mbps';
        }

        return number_format($kbps) . ' Kbps';
    }
}

if (! function_exists('format_bytes')) {
    function format_bytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
