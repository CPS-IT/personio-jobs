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

namespace CPSIT\Typo3PersonioJobs\Configuration;

use CPSIT\Typo3PersonioJobs\Extension;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Tca
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class Tca
{
    public static function addPlugin(
        string $name,
        string $icon = null,
        string $flexForm = null,
    ): void {
        $pluginSignature = self::buildPluginSignature($name);

        ExtensionUtility::registerPlugin(
            Extension::KEY,
            $name,
            'LLL:EXT:personio_jobs/Resources/Private/Language/locallag_db:plugins.' . lcfirst($name),
            $icon,
        );

        if ($flexForm !== null) {
            ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, $flexForm);

            /* @phpstan-ignore-next-line */
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        }
    }

    private static function buildPluginSignature(string $pluginName): string
    {
        $extensionName = GeneralUtility::underscoredToUpperCamelCase(Extension::KEY);

        return strtolower($extensionName . '_' . $pluginName);
    }
}
