<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "cpsit/personio-jobs".
 *
 * Copyright (C) 2023 Martin Adler <m.adler@familie-redlich.de>
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

namespace Unit\Utility;

use CPSIT\Typo3PersonioJobs\Utility\FrontendUtility;
use PHPUnit\Framework\TestCase;

class FrontendUtilityTest extends TestCase
{
    /**
     * @test
     */
    public function getServerRequestReturnsServerRequest(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = new \TYPO3\CMS\Core\Http\ServerRequest();

        $serverRequest = FrontendUtility::getServerRequest();
        self::assertInstanceOf(\Psr\Http\Message\ServerRequestInterface::class, $serverRequest);
        self::assertSame($GLOBALS['TYPO3_REQUEST'], $serverRequest);
    }
}
