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

namespace CPSIT\Typo3PersonioJobs\Exception;

/**
 * InvalidArrayPathException
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class InvalidArrayPathException extends \Exception
{
    public static function forUnexpectedType(string $path, string $expected, string $actual): self
    {
        return new self(
            sprintf('Expected %s at array path "%s", got %s instead.', $expected, $path, $actual),
            1692177655,
        );
    }

    public static function forInvalidPathSegment(string $path): self
    {
        return new self(
            sprintf('The array path segment "%s" is not valid.', $path),
            1692178102,
        );
    }
}
