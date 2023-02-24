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

namespace CPSIT\Typo3PersonioJobs;

use CPSIT\Typo3PersonioJobs\Controller\JobController;
use CPSIT\Typo3PersonioJobs\Hooks\DataHandlerHook;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Extension
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class Extension
{
    public const KEY = 'personio_jobs';

    /**
     * FOR USE IN ext_localconf.php ONLY.
     */
    public static function registerHooks(): void
    {
        /* @phpstan-ignore-next-line */
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
            = DataHandlerHook::class;
    }

    /**
     * FOR USE IN ext_localconf.php ONLY.
     */
    public static function registerPlugins(): void
    {
        ExtensionUtility::configurePlugin(
            self::KEY,
            'List',
            [
                JobController::class => 'list',
            ],
        );

        ExtensionUtility::configurePlugin(
            self::KEY,
            'Show',
            [
                JobController::class => 'show',
            ],
            [
                JobController::class => 'show',
            ],
        );
    }

    /**
     * FOR USE IN ext_localconf.php ONLY.
     */
    public static function registerTSconfig(): void
    {
        ExtensionManagementUtility::addPageTSConfig(
            '@import "EXT:personio_jobs/Configuration/TSconfig/Page.tsconfig"',
        );
    }
}
