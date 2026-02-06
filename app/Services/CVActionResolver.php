<?php
namespace app\Services;

final class CVActionResolver
{
    private const MAP = [
        'DOWNLOAD_CV' => 'downloadcv',
    ];

    public static function resolve(?string $action): ?string
    {
        return self::MAP[$action] ?? null;
    }
}