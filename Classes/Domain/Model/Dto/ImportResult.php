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

namespace CPSIT\Typo3PersonioJobs\Domain\Model\Dto;

use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Enums\ImportOperation;

/**
 * ImportResult
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ImportResult
{
    /**
     * @var array<value-of<ImportOperation>, list<Job>>
     */
    private array $operations = [];

    public function __construct(
        private readonly bool $dryRun,
    ) {}

    public function add(Job $job, ImportOperation $operation): self
    {
        $this->operations[$operation->value] ??= [];
        $this->operations[$operation->value][] = $job;

        return $this;
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
        return $this->operations;
    }

    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * @return list<Job>
     */
    private function filterByOperation(ImportOperation $operation): array
    {
        return $this->operations[$operation->value] ?? [];
    }
}
