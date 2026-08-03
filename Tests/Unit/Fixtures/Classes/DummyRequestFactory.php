<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "personio_jobs".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace CPSIT\Typo3PersonioJobs\Tests\Unit\Fixtures\Classes;

use Psr\Http\Message\ResponseInterface;
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
    /* @phpstan-ignore constructor.missingParentCall */
    public function __construct(
        public ResponseInterface $response = new Response(),
        public ?\Throwable $exception = null,
    ) {
        // Missing parent constructor call is intended.
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $uri, string $method = 'GET', array $options = [], ?string $context = null): ResponseInterface
    {
        if ($this->exception !== null) {
            throw $this->exception;
        }

        return $this->response;
    }
}
