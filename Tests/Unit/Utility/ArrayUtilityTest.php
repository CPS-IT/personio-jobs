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

namespace CPSIT\Typo3PersonioJobs\Tests\Unit\Utility;

use CPSIT\Typo3PersonioJobs\Exception\InvalidArrayPathException;
use CPSIT\Typo3PersonioJobs\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * ArrayUtilityTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @covers \CPSIT\Typo3PersonioJobs\Utility\ArrayUtility
 */
final class ArrayUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function convertToCollectionThrowsExceptionOnInvalidPathSegments(): void
    {
        $this->expectExceptionObject(
            InvalidArrayPathException::forInvalidPathSegment('foo.'),
        );

        ArrayUtility::convertToCollection([], 'foo..baz');
    }

    /**
     * @test
     */
    public function convertToCollectionThrowsExceptionOnNonListValues(): void
    {
        $this->expectExceptionObject(
            InvalidArrayPathException::forUnexpectedType('foo', 'list', 'array'),
        );

        $array = [
            'foo' => [
                'baz' => null,
            ],
        ];

        ArrayUtility::convertToCollection($array, 'foo.*.baz');
    }

    /**
     * @test
     */
    public function convertToCollectionConvertsRespectsListPlaceholders(): void
    {
        $array = [
            'foo' => [
                [
                    'baz' => [
                        'hello' => 'world',
                    ],
                ],
                [
                    'baz' => [
                        'hello' => 'world',
                    ],
                ],
            ],
        ];

        $expected = [
            'foo' => [
                [
                    'baz' => [
                        [
                            'hello' => 'world',
                        ],
                    ],
                ],
                [
                    'baz' => [
                        [
                            'hello' => 'world',
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($expected, ArrayUtility::convertToCollection($array, 'foo.*.baz'));
    }

    /**
     * @test
     */
    public function convertToCollectionThrowsExceptionOnNonArrayValues(): void
    {
        $this->expectExceptionObject(
            InvalidArrayPathException::forUnexpectedType('foo', 'array', 'NULL'),
        );

        $array = [
            'foo' => null,
        ];

        ArrayUtility::convertToCollection($array, 'foo.baz');
    }

    /**
     * @test
     */
    public function convertToCollectionConvertsGivenPathToCollection(): void
    {
        $array = [
            'foo' => [
                'baz' => [
                    'hello' => 'world',
                ],
            ],
        ];

        $expected = [
            'foo' => [
                'baz' => [
                    [
                        'hello' => 'world',
                    ],
                ],
            ],
        ];

        self::assertSame($expected, ArrayUtility::convertToCollection($array, 'foo.baz'));
    }
}
