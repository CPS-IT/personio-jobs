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

namespace CPSIT\Typo3PersonioJobs\Tests\Unit\Exception;

use CPSIT\Typo3PersonioJobs\Exception\InvalidArrayPathException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * InvalidArrayPathExceptionTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @covers \CPSIT\Typo3PersonioJobs\Exception\InvalidArrayPathException
 */
final class InvalidArrayPathExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function forUnexpectedTypeReturnsExceptionForUnexpectedType(): void
    {
        $actual = InvalidArrayPathException::forUnexpectedType('foo', 'array', 'NULL');

        self::assertSame('Expected array at array path "foo", got NULL instead.', $actual->getMessage());
        self::assertSame(1692177655, $actual->getCode());
    }

    /**
     * @test
     */
    public function forInvalidPathSegmentReturnsExceptionForInvalidPathSegment(): void
    {
        $actual = InvalidArrayPathException::forInvalidPathSegment('foo..baz');

        self::assertSame('The array path segment "foo..baz" is not valid.', $actual->getMessage());
        self::assertSame(1692178102, $actual->getCode());
    }
}
