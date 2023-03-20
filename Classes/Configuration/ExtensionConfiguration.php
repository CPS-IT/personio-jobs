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

namespace CPSIT\Typo3PersonioJobs\Configuration;

use CPSIT\Typo3PersonioJobs\Exception\InvalidApiUrlException;
use CPSIT\Typo3PersonioJobs\Extension;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as BaseConfiguration;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\Uri;

/**
 * ExtensionConfiguration
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ExtensionConfiguration
{
    public function __construct(
        private readonly BaseConfiguration $configuration,
    ) {
    }

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

    public function getStoragePid(): int
    {
        try {
            $jobPid = $this->configuration->get(Extension::KEY, 'storagePid');
        } catch (Exception) {
            return 0;
        }

        if (!is_numeric($jobPid) || $jobPid <= 0) {
            return 0;
        }

        return (int)$jobPid;
    }
}
