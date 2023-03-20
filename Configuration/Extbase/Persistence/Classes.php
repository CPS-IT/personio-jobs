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

return [
    \CPSIT\Typo3PersonioJobs\Domain\Model\Job::class => [
        'tableName' => \CPSIT\Typo3PersonioJobs\Domain\Model\Job::TABLE_NAME,
    ],
    \CPSIT\Typo3PersonioJobs\Domain\Model\JobDescription::class => [
        'tableName' => \CPSIT\Typo3PersonioJobs\Domain\Model\JobDescription::TABLE_NAME,
    ],
];
