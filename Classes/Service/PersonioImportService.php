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

namespace CPSIT\Typo3PersonioJobs\Service;

use CPSIT\Typo3PersonioJobs\Cache\CacheManager;
use CPSIT\Typo3PersonioJobs\Domain\Model\Dto\ImportResult;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Domain\Repository\JobRepository;
use CPSIT\Typo3PersonioJobs\Enums\ImportOperation;
use CPSIT\Typo3PersonioJobs\Event\AfterJobsImportedEvent;
use CPSIT\Typo3PersonioJobs\Exception\InvalidArrayPathException;
use CPSIT\Typo3PersonioJobs\Exception\InvalidParametersException;
use CPSIT\Typo3PersonioJobs\Exception\MalformedXmlException;
use CPSIT\Typo3PersonioJobs\Exception\UnavailableLanguageException;
use CPSIT\Typo3PersonioJobs\Helper\SlugHelper;
use Generator;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * PersonioImportService
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class PersonioImportService
{
    private ImportResult $result;

    public function __construct(
        private readonly CacheManager $cacheManager,
        private readonly Connection $connection,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly JobRepository $jobRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly PersonioApiService $personioApiService,
        private readonly SiteFinder $siteFinder,
    ) {
        $this->result = new ImportResult(false);
    }

    /**
     * @param int<0, max> $storagePid
     * @throws InvalidArrayPathException
     * @throws InvalidParametersException
     * @throws MalformedXmlException
     * @throws UnavailableLanguageException
     */
    public function import(
        int $storagePid,
        string $language = null,
        bool $updateExistingJobs = true,
        bool $deleteOrphans = true,
        bool $forceImport = false,
        bool $dryRun = false,
    ): ImportResult {
        $this->result = new ImportResult($dryRun);

        // Validate parameters
        if (!$updateExistingJobs && $forceImport) {
            throw InvalidParametersException::create('$updateExistingJobs', '$forceImport');
        }

        // Resolve language id
        if ($language !== null) {
            $languageId = $this->resolveLanguageId($language, $storagePid)
                ?? throw UnavailableLanguageException::create($language)
            ;
        } else {
            $languageId = null;
        }

        // Fetch jobs from Personio API
        $jobs = $this->personioApiService->getJobs($language);
        $orphans = $deleteOrphans ? $this->jobRepository->findOrphans($jobs, $storagePid, $languageId) : [];

        // Process imported jobs
        foreach ($jobs as $job) {
            $job->setPid($storagePid);

            foreach ($job->getJobDescriptions() as $jobDescription) {
                $jobDescription->setPid($storagePid);
            }

            $this->addOrUpdateJob($job, $storagePid, $languageId, $forceImport, $updateExistingJobs);
        }

        // Remove orphaned jobs
        foreach ($orphans as $orphanedJob) {
            $this->removeJob($orphanedJob);
        }

        // Persist all changes and flush caches
        $this->persistChanges();

        return $this->result;
    }

    /**
     * @param int<-1, max>|null $language
     */
    private function addOrUpdateJob(
        Job $job,
        int $storagePid,
        int $language = null,
        bool $force = false,
        bool $update = true,
    ): void {
        $job->setLanguage($language ?? -1);

        $existingJob = $this->jobRepository->findOneByPersonioId($job->getPersonioId(), $storagePid, $language);

        // Add non-existing job
        if ($existingJob === null) {
            $this->addJob($job);

            return;
        }

        // Update changed job
        if (($update && $existingJob->getContentHash() !== $job->getContentHash()) || $force) {
            $this->replaceJob($existingJob, $job);

            return;
        }

        // Skip unchanged job
        $this->result->add($job, ImportOperation::Skipped);
    }

    private function addJob(Job $job): void
    {
        if (!$this->result->isDryRun()) {
            $this->jobRepository->add($job);
        }

        $this->result->add($job, ImportOperation::Added);
    }

    private function replaceJob(Job $existingJob, Job $importedJob): void
    {
        $this->result->add($importedJob, ImportOperation::Updated);

        // Early return on dry-run
        if ($this->result->isDryRun()) {
            return;
        }

        // Keep existing UID
        $existingUid = $existingJob->getUid();
        if ($existingUid !== null) {
            $importedJob->setUid($existingUid);
        } else {
            $this->persistenceManager->remove($existingJob);
        }

        // Remove existing job descriptions
        foreach ($existingJob->getJobDescriptions() as $jobDescription) {
            $this->persistenceManager->remove($jobDescription);
        }

        // Add updated job
        $this->persistenceManager->add($importedJob);
    }

    private function removeJob(Job $job): void
    {
        if (!$this->result->isDryRun()) {
            $this->persistenceManager->remove($job);
        }

        $this->result->add($job, ImportOperation::Removed);
    }

    private function persistChanges(): void
    {
        // Early return on dry-run
        if ($this->result->isDryRun()) {
            return;
        }

        $this->persistenceManager->persistAll();

        foreach ($this->getModifiedJobs() as $job) {
            $this->updateSlug($job);
        }

        $this->eventDispatcher->dispatch(new AfterJobsImportedEvent($this->result));

        $this->flushCacheTags();
    }

    private function updateSlug(Job $job): void
    {
        // Fetch job record
        $record = $this->connection->select(['*'], Job::TABLE_NAME, ['uid' => $job->getUid()])->fetchAssociative();

        // Early return if record cannot be fetched
        if ($record === false) {
            return;
        }

        // Generate slug for updated record
        $slug = SlugHelper::generateSlug(Job::TABLE_NAME, $record);

        // Update record
        $this->connection->update(Job::TABLE_NAME, ['slug' => $slug], ['uid' => $job->getUid()]);
    }

    private function flushCacheTags(): void
    {
        $flushGeneralCache = false;

        // Flush specific job caches (used in list and detail view)
        foreach ($this->getModifiedJobs() as $job) {
            $this->cacheManager->flushTag($job);

            $flushGeneralCache = true;
        }

        // Flush general job cache (used in list view)
        if ($flushGeneralCache) {
            $this->cacheManager->flushTag();
        }
    }

    /**
     * @return Generator<Job>
     */
    private function getModifiedJobs(): Generator
    {
        foreach ($this->result->getNewJobs() as $newJob) {
            yield $newJob;
        }

        foreach ($this->result->getUpdatedJobs() as $newJob) {
            yield $newJob;
        }

        foreach ($this->result->getRemovedJobs() as $newJob) {
            yield $newJob;
        }
    }

    /**
     * @phpstan-return non-negative-int
     */
    private function resolveLanguageId(string $language, int $storagePid): ?int
    {
        try {
            $site = $this->siteFinder->getSiteByPageId($storagePid);
        } catch (SiteNotFoundException) {
            return null;
        }

        foreach ($site->getLanguages() as $siteLanguage) {
            if ($siteLanguage->getTwoLetterIsoCode() === $language) {
                /** @var non-negative-int $languageId */
                $languageId = $siteLanguage->getLanguageId();

                return $languageId;
            }
        }

        return null;
    }
}
