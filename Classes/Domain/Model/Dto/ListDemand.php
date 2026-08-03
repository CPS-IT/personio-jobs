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

        // Early return if no filter constraints are defined
        if ($filterConstraint === null) {
            return;
        }

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
