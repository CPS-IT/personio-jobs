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

namespace CPSIT\Typo3PersonioJobs\Exception;

use CuyZ\Valinor\Mapper\MappingError;

/**
 * MalformedApiResponseException
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class MalformedApiResponseException extends \RuntimeException
{
    public static function forMappingError(MappingError $error): self
    {
        $message = 'Received malformed API response:';

        foreach ($error->messages() as $errorMessage) {
            $message .= PHP_EOL . sprintf(' - %s: %s', $errorMessage->path(), $errorMessage->toString());
        }

        return new self($message, 1677234223);
    }
}
