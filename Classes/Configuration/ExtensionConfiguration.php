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

namespace CPSIT\Typo3PersonioJobs\Configuration;

use CPSIT\Typo3PersonioJobs\Exception\InvalidApiUrlException;
use CPSIT\Typo3PersonioJobs\Extension;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as BaseConfiguration;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\Uri;

/**
 * ExtensionConfiguration
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
#[Autoconfigure(public: true)]
final readonly class ExtensionConfiguration
{
    public function __construct(
        private BaseConfiguration $configuration,
    ) {}

    /**
     * @throws InvalidApiUrlException
     */
    public function getApiUrl(): Uri
    {
        try {
            $apiUrl = $this->configuration->get(Extension::KEY, 'apiUrl');
        } catch (Exception) {
            throw InvalidApiUrlException::create();
        }

        if (!is_string($apiUrl) || trim($apiUrl) === '') {
            throw InvalidApiUrlException::create();
        }

        return new Uri($apiUrl);
    }
}
