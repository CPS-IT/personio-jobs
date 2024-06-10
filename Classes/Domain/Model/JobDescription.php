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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * JobDescription
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class JobDescription extends AbstractEntity implements \JsonSerializable
{
    final public const TABLE_NAME = 'tx_personiojobs_domain_model_job_description';

    protected string $header = '';
    protected string $bodytext = '';
    protected ?Job $job = null;

    public static function fromApiResponse(
        string $name,
        string $value,
    ): self {
        $jobDescription = new self();
        $jobDescription->header = trim($name);
        $jobDescription->bodytext = trim($value);

        return $jobDescription;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function setHeader(string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    public function setBodytext(string $bodytext): self
    {
        $this->bodytext = $bodytext;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return array{header: string, bodytext: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'header' => $this->header,
            'bodytext' => $this->bodytext,
        ];
    }
}
