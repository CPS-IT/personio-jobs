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

namespace CPSIT\Typo3PersonioJobs\Domain\Model\Dto;

use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Filter
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 *
 * @phpstan-type FilterSettings array{
 *     subcompanyInclude?: string,
 *     subcompanyExclude?: string,
 * }
 */
class Filter
{
    /**
     * @var list<string>
     */
    protected array $subcompanyInclude;

    /**
     * @var list<string>
     */
    protected array $subcompanyExclude;

    final protected function __construct()
    {
        $this->subcompanyInclude = [];
        $this->subcompanyExclude = [];
    }

    /**
     * @phpstan-param FilterSettings $settings
     */
    public static function fromArray(array $settings): static
    {
        $filter = new static();
        $filter->subcompanyInclude = GeneralUtility::trimExplode(',', $settings['subcompanyInclude'] ?? '', true);
        $filter->subcompanyExclude = GeneralUtility::trimExplode(',', $settings['subcompanyExclude'] ?? '', true);

        return $filter;
    }

    /**
     * @param QueryInterface<Job> $query
     */
    public function buildConstraint(QueryInterface $query): ?ConstraintInterface
    {
        $constraints = [];

        if ($this->subcompanyInclude !== []) {
            $constraints[] = $query->in('subcompany', $this->subcompanyInclude);
        }

        if ($this->subcompanyExclude !== []) {
            $constraints[] = $query->logicalNot(
                $query->in('subcompany', $this->subcompanyExclude),
            );
        }

        // Early return if no constraints are defined
        if ($constraints === []) {
            return null;
        }

        return $query->logicalAnd(...$constraints);
    }

    /**
     * @return list<string>
     */
    public function getSubcompanyInclude(): array
    {
        return $this->subcompanyInclude;
    }

    /**
     * @param list<string> $subcompanyInclude
     */
    public function setSubcompanyInclude(array $subcompanyInclude): static
    {
        $this->subcompanyInclude = $subcompanyInclude;
        return $this;
    }

    /**
     * @return list<string>
     */
    public function getSubcompanyExclude(): array
    {
        return $this->subcompanyExclude;
    }

    /**
     * @param list<string> $subcompanyExclude
     */
    public function setSubcompanyExclude(array $subcompanyExclude): static
    {
        $this->subcompanyExclude = $subcompanyExclude;
        return $this;
    }
}
