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

namespace CPSIT\Typo3PersonioJobs\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

/**
 * MigratePluginsToContentElementsUpgradeWizard
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
// @todo Switch attribute and base class once support for TYPO3 v13 is dropped
// #[\TYPO3\CMS\Core\Attribute\UpgradeWizard('formConsentMigratePluginToContentElement')]
#[UpgradeWizard('personioJobsMigratePluginsToContentElements')]
final class MigratePluginsToContentElementsUpgradeWizard extends /* \TYPO3\CMS\Core\Upgrades\AbstractListTypeToCTypeUpdate */ AbstractListTypeToCTypeUpdate
{
    public function getTitle(): string
    {
        return 'Migrate EXT:personio_jobs plugins to content elements';
    }

    public function getDescription(): string
    {
        return 'Migrates the EXT:personio_jobs plugins "List" and "Show" from list_types "personiojobs_list" '
            . 'and "personiojobs_show" to CTypes "personiojobs_list" and "personiojobs_show".';
    }

    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'personiojobs_list' => 'personiojobs_list',
            'personiojobs_show' => 'personiojobs_show',
        ];
    }
}
