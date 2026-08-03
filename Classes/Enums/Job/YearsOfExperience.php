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

namespace CPSIT\Typo3PersonioJobs\Enums\Job;

/**
 * YearsOfExperience
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
enum YearsOfExperience: string
{
    case LessThanOneYear = 'lt-1';
    case OneTwoYears = '1-2';
    case TwoFiveYears = '2-5';
    case FiveSevenYears = '5-7';
    case SevenTenYears = '7-10';
    case TenFifteenYears = '10-15';
    case MoreThanFifteenYears = 'gt-15';
}
