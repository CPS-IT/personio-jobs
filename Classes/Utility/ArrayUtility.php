<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "personio_jobs".
 *
 * Copyright (C) 2023 Elias Häußler <e.haeussler@familie-redlich.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace CPSIT\Typo3PersonioJobs\Utility;

use CPSIT\Typo3PersonioJobs\Exception\InvalidArrayPathException;

/**
 * ArrayUtility
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ArrayUtility
{
    /**
     * @param array<string, mixed> $array
     * @return array<string, mixed>
     * @throws InvalidArrayPathException
     */
    public static function convertToCollection(array $array, string $path): array
    {
        $reference = &$array;
        $pathSegments = str_getcsv($path, '.');
        $remainingSegments = $pathSegments;
        $currentPathSegments = [];

        foreach ($pathSegments as $pathSegment) {
            $currentPathSegments[] = array_shift($remainingSegments);

            // Validate path segment
            if (!is_string($pathSegment) || trim($pathSegment) === '') {
                throw InvalidArrayPathException::forInvalidPathSegment(implode('.', $currentPathSegments));
            }

            // Handle non-array values
            if (!is_array($reference)) {
                throw InvalidArrayPathException::forUnexpectedType(
                    implode('.', $currentPathSegments),
                    'array',
                    gettype($reference),
                );
            }

            // Handle placeholder for lists
            if ($pathSegment === '*') {
                $reference = self::convertListToCollection($reference, implode('.', $remainingSegments));

                return $array;
            }

            // Create node value if not exists
            if (!array_key_exists($pathSegment, $reference)) {
                $reference[$pathSegment] = [];
            }

            $reference = &$reference[$pathSegment];
        }

        // Handle non-array values
        if (!is_array($reference)) {
            throw InvalidArrayPathException::forUnexpectedType($path, 'array', gettype($reference));
        }

        // Convert array to list
        if (!array_is_list($reference)) {
            $reference = [$reference];
        }

        return $array;
    }

    /**
     * @param array<mixed> $array
     * @return array<int, mixed>
     * @throws InvalidArrayPathException
     */
    private static function convertListToCollection(array $array, string $path): array
    {
        // Handle non-lists
        if (!array_is_list($array)) {
            throw InvalidArrayPathException::forUnexpectedType($path, 'list', 'array');
        }

        foreach ($array as $key => $value) {
            $array[$key] = self::convertToCollection($value, $path);
        }

        return $array;
    }
}
