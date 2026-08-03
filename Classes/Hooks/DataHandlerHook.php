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

namespace CPSIT\Typo3PersonioJobs\Hooks;

use CPSIT\Typo3PersonioJobs\Cache\CacheManager;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Domain\Model\JobDescription;
use CPSIT\Typo3PersonioJobs\Domain\Repository\JobRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * DataHandlerHook
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
#[Autoconfigure(public: true)]
final readonly class DataHandlerHook
{
    public function __construct(
        private CacheManager $cacheManager,
        private JobRepository $jobRepository,
        private PersistenceManagerInterface $persistenceManager,
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
