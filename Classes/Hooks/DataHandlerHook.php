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

namespace CPSIT\Typo3PersonioJobs\Hooks;

use CPSIT\Typo3PersonioJobs\Cache\CacheManager;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Domain\Model\JobDescription;
use CPSIT\Typo3PersonioJobs\Domain\Repository\JobRepository;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * DataHandlerHook
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class DataHandlerHook
{
    public function __construct(
        private readonly CacheManager $cacheManager,
        private readonly JobRepository $jobRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
    ) {}

    /**
     * @param array<string, mixed> $fieldArray
     */
    public function processDatamap_afterDatabaseOperations(
        string $status,
        string $table,
        string|int $id,
        array $fieldArray,
        DataHandler $dataHandler,
    ): void {
        $uid = (int)(is_numeric($id) ? $id : $dataHandler->substNEWwithIDs[$id]);

        // Fetch job
        if ($table === JobDescription::TABLE_NAME) {
            $job = $this->jobRepository->findOneByJobDescription($uid);
        } elseif ($table === Job::TABLE_NAME) {
            $job = $this->jobRepository->findByUid($uid);
        } else {
            // Early return if current table is not supported
            return;
        }

        // Early return if job cannot be found
        if ($job === null) {
            return;
        }

        // Recalculate content hash
        $originalHash = $job->getContentHash();
        $job->recalculateContentHash();
        $updatedHash = $job->getContentHash();

        // Update job if hash changed
        if ($originalHash !== $updatedHash) {
            $this->persistenceManager->update($job);
            $this->cacheManager->flushTag($job);
            $this->cacheManager->flushTag();
        }
    }

    public function __destruct()
    {
        $this->persistenceManager->persistAll();
    }
}
