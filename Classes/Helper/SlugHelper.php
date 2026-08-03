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
