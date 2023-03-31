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

namespace CPSIT\Typo3PersonioJobs\Domain\Factory;

use Brotkrueml\Schema\Model\Type\JobPosting;
use Brotkrueml\Schema\Model\Type\Organization;
use Brotkrueml\Schema\Model\Type\Place;
use Brotkrueml\Schema\Type\TypeFactory;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Enums\Job\EmploymentType;
use CPSIT\Typo3PersonioJobs\Enums\Job\Schedule;
use CPSIT\Typo3PersonioJobs\Enums\Schema\EmploymentType as EmploymentTypeSchema;
use CPSIT\Typo3PersonioJobs\Exception\ExtensionNotLoadedException;
use CPSIT\Typo3PersonioJobs\Service\PersonioService;
use CPSIT\Typo3PersonioJobs\Utility\FrontendUtility;
use DateTime;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * SchemaFactory
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class SchemaFactory
{
    public function __construct(
        protected readonly PersonioService $personioService,
        protected readonly ContentObjectRenderer $contentObjectRenderer,
    ) {
    }

    /**
     * @throws ExtensionNotLoadedException
     */
    public function createJobPosting(Job $job): JobPosting
    {
        // Throw exception if schema extension is not installed
        if (!ExtensionManagementUtility::isLoaded('schema')) {
            throw ExtensionNotLoadedException::create('schema');
        }

        $serverRequest = FrontendUtility::getServerRequest();
        $organizationType = $this->createOrganization($job);
        $placeType = $this->createPlace($job);

        /** @var JobPosting $jobPosting */
        $jobPosting = TypeFactory::createType('JobPosting')
            ->setProperty('datePosted', ($job->getCreateDate() ?? new DateTime())->format('Y-m-d'))
            ->setProperty('employmentType', $this->decorateEmploymentType($job))
            ->setProperty('hiringOrganization', $organizationType)
            ->setProperty('jobLocation', $placeType)
            ->setProperty('occupationalCategory', $job->getOccupationCategory())
            ->setProperty('title', $job->getName())
            ->setProperty('description', $this->decorateDescription($job))
            ->setProperty('url', (string)$serverRequest->getUri())
            ->setProperty('sameAs', (string)$this->personioService->getJobUrl($job))
        ;

        return $jobPosting;
    }

    protected function createOrganization(Job $job): Organization
    {
        /** @var Organization $organization */
        $organization = TypeFactory::createType('Organization')
            ->setProperty('name', $job->getSubcompany())
            ->setProperty('address', $job->getOffice())
        ;

        return $organization;
    }

    protected function createPlace(Job $job): Place
    {
        /** @var Place $place */
        $place = TypeFactory::createType('Place')
            ->setProperty('address', $job->getOffice())
        ;

        return $place;
    }

    /**
     * @return value-of<EmploymentTypeSchema>|list<value-of<EmploymentTypeSchema>>
     * @see https://developers.google.com/search/docs/appearance/structured-data/job-posting#job-posting-definition
     */
    protected function decorateEmploymentType(Job $job): string|array
    {
        $employmentType = EmploymentType::tryFrom($job->getEmploymentType());
        $schedule = Schedule::tryFrom($job->getSchedule());

        if ($employmentType === null && $schedule === null) {
            return EmploymentTypeSchema::Other->value;
        }

        if ($employmentType === EmploymentType::Intern) {
            return EmploymentTypeSchema::Intern->value;
        }

        return match ($schedule) {
            Schedule::FullTime => EmploymentTypeSchema::FullTime->value,
            Schedule::PartTime => EmploymentTypeSchema::PartTime->value,
            Schedule::FullOrPartTime => [EmploymentTypeSchema::FullTime->value, EmploymentTypeSchema::PartTime->value],
            null => EmploymentTypeSchema::Other->value,
        };
    }

    protected function decorateDescription(Job $job): string
    {
        $description = '';

        foreach ($job->getJobDescriptions() as $jobDescription) {
            $rawJobDescription = $jobDescription->getBodytext();
            $description .= $rawJobDescription . ' ';
        }

        if ((new Typo3Version())->getMajorVersion() >= 12) {
            // https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.0/Breaking-96520-EnforceNon-emptyConfigurationInCObjparseFunc.html
            $parsedDescription = $this->contentObjectRenderer->parseFunc($description, null, '< lib.parseFunc_RTE');
        } else {
            /* @phpstan-ignore-next-line */
            $parsedDescription = $this->contentObjectRenderer->parseFunc($description, [], '< lib.parseFunc_RTE');
        }

        return $parsedDescription;
    }
}
