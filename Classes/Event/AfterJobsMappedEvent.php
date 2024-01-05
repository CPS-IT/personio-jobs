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

namespace CPSIT\Typo3PersonioJobs\Event;

use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use TYPO3\CMS\Core\Http\Uri;

/**
 * AfterJobsMappedEvent
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class AfterJobsMappedEvent
{
    /**
     * @param list<Job> $jobs
     */
    public function __construct(
        private readonly Uri $requestUri,
        private readonly array $jobs,
        private readonly ?string $language = null,
    ) {}

    public function getRequestUri(): Uri
    {
        return $this->requestUri;
    }

    /**
     * @return list<Job>
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }
}
