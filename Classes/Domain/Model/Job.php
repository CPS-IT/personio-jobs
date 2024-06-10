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

namespace CPSIT\Typo3PersonioJobs\Domain\Model;

use CPSIT\Typo3PersonioJobs\Enums\Job\EmploymentType;
use CPSIT\Typo3PersonioJobs\Enums\Job\Schedule;
use CPSIT\Typo3PersonioJobs\Enums\Job\Seniority;
use CPSIT\Typo3PersonioJobs\Enums\Job\YearsOfExperience;
use JsonSerializable;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Job
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class Job extends AbstractEntity implements JsonSerializable
{
    final public const TABLE_NAME = 'tx_personiojobs_domain_model_job';

    protected int $personioId = 0;
    protected string $subcompany = '';
    protected string $office = '';
    protected string $department = '';
    protected string $recruitingCategory = '';
    protected string $name = '';

    /**
     * @Extbase\ORM\Cascade("remove")
     * @var ObjectStorage<JobDescription>
     */
    protected ObjectStorage $jobDescriptions;
    protected string $employmentType = '';
    protected string $seniority = '';
    protected string $schedule = '';
    protected string $yearsOfExperience = '';
    protected string $keywords = '';
    protected string $occupation = '';
    protected string $occupationCategory = '';
    protected ?\DateTime $createDate = null;
    protected string $contentHash = '';

    public function __construct()
    {
        $this->initializeStorageObjects();
    }

    /**
     * @param array{jobDescription: list<JobDescription>} $jobDescriptions
     */
    public static function fromApiResponse(
        int $id,
        ?string $subcompany,
        ?string $office,
        ?string $department,
        ?string $recruitingCategory,
        string $name,
        array $jobDescriptions,
        EmploymentType $employmentType,
        Seniority $seniority,
        Schedule $schedule,
        YearsOfExperience $yearsOfExperience,
        ?string $keywords,
        ?string $occupation,
        ?string $occupationCategory,
        \DateTime $createdAt,
    ): self {
        $job = new self();
        $job->personioId = $id;
        $job->subcompany = (string)$subcompany;
        $job->office = (string)$office;
        $job->department = (string)$department;
        $job->recruitingCategory = (string)$recruitingCategory;
        $job->name = $name;

        foreach ($jobDescriptions['jobDescription'] as $jobDescription) {
            $jobDescription->setJob($job);
            $job->addJobDescription($jobDescription);
        }

        $job->employmentType = $employmentType->value;
        $job->seniority = $seniority->value;
        $job->schedule = $schedule->value;
        $job->yearsOfExperience = $yearsOfExperience->value;
        $job->keywords = (string)$keywords;
        $job->occupation = (string)$occupation;
        $job->occupationCategory = (string)$occupationCategory;
        $job->createDate = $createdAt;
        $job->contentHash = $job->calculateHash();

        return $job;
    }

    protected function initializeStorageObjects(): void
    {
        $this->jobDescriptions = new ObjectStorage();
    }

    /**
     * @param int<1, max> $uid
     */
    public function setUid(int $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function getPersonioId(): int
    {
        return $this->personioId;
    }

    public function setPersonioId(int $personioId): self
    {
        $this->personioId = $personioId;

        return $this;
    }

    public function getSubcompany(): string
    {
        return $this->subcompany;
    }

    public function setSubcompany(string $subcompany): self
    {
        $this->subcompany = $subcompany;

        return $this;
    }

    public function getOffice(): string
    {
        return $this->office;
    }

    public function setOffice(string $office): self
    {
        $this->office = $office;

        return $this;
    }

    public function getDepartment(): string
    {
        return $this->department;
    }

    public function setDepartment(string $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getRecruitingCategory(): string
    {
        return $this->recruitingCategory;
    }

    public function setRecruitingCategory(string $recruitingCategory): self
    {
        $this->recruitingCategory = $recruitingCategory;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ObjectStorage<JobDescription>
     */
    public function getJobDescriptions(): ObjectStorage
    {
        return $this->jobDescriptions;
    }

    /**
     * @param ObjectStorage<JobDescription> $jobDescriptions
     */
    public function setJobDescriptions(ObjectStorage $jobDescriptions): self
    {
        $this->jobDescriptions = $jobDescriptions;

        return $this;
    }

    public function addJobDescription(JobDescription $jobDescription): self
    {
        $this->jobDescriptions->attach($jobDescription);

        return $this;
    }

    public function removeJobDescription(JobDescription $jobDescription): self
    {
        $this->jobDescriptions->detach($jobDescription);

        return $this;
    }

    public function getEmploymentType(): string
    {
        return $this->employmentType;
    }

    public function setEmploymentType(string $employmentType): self
    {
        $this->employmentType = $employmentType;

        return $this;
    }

    public function getSeniority(): string
    {
        return $this->seniority;
    }

    public function setSeniority(string $seniority): self
    {
        $this->seniority = $seniority;

        return $this;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function setSchedule(string $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getYearsOfExperience(): string
    {
        return $this->yearsOfExperience;
    }

    public function setYearsOfExperience(string $yearsOfExperience): self
    {
        $this->yearsOfExperience = $yearsOfExperience;

        return $this;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getOccupation(): string
    {
        return $this->occupation;
    }

    public function setOccupation(string $occupation): self
    {
        $this->occupation = $occupation;

        return $this;
    }

    public function getOccupationCategory(): string
    {
        return $this->occupationCategory;
    }

    public function setOccupationCategory(string $occupationCategory): self
    {
        $this->occupationCategory = $occupationCategory;

        return $this;
    }

    public function getCreateDate(): ?\DateTime
    {
        return $this->createDate;
    }

    public function setCreateDate(?\DateTime $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getContentHash(): string
    {
        return $this->contentHash;
    }

    public function recalculateContentHash(): self
    {
        $this->contentHash = $this->calculateHash();

        return $this;
    }

    /**
     * @param int<-1, max> $language
     */
    public function setLanguage(int $language): self
    {
        $this->_languageUid = $language;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'personioId' => $this->personioId,
            'subcompany' => $this->subcompany,
            'office' => $this->office,
            'department' => $this->department,
            'recruitingCategory' => $this->recruitingCategory,
            'name' => $this->name,
            'jobDescriptions' => $this->jobDescriptions->toArray(),
            'employmentType' => $this->employmentType,
            'seniority' => $this->seniority,
            'schedule' => $this->schedule,
            'yearsOfExperience' => $this->yearsOfExperience,
            'keywords' => $this->keywords,
            'occupation' => $this->occupation,
            'occupationCategory' => $this->occupationCategory,
            'createDate' => $this->createDate?->getTimestamp(),
        ];
    }

    private function calculateHash(): string
    {
        return sha1(json_encode($this, JSON_THROW_ON_ERROR));
    }
}
