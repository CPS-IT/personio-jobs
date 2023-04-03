<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "personio_jobs".
 *
 * Copyright (C) 2020 Juliane Wundermann <j.wundermann@familie-redlich.de>
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

namespace CPSIT\Typo3PersonioJobs\Command;

use CPSIT\Typo3PersonioJobs\Cache\CacheManager;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Domain\Repository\JobRepository;
use CPSIT\Typo3PersonioJobs\Enums\ImportOperation;
use CPSIT\Typo3PersonioJobs\Event\AfterJobsImportedEvent;
use CPSIT\Typo3PersonioJobs\Helper\SlugHelper;
use CPSIT\Typo3PersonioJobs\Service\PersonioService;
use Generator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * ImportCommand
 *
 * @author Juliane Wundermann <j.wundermann@familie-redlich.de>
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ImportCommand extends Command
{
    private SymfonyStyle $io;
    private bool $dryRun = false;

    /**
     * @var array<value-of<ImportOperation>, list<Job>>
     */
    private array $result = [];

    public function __construct(
        private readonly PersonioService $personioService,
        private readonly JobRepository $jobRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly CacheManager $cacheManager,
        private readonly Connection $connection,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct('personio-jobs:import');
    }

    protected function configure(): void
    {
        $this->setDescription('Imports a Personio job feed and stores them in the local database');

        $this->addArgument(
            'storage-pid',
            InputArgument::REQUIRED,
            'Storage pid of imported jobs',
        );
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Enforce re-import of unchanged jobs',
        );
        $this->addOption(
            'no-delete',
            null,
            InputOption::VALUE_NONE,
            'Do not delete orphaned jobs',
        );
        $this->addOption(
            'no-update',
            null,
            InputOption::VALUE_NONE,
            'Do not update imported jobs that have been changed',
        );
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Do not perform database operations',
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->dryRun = (bool)$input->getOption('dry-run');
        $this->result = [];
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /* @phpstan-ignore-next-line */
        $storagePid = max(0, (int)$input->getArgument('storage-pid'));
        $force = (bool)$input->getOption('force');
        $noDelete = (bool)$input->getOption('no-delete');
        $noUpdate = (bool)$input->getOption('no-update');

        // Validate parameters
        if ($force && $noUpdate) {
            $this->io->error('The options --force and --no-update cannot be used together.');

            return self::INVALID;
        }

        // Fetch jobs from Personio API
        $jobs = $this->personioService->getJobs();
        $orphans = $noDelete ? [] : $this->jobRepository->findOrphans($jobs, $storagePid);

        // Process imported jobs
        foreach ($jobs as $job) {
            $job->setPid($storagePid);

            foreach ($job->getJobDescriptions() as $jobDescription) {
                $jobDescription->setPid($storagePid);
            }

            $this->addOrUpdateJob($job, $storagePid, $force, !$noUpdate);
        }

        // Remove orphaned jobs
        foreach ($orphans as $orphanedJob) {
            $this->removeJob($orphanedJob);
        }

        // Persist all changes and flush caches
        $this->persistChanges();

        // Show result
        $this->printResult($output);

        if ($this->dryRun) {
            $this->io->warning('No jobs were imported (dry-run mode).');
            $this->io->writeln('💡 Omit the <comment>--dry-run</comment> option to perform database operations.');
            $this->io->newLine();
        } else {
            $this->io->success('Job import successful.');
        }

        return self::SUCCESS;
    }

    private function addOrUpdateJob(Job $job, int $storagePid, bool $force = false, bool $update = true): void
    {
        $existingJob = $this->jobRepository->findOneByPersonioId($job->getPersonioId(), $storagePid);

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
        $this->addResult($job, ImportOperation::Skipped);
    }

    private function addJob(Job $job): void
    {
        if (!$this->dryRun) {
            $this->jobRepository->add($job);
        }

        $this->addResult($job, ImportOperation::Added);
    }

    private function replaceJob(Job $existingJob, Job $importedJob): void
    {
        $this->addResult($importedJob, ImportOperation::Updated);

        // Early return on dry-run
        if ($this->dryRun) {
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
        if (!$this->dryRun) {
            $this->persistenceManager->remove($job);
        }

        $this->addResult($job, ImportOperation::Removed);
    }

    private function persistChanges(): void
    {
        // Early return on dry-run
        if ($this->dryRun) {
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

    private function addResult(Job $job, ImportOperation $operation): void
    {
        if (!is_array($this->result[$operation->value] ?? null)) {
            $this->result[$operation->value] = [];
        }

        $this->result[$operation->value][] = $job;
    }

    /**
     * @return Generator<Job>
     */
    private function getModifiedJobs(): Generator
    {
        $operations = [
            ImportOperation::Added,
            ImportOperation::Removed,
            ImportOperation::Updated,
        ];

        foreach ($operations as $operation) {
            foreach ($this->result[$operation->value] ?? [] as $job) {
                yield $job;
            }
        }
    }

    private function printResult(OutputInterface $output): void
    {
        $rowsAdded = false;

        $table = new Table($output);
        $table->setHeaders(['Job ID', 'Job title', 'Result']);
        $table->setStyle('box');

        foreach ($this->result as $operation => $jobs) {
            $importOperation = ImportOperation::from($operation);

            // Skip operation without jobs
            if ($jobs === []) {
                continue;
            }

            // Skip operation if verbosity is lower than required
            if ($output->getVerbosity() < $importOperation->getVerbosity()) {
                continue;
            }

            // Add table separator between operations
            if ($rowsAdded) {
                $table->addRow(new TableSeparator());
            }

            // Sort jobs by personio id
            usort($jobs, static fn (Job $a, Job $b) => $a->getPersonioId() <=> $b->getPersonioId());

            // Add job to table
            $table->addRows(array_map(static fn (Job $job) => self::decorateTableRow($job, $importOperation), $jobs));

            $rowsAdded = true;
        }

        if ($rowsAdded) {
            $table->render();
        }
    }

    /**
     * @return array{int, string, string}
     */
    private static function decorateTableRow(Job $job, ImportOperation $operation): array
    {
        return [$job->getPersonioId(), $job->getName(), $operation->getLabel()];
    }
}
