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

namespace CPSIT\Typo3PersonioJobs;

use CPSIT\Typo3PersonioJobs\Controller\JobController;
use CPSIT\Typo3PersonioJobs\Hooks\DataHandlerHook;
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
                JobController::class => ['list'],
            ],
            [],
            ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
        );

        ExtensionUtility::configurePlugin(
            self::KEY,
            'Show',
            [
                JobController::class => ['show'],
            ],
            [],
            ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
        );
    }
}
