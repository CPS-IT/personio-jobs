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

namespace CPSIT\Typo3PersonioJobs\Mapper\Source;

use ArrayObject;
use CPSIT\Typo3PersonioJobs\Exception\InvalidArrayPathException;
use CPSIT\Typo3PersonioJobs\Exception\MalformedXmlException;
use CPSIT\Typo3PersonioJobs\Utility\ArrayUtility;
use Mtownsend\XmlToArray\XmlToArray;
use Throwable;

/**
 * XmlSource
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 *
 * @extends ArrayObject<string, mixed>
 */
final class XmlSource extends ArrayObject
{
    /**
     * @param array<string, mixed> $source
     */
    public function __construct(array $source)
    {
        parent::__construct($source);
    }

    /**
     * @throws MalformedXmlException
     */
    public static function fromXml(string $xml): self
    {
        set_error_handler(static fn(int $code, string $message) => self::handleParseError($xml, $message));

        try {
            $source = XmlToArray::convert($xml);
        } catch (Throwable $exception) {
            self::handleParseError($xml, $exception->getMessage());
        } finally {
            restore_error_handler();
        }

        return new self($source);
    }

    /**
     * @throws InvalidArrayPathException
     */
    public function asCollection(string $node): self
    {
        $clone = clone $this;
        $clone->exchangeArray(
            ArrayUtility::convertToCollection((array)$clone, $node),
        );

        return $clone;
    }

    /**
     * @throws MalformedXmlException
     */
    private static function handleParseError(string $xml, string $message): never
    {
        throw MalformedXmlException::create($xml, $message);
    }
}
