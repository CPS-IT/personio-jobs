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

use Brotkrueml\Schema\Model\Type\JobPosting;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;

/**
 * EnrichJobPostingSchemaEvent
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final readonly class EnrichJobPostingSchemaEvent
{
    public function __construct(
        private Job $job,
        private JobPosting $jobPosting,
    ) {}

    public function getJob(): Job
    {
        return $this->job;
    }

    public function getJobPosting(): JobPosting
    {
        return $this->jobPosting;
    }
}
