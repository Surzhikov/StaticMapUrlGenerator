<?php

declare(strict_types=1);

namespace Surzhikov\StaticMapUrlGenerator;

class GooglePolylineEncoder
{
    /**
     * Encoding list of [Latitude, Longitude] pairs into string.
     *
     * @param array<array<float>> $coordinates
     * @param int                 $precision
     *
     * @return string
     */
    public static function encode(array $coordinates, int $precision = 5): string
    {
        if (empty($coordinates)) {
            return '';
        }

        $factor = 10 ** $precision;
        $output = '';

        $prevLat = 0;
        $prevLng = 0;
        foreach ($coordinates as $c) {
            $curLng = static::round($c[0] * $factor);
            $curLat = static::round($c[1] * $factor);

            $output .= static::encodeCoordinate($curLat, $prevLat) . static::encodeCoordinate($curLng, $prevLng);

            $prevLat = $curLat;
            $prevLng = $curLng;
        }

        return $output;
    }

    private static function round(float $value): int
    {
        return (int)floor(abs($value + 0.5) * ($value >= 0 ? 1 : -1));
    }

    private static function encodeCoordinate(int $current, int $previous): string
    {
        $coordinate = ($current - $previous) << 1;
        if ($current < $previous) {
            $coordinate = ~$coordinate;
        }

        $output = '';
        while ($coordinate >= 0x20) {
            $output .= chr((0x20 | ($coordinate & 0x1f)) + 63);
            $coordinate >>= 5;
        }
        $output .= chr($coordinate + 63);

        return $output;
    }

}