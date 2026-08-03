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

namespace CPSIT\Typo3PersonioJobs\Event;

use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use TYPO3\CMS\Core\Http\Uri;

/**
 * AfterJobsMappedEvent
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final readonly class AfterJobsMappedEvent
{
    /**
     * @param list<Job> $jobs
     */
    public function __construct(
        private Uri $requestUri,
        private array $jobs,
    ) {}

    public function getRequestUri(): Uri
    {
        return $this->requestUri;
    }

    /**
     * @return list<Job>
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }
}
