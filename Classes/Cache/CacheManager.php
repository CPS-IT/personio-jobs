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

use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
    ) {}

    public function addTag(?Job $job = null): void
    {
        $this->getTypoScriptFrontendController()?->addCacheTags([$this->buildCacheTag($job)]);
    }

    public function flushTag(?Job $job = null): void
    {
        $this->pageCache->flushByTag($this->buildCacheTag($job));
    }

    private function buildCacheTag(?Job $job = null): string
    {
        $cacheTag = 'tx_personiojobs';

        if ($job !== null) {
            $cacheTag .= '_' . $job->getUid();
        }

        return $cacheTag;
    }

    private function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        $tsfe = $GLOBALS['TSFE'] ?? null;

        if ($tsfe instanceof TypoScriptFrontendController) {
            return $tsfe;
        }

        return null;
    }
}
