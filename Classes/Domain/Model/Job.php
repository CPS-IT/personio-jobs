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
    protected string $name = '';
    protected string $subcompany = '';
    protected string $office = '';
    protected string $department = '';

    /**
     * @Extbase\ORM\Cascade("remove")
     * @var ObjectStorage<JobDescription>
     */
    protected ObjectStorage $jobDescriptions;
    protected string $recruitingCategory = '';
    protected string $keywords = '';
    protected ?\DateTime $createDate = null;
    protected string $contentHash = '';

    public function __construct()
    {
        $this->initializeStorageObjects();
    }

    /**
     * @param array{id: int, name: string, subcompany: string|null, office: string|null, department: string|null, jobDescriptions: array{jobDescription: list<JobDescription>}, recruitingCategory: string|null, keywords: string|null, createDate: \DateTime} $apiResponse
     */
    public static function fromApiResponse(array $apiResponse): self
    {
        $job = new self();
        $job->personioId = $apiResponse['id'];
        $job->name = $apiResponse['name'];
        $job->subcompany = (string)$apiResponse['subcompany'];
        $job->office = (string)$apiResponse['office'];
        $job->department = (string)$apiResponse['department'];

        foreach ($apiResponse['jobDescriptions']['jobDescription'] as $jobDescription) {
            $jobDescription->setJob($job);
            $job->addJobDescription($jobDescription);
        }

        $job->recruitingCategory = (string)$apiResponse['recruitingCategory'];
        $job->keywords = (string)$apiResponse['keywords'];
        $job->createDate = $apiResponse['createDate'];
        $job->contentHash = $job->calculateHash();

        return $job;
    }

    protected function initializeStorageObjects(): void
    {
        $this->jobDescriptions = new ObjectStorage();
    }

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getRecruitingCategory(): string
    {
        return $this->recruitingCategory;
    }

    public function setRecruitingCategory(string $recruitingCategory): self
    {
        $this->recruitingCategory = $recruitingCategory;

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
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'personioId' => $this->personioId,
            'name' => $this->name,
            'subcompany' => $this->subcompany,
            'office' => $this->office,
            'department' => $this->department,
            'jobDescriptions' => $this->jobDescriptions->toArray(),
            'recruitingCategory' => $this->recruitingCategory,
            'keywords' => $this->keywords,
            'createDate' => $this->createDate?->getTimestamp(),
        ];
    }

    private function calculateHash(): string
    {
        return sha1(json_encode($this, JSON_THROW_ON_ERROR));
    }
}
