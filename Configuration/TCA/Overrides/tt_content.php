<?php

defined('TYPO3') or die();

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

(static function() {
    $typo3Version = new \TYPO3\CMS\Core\Information\Typo3Version();

    // @todo Remove once support for TYPO3 v11 is dropped
    if ($typo3Version < 12) {
        $suffix = '.v11';
    } else {
        $suffix = '';
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
        'tt_content',
        'list_type',
        'personio',
        'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:groups.personio',
    );

    \CPSIT\Typo3PersonioJobs\Configuration\Tca::addPlugin(
        'List',
        'tx-personio-jobs-plugin-list',
        'FILE:EXT:personio_jobs/Configuration/FlexForms/List' . $suffix . '.xml',
    );

    \CPSIT\Typo3PersonioJobs\Configuration\Tca::addPlugin(
        'Show',
        'tx-personio-jobs-plugin-show',
        'FILE:EXT:personio_jobs/Configuration/FlexForms/Show' . $suffix . '.xml',
    );
})();
