<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "personio_jobs".
 *
 * Copyright (C) 2026 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace CPSIT\Typo3PersonioJobs\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

/**
 * MigratePluginsToContentElementsUpgradeWizard
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
#[UpgradeWizard('personioJobsMigratePluginsToContentElements')]
final class MigratePluginsToContentElementsUpgradeWizard extends AbstractListTypeToCTypeUpdate
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
