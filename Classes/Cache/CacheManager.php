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

namespace CPSIT\Typo3PersonioJobs\Cache;

use CPSIT\Typo3CacheBags\Cache\Bag\CacheBagRegistry;
use CPSIT\Typo3CacheBags\Cache\Bag\PageCacheBag;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * CacheManager
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final readonly class CacheManager
{
    public function __construct(
        #[Autowire(expression: 'service("TYPO3\\\\CMS\\\\Core\\\\Cache\\\\CacheManager").getCache("pages")')]
        private FrontendInterface $pageCache,
        private CacheBagRegistry $cacheBagRegistry,
    ) {}

    public function addTag(?Job $job = null): void
    {
        $this->cacheBagRegistry->add($this->createCacheBag($job));
    }

    public function flushTag(?Job $job = null): void
    {
        $cacheBag = $this->createCacheBag($job);

        foreach ($cacheBag->getCacheTags() as $cacheTag) {
            $this->pageCache->flushByTag($cacheTag);
        }
    }

    private function createCacheBag(?Job $job): PageCacheBag
    {
        $jobId = $job?->getUid();

        if ($jobId === null) {
            return PageCacheBag::forTable(Job::TABLE_NAME);
        }

        return PageCacheBag::forRecord(Job::TABLE_NAME, $jobId);
    }
}
