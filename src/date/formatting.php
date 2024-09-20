<?php declare(strict_types=1);

namespace date;

/**
 * Get's the date from a unix timestamp using the specified format.
 *
 * @param int $timestamp Unix Timestamps.
 * @param string $format (optional) Date Format.
 * @return string
 */
function get(int $timestamp, string $format = 'd/m/Y'): string
{
    return date($format, $timestamp);
}

/**
 * Splits the date, given in unix timestamps, into the passed format.
 *
 * @param int $timestamp Unix Timestamps.
 * @param string $format (optional) Date Format.
 * @param string $sep (optional) Separator to split.
 * @return array<string, string>
 */
function split(int $timestamp, string $format = 'd/M/Y', string $sep = '/'): array
{
    [$day, $month, $year] = explode($sep, date($format, $timestamp));

    return [
        'day' => $day,
        'month' => $month,
        'year' => $year,
    ];
}
