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

namespace CPSIT\Typo3PersonioJobs\Helper;

use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;
use TYPO3\CMS\Core\DataHandling\SlugHelper as CoreSlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SlugHelper
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class SlugHelper
{
    /**
     * @param array<string, mixed> $record
     */
    public static function generateSlug(string $tableName, array $record, string $slugFieldName = 'slug'): ?string
    {
        $fieldConfiguration = $GLOBALS['TCA'][$tableName]['columns'][$slugFieldName]['config'] ?? null;

        // Early return if slug field is not configured
        if (!is_array($fieldConfiguration)) {
            return null;
        }

        // Get field configuration
        $evalInfo = GeneralUtility::trimExplode(',', (string)($fieldConfiguration['eval'] ?? ''), true);

        // Initialize Slug helper
        $slugHelper = GeneralUtility::makeInstance(CoreSlugHelper::class, $tableName, $slugFieldName, $fieldConfiguration);

        // Generate slug
        $slug = $slugHelper->generate($record, (int)$record['pid']);
        $state = RecordStateFactory::forName($tableName)->fromArray($record, (int)$record['pid'], (int)$record['uid']);

        // Assure slug is unique as configured
        if (in_array('uniqueInSite', $evalInfo, true)) {
            $slug = $slugHelper->buildSlugForUniqueInSite($slug, $state);
        } elseif (in_array('uniqueInPid', $evalInfo, true)) {
            $slug = $slugHelper->buildSlugForUniqueInPid($slug, $state);
        }

        return $slug;
    }
}
