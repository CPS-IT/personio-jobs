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

namespace CPSIT\Typo3PersonioJobs\Pagination;

use GeorgRinger\NumberedPagination\NumberedPagination;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Pagination\SlidingWindowPagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * PaginationFactory
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class PaginationFactory
{
    public function get(PaginatorInterface $paginator, int $maximumNumberOfLinks = 5): PaginationInterface
    {
        if (class_exists(NumberedPagination::class)) {
            return GeneralUtility::makeInstance(NumberedPagination::class, $paginator, $maximumNumberOfLinks);
        }

        if (class_exists(SlidingWindowPagination::class)) {
            return new SlidingWindowPagination($paginator, $maximumNumberOfLinks);
        }

        return new SimplePagination($paginator);
    }
}
