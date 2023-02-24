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

namespace CPSIT\Typo3PersonioJobs\Service;

use CPSIT\Typo3PersonioJobs\Configuration\ExtensionConfiguration;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Domain\Model\JobDescription;
use CPSIT\Typo3PersonioJobs\Exception\MalformedApiResponseException;
use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use DateTimeInterface;
use Mtownsend\XmlToArray\XmlToArray;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;

/**
 * PersonioService
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class PersonioService
{
    private readonly Uri $apiUrl;
    private readonly TreeMapper $mapper;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        ExtensionConfiguration $extensionConfiguration,
    ) {
        $this->apiUrl = $extensionConfiguration->getApiUrl();
        $this->mapper = $this->createMapper();
    }

    /**
     * @return list<Job>
     * @throws MalformedApiResponseException
     */
    public function getJobs(): array
    {
        $response = $this->requestFactory->request((string)$this->apiUrl->withPath('/xml'));
        $array = XmlToArray::convert((string)$response->getBody());
        $source = Source::array($array['position'] ?? [])->map([
            '*.createdAt' => 'createDate',
            '*.jobDescriptions.jobDescription.*.name' => 'header',
            '*.jobDescriptions.jobDescription.*.value' => 'bodytext',
        ]);

        try {
            return $this->mapper->map('list<' . Job::class . '>', $source);
        } catch (MappingError $error) {
            $errors = Messages::flattenFromNode($error->node())->errors();

            throw MalformedApiResponseException::forMappingErrors($errors);
        }
    }

    private function createMapper(): TreeMapper
    {
        return (new MapperBuilder())
            ->supportDateFormats(DateTimeInterface::ATOM)
            ->allowSuperfluousKeys()
            ->enableFlexibleCasting()
            ->registerConstructor(
                Job::fromApiResponse(...),
                JobDescription::fromApiResponse(...),
            )
            ->mapper()
        ;
    }
}
