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
use CPSIT\Typo3PersonioJobs\Event\AfterJobsMappedEvent;
use CPSIT\Typo3PersonioJobs\Exception\MalformedApiResponseException;
use CPSIT\Typo3PersonioJobs\Utility\FrontendUtility;
use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use EliasHaeussler\ValinorXml\Exception\ArrayPathHasUnexpectedType;
use EliasHaeussler\ValinorXml\Exception\ArrayPathIsInvalid;
use EliasHaeussler\ValinorXml\Exception\XmlIsMalformed;
use EliasHaeussler\ValinorXml\Mapper\Source\XmlSource;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * PersonioApiService
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
#[Autoconfigure(public: true)]
final readonly class PersonioApiService
{
    private Uri $apiUrl;
    private TreeMapper $mapper;

    public function __construct(
        private RequestFactory $requestFactory,
        private EventDispatcherInterface $eventDispatcher,
        ExtensionConfiguration $extensionConfiguration,
    ) {
        $this->apiUrl = $extensionConfiguration->getApiUrl();
        $this->mapper = $this->createMapper();
    }

    /**
     * @return list<Job>
     * @throws ArrayPathHasUnexpectedType
     * @throws ArrayPathIsInvalid
     * @throws XmlIsMalformed
     */
    public function getJobs(?string $language = null): array
    {
        $requestUri = $this->apiUrl->withPath('/xml');

        if ($language !== null) {
            $requestUri = $requestUri->withQuery('language=' . $language);
        }

        $response = $this->requestFactory->request((string)$requestUri);
        $source = XmlSource::fromXmlString((string)$response->getBody())
            ->asCollection('position')
            ->asCollection('position.*.jobDescriptions.jobDescription')
        ;

        try {
            $jobs = $this->mapper->map('list<' . Job::class . '>', $source['position']);

            $this->eventDispatcher->dispatch(new AfterJobsMappedEvent($requestUri, $jobs, $language));

            return $jobs;
        } catch (MappingError $error) {
            $errors = Messages::flattenFromNode($error->node())->errors();

            throw MalformedApiResponseException::forMappingErrors($errors);
        }
    }

    public function getJobUrl(Job $job): Uri
    {
        $language = $this->getLanguageCode();
        $jobUrl = $this->apiUrl->withPath(sprintf('/job/%d', $job->getPersonioId()));

        if ($language !== null) {
            $jobUrl = $jobUrl->withQuery(sprintf('?language=%s', $language));
        }

        return $jobUrl;
    }

    public function getApplyUrl(Job $job): Uri
    {
        return $this->getJobUrl($job)->withFragment('apply');
    }

    private function createMapper(): TreeMapper
    {
        return (new MapperBuilder())
            ->supportDateFormats(\DateTimeInterface::ATOM)
            ->allowSuperfluousKeys()
            ->enableFlexibleCasting()
            ->registerConstructor(
                Job::fromApiResponse(...),
                JobDescription::fromApiResponse(...),
            )
            ->mapper()
        ;
    }

    private function getLanguageCode(): ?string
    {
        $serverRequest = FrontendUtility::getServerRequest();
        $siteLanguage = $serverRequest->getAttribute('language');

        if (!($siteLanguage instanceof SiteLanguage)) {
            return null;
        }

        return $siteLanguage->getLocale()->getLanguageCode();
    }
}
