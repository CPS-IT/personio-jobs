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

namespace CPSIT\Typo3PersonioJobs\Tests\Unit\Mapper\Source;

use CPSIT\Typo3PersonioJobs\Exception\MalformedXmlException;
use CPSIT\Typo3PersonioJobs\Mapper\Source\XmlSource;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * XmlSourceTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @covers \CPSIT\Typo3PersonioJobs\Mapper\Source\XmlSource
 */
final class XmlSourceTest extends UnitTestCase
{
    protected XmlSource $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new XmlSource([
            'foo' => [
                'baz' => 1,
            ],
        ]);
    }

    /**
     * @test
     */
    public function fromXmlThrowsExceptionOnMalformedXml(): void
    {
        $this->expectException(MalformedXmlException::class);

        XmlSource::fromXml('');
    }

    /**
     * @test
     */
    public function fromXmlReturnsSourceForGivenXml(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <foo>
        <baz>1</baz>
    </foo>
</root>
XML;

        self::assertEquals($this->subject, XmlSource::fromXml($xml));
    }

    /**
     * @test
     */
    public function asCollectionConvertsGivenNodePathToCollection(): void
    {
        $expected = new XmlSource([
            'foo' => [
                [
                    'baz' => 1,
                ],
            ],
        ]);

        self::assertEquals($expected, $this->subject->asCollection('foo'));
    }
}
