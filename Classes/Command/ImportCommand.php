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

use CPSIT\Typo3PersonioJobs\Domain\Model\Dto\ImportResult;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Enums\ImportOperation;
use CPSIT\Typo3PersonioJobs\Service\PersonioImportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

    public function __construct(
        private readonly PersonioImportService $personioImportService,
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
            'language',
            'l',
            InputOption::VALUE_REQUIRED,
            'Job language, should be the two-letter ISO 639-1 code of a configured site language',
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $languageId = null;
        /* @phpstan-ignore-next-line */
        $storagePid = max(0, (int)$input->getArgument('storage-pid'));
        /** @var string|null $language */
        $language = $input->getOption('language');
        $force = (bool)$input->getOption('force');
        $noDelete = (bool)$input->getOption('no-delete');
        $noUpdate = (bool)$input->getOption('no-update');
        $dryRun = (bool)$input->getOption('dry-run');

        // Validate parameters
        if ($force && $noUpdate) {
            $this->io->error('The options --force and --no-update cannot be used together.');

            return self::INVALID;
        }

        // Fetch and import jobs from Personio API
        $result = $this->personioImportService->import($storagePid, $language, !$noUpdate, !$noDelete, $force, $dryRun);

        // Show result
        $this->printResult($result);

        if ($dryRun) {
            $this->io->warning('No jobs were imported (dry-run mode).');
            $this->io->writeln('💡 Omit the <comment>--dry-run</comment> option to perform database operations.');
            $this->io->newLine();
        } else {
            $this->io->success('Job import successful.');
        }

        return self::SUCCESS;
    }

    private function printResult(ImportResult $result): void
    {
        $rowsAdded = false;

        $table = new Table($this->io);
        $table->setHeaders(['Job ID', 'Job title', 'Result']);
        $table->setStyle('box');

        foreach ($result->getAllProcessedJobs() as $operation => $jobs) {
            $importOperation = ImportOperation::from($operation);

            // Skip operation without jobs
            if ($jobs === []) {
                continue;
            }

            // Skip operation if verbosity is lower than required
            if ($this->io->getVerbosity() < $importOperation->getVerbosity()) {
                continue;
            }

            // Add table separator between operations
            if ($rowsAdded) {
                $table->addRow(new TableSeparator());
            }

            // Sort jobs by personio id
            usort($jobs, static fn(Job $a, Job $b) => $a->getPersonioId() <=> $b->getPersonioId());

            // Add job to table
            $table->addRows(array_map(static fn(Job $job) => self::decorateTableRow($job, $importOperation), $jobs));

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
