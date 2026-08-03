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
