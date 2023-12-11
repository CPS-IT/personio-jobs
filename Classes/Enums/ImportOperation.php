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

namespace CPSIT\Typo3PersonioJobs\Enums;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * ImportOperation
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
enum ImportOperation: string
{
    case Added = 'added';
    case Updated = 'updated';
    case Removed = 'removed';
    case Skipped = 'skipped';

    public function getLabel(): string
    {
        return match ($this) {
            self::Added => '✅ <info>added</info>',
            self::Updated => '🔁 <comment>updated</comment>',
            self::Removed => '🚨 <fg=red>removed</>',
            self::Skipped => '⏩ <fg=cyan>skipped</>',
        };
    }

    /**
     * @return OutputInterface::VERBOSITY_VERBOSE|OutputInterface::VERBOSITY_NORMAL
     */
    public function getVerbosity(): int
    {
        return match ($this) {
            self::Skipped => OutputInterface::VERBOSITY_VERBOSE,
            default => OutputInterface::VERBOSITY_NORMAL,
        };
    }
}
