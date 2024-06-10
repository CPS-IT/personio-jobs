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

namespace CPSIT\Typo3PersonioJobs\Tests\Unit\Fixtures\Classes;

use Psr\Http\Message\ResponseInterface;
use Throwable;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Response;

/**
 * DummyRequestFactory
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @internal
 */
final class DummyRequestFactory extends RequestFactory
{
    public ?string $lastUri = null;

    public function __construct(
        public ResponseInterface $response = new Response(),
        public ?Throwable $exception = null,
    ) {
        // Missing parent constructor call is intended.
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $uri, string $method = 'GET', array $options = []): ResponseInterface
    {
        $this->lastUri = $uri;

        if ($this->exception !== null) {
            throw $this->exception;
        }

        return $this->response;
    }
}
