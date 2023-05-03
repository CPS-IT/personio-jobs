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

use CPSIT\Typo3PersonioJobs\Enums\SortingDirection;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * ListDemand
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 *
 * @phpstan-import-type FilterSettings from Filter
 */
class ListDemand implements Demand
{
    protected string $sorting;
    protected SortingDirection $sortingDirection;
    protected Filter $filter;

    final protected function __construct()
    {
        $this->sorting = '';
        $this->sortingDirection = SortingDirection::Ascending;
        $this->filter = Filter::fromArray([]);
    }

    /**
     * @param array{sorting?: string, sortingDirection?: value-of<SortingDirection>, filter?: FilterSettings} $settings
     */
    public static function fromArray(array $settings): static
    {
        $demand = new static();
        $demand->sorting = $settings['sorting'] ?? '';
        $demand->sortingDirection = SortingDirection::fromCaseInsensitive($settings['sortingDirection'] ?? 'asc');
        $demand->filter = Filter::fromArray($settings['filter'] ?? []);

        return $demand;
    }

    public function apply(QueryInterface $query): void
    {
        if ($this->sorting !== '') {
            $query->setOrderings([
                $this->sorting => $this->sortingDirection->value,
            ]);
        }

        $originalConstraint = $query->getConstraint();
        $filterConstraint = $this->filter->buildConstraint($query);

        if ($originalConstraint === null) {
            $query->matching($filterConstraint);
        } else {
            $query->matching(
                $query->logicalAnd($originalConstraint, $filterConstraint),
            );
        }
    }

    public function getSorting(): string
    {
        return $this->sorting;
    }

    public function setSorting(string $sorting): static
    {
        $this->sorting = trim($sorting);
        return $this;
    }

    public function getSortingDirection(): SortingDirection
    {
        return $this->sortingDirection;
    }

    public function setSortingDirection(SortingDirection $sortingDirection): static
    {
        $this->sortingDirection = $sortingDirection;
        return $this;
    }

    public function getFilter(): Filter
    {
        return $this->filter;
    }

    public function setFilter(Filter $filter): static
    {
        $this->filter = $filter;
        return $this;
    }
}
