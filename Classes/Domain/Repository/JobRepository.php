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

namespace CPSIT\Typo3PersonioJobs\Domain\Repository;

use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * JobRepository
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 *
 * @extends Repository<Job>
 */
class JobRepository extends Repository
{
    public function findOneByPersonioId(int $personioId): ?Job
    {
        $query = $this->createQuery();
        $query->matching($query->equals('personioId', $personioId));
        $query->setLimit(1);

        return $query->execute()->getFirst();
    }

    public function findOneByJobDescription(int $jobDescription): ?Job
    {
        $query = $this->createQuery();
        $query->matching($query->contains('jobDescriptions', $jobDescription));
        $query->setLimit(1);

        return $query->execute()->getFirst();
    }

    /**
     * @param list<Job> $existingJobs
     * @return QueryResultInterface<Job>
     */
    public function findOrphans(array $existingJobs, int $storagePid = null): QueryResultInterface
    {
        $query = $this->createQuery();

        if ($storagePid !== null) {
            $query->getQuerySettings()->setStoragePageIds([$storagePid]);
        }

        if ($existingJobs !== []) {
            $query->matching(
                $query->logicalNot(
                    $query->in(
                        'personioId',
                        array_map(static fn (Job $job) => $job->getPersonioId(), $existingJobs),
                    ),
                ),
            );
        }

        return $query->execute();
    }
}
