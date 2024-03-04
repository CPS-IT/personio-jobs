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

use CPSIT\Typo3PersonioJobs\Domain\Model\Dto\Demand;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
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
     * @return QueryResultInterface<Job>
     */
    public function findByDemand(Demand $demand): QueryResultInterface
    {
        $query = $this->createQuery();
        $demand->apply($query);

        return $query->execute();
    }

    /**
     * @param int<-1, max>|null $language
     */
    public function findOneByPersonioId(int $personioId, int $storagePid = null, int $language = null): ?Job
    {
        $query = $this->createQuery();

        if ($storagePid !== null) {
            $query->getQuerySettings()->setStoragePageIds([$storagePid]);
        }

        $query->matching($query->equals('personioId', $personioId));
        $query->setLimit(1);

        if ($language !== null) {
            $this->setLanguageForQuery($query, $language);
        }

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
     * @param int<-1, max>|null $language
     * @return QueryResultInterface<Job>
     */
    public function findOrphans(array $existingJobs, int $storagePid = null, int $language = null): QueryResultInterface
    {
        $query = $this->createQuery();

        if ($storagePid !== null) {
            $query->getQuerySettings()->setStoragePageIds([$storagePid]);
        }

        if ($language !== null) {
            $this->setLanguageForQuery($query, $language);
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

    /**
     * @param QueryInterface<Job> $query
     * @param int<-1, max> $language
     */
    protected function setLanguageForQuery(QueryInterface $query, int $language): void
    {
        $querySettings = $query->getQuerySettings();

        // https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.0/Breaking-97926-ExtbaseQuerySettingsMethodsRemoved.html
        if ((new Typo3Version())->getMajorVersion() >= 12) {
            $languageAspect = new LanguageAspect($language, overlayType: LanguageAspect::OVERLAYS_MIXED);
            $querySettings->setLanguageAspect($languageAspect);
        } else {
            /* @phpstan-ignore-next-line */
            $querySettings->setLanguageUid($language);
        }
    }
}
