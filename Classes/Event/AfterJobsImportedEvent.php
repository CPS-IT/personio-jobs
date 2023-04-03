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
use CPSIT\Typo3PersonioJobs\Enums\ImportOperation;

/**
 * AfterJobsImportedEvent
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class AfterJobsImportedEvent
{
    /**
     * @param array<value-of<ImportOperation>, list<Job>> $processedJobs
     */
    public function __construct(
        private readonly array $processedJobs,
    ) {
    }

    /**
     * @return list<Job>
     */
    public function getNewJobs(): array
    {
        return $this->filterByOperation(ImportOperation::Added);
    }

    /**
     * @return list<Job>
     */
    public function getUpdatedJobs(): array
    {
        return $this->filterByOperation(ImportOperation::Updated);
    }

    /**
     * @return list<Job>
     */
    public function getRemovedJobs(): array
    {
        return $this->filterByOperation(ImportOperation::Removed);
    }

    /**
     * @return list<Job>
     */
    public function getSkippedJobs(): array
    {
        return $this->filterByOperation(ImportOperation::Skipped);
    }

    /**
     * @return array<value-of<ImportOperation>, list<Job>>
     */
    public function getAllProcessedJobs(): array
    {
        return $this->processedJobs;
    }

    /**
     * @return list<Job>
     */
    private function filterByOperation(ImportOperation $operation): array
    {
        return $this->processedJobs[$operation->value] ?? [];
    }
}
