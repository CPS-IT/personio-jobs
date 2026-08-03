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

namespace CPSIT\Typo3PersonioJobs\Domain\Repository;

use CPSIT\Typo3PersonioJobs\Domain\Model\Dto\Demand;
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
    /**
     * @return QueryResultInterface<int, Job>
     */
    public function findByDemand(Demand $demand): QueryResultInterface
    {
        $query = $this->createQuery();
        $demand->apply($query);

        return $query->execute();
    }

    public function findOneByPersonioId(int $personioId, ?int $storagePid = null): ?Job
    {
        $query = $this->createQuery();

        if ($storagePid !== null) {
            $query->getQuerySettings()->setStoragePageIds([$storagePid]);
        }

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
     * @return QueryResultInterface<int, Job>
     */
    public function findOrphans(array $existingJobs, ?int $storagePid = null): QueryResultInterface
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
                        array_map(static fn(Job $job) => $job->getPersonioId(), $existingJobs),
                    ),
                ),
            );
        }

        return $query->execute();
    }
}
