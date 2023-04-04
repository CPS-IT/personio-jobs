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
 */
class ListDemand implements Demand
{
    protected string $sorting = '';
    protected SortingDirection $sortingDirection = SortingDirection::Ascending;

    final protected function __construct()
    {
    }

    /**
     * @param array{sorting?: string, sortingDirection?: value-of<SortingDirection>} $settings
     */
    public static function fromArray(array $settings): static
    {
        $demand = new static();
        $demand->sorting = $settings['sorting'] ?? '';
        $demand->sortingDirection = SortingDirection::fromCaseInsensitive($settings['sortingDirection'] ?? 'asc');

        return $demand;
    }

    public function apply(QueryInterface $query): void
    {
        if ($this->sorting !== '') {
            $query->setOrderings([
                $this->sorting => $this->sortingDirection->value,
            ]);
        }
    }

    public function getSorting(): string
    {
        return $this->sorting;
    }

    public function setSorting(string $sorting): self
    {
        $this->sorting = trim($sorting);
        return $this;
    }

    public function getSortingDirection(): SortingDirection
    {
        return $this->sortingDirection;
    }

    public function setSortingDirection(SortingDirection $sortingDirection): self
    {
        $this->sortingDirection = $sortingDirection;
        return $this;
    }
}
