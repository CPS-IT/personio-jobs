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

namespace CPSIT\Typo3PersonioJobs\Tests\Unit\Service;

use CPSIT\Typo3PersonioJobs\Configuration\ExtensionConfiguration;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Domain\Model\JobDescription;
use CPSIT\Typo3PersonioJobs\Enums\Job\EmploymentType;
use CPSIT\Typo3PersonioJobs\Enums\Job\Schedule;
use CPSIT\Typo3PersonioJobs\Enums\Job\Seniority;
use CPSIT\Typo3PersonioJobs\Enums\Job\YearsOfExperience;
use CPSIT\Typo3PersonioJobs\Exception\MalformedApiResponseException;
use CPSIT\Typo3PersonioJobs\Exception\MalformedXmlException;
use CPSIT\Typo3PersonioJobs\Service\PersonioApiService;
use CPSIT\Typo3PersonioJobs\Tests\Unit\Fixtures\Classes\DummyExtensionConfiguration;
use CPSIT\Typo3PersonioJobs\Tests\Unit\Fixtures\Classes\DummyRequestFactory;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * PersonioApiServiceTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @covers \CPSIT\Typo3PersonioJobs\Service\PersonioApiService
 */
final class PersonioApiServiceTest extends UnitTestCase
{
    protected DummyExtensionConfiguration $extensionConfiguration;
    protected DummyRequestFactory $requestFactory;
    protected StreamFactory $streamFactory;
    protected PersonioApiService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extensionConfiguration = new DummyExtensionConfiguration();
        $this->extensionConfiguration->config['apiUrl'] = 'https://testing.jobs.personio.local';

        $this->requestFactory = new DummyRequestFactory();
        $this->streamFactory = new StreamFactory();
        $this->subject = new PersonioApiService(
            $this->requestFactory,
            new EventDispatcher(),
            new ExtensionConfiguration($this->extensionConfiguration),
        );
    }

    /**
     * @test
     */
    public function getJobsThrowsExceptionOnMalformedXml(): void
    {
        $stream = $this->streamFactory->createStreamFromFile(dirname(__DIR__) . '/Fixtures/Files/api-response-malformed.xml');

        $this->requestFactory->response = new Response($stream);

        $this->expectException(MalformedXmlException::class);
        $this->expectExceptionCode(1692170602);

        $this->subject->getJobs();
    }

    /**
     * @test
     */
    public function getJobsThrowsExceptionOnInvalidApiResponse(): void
    {
        $stream = $this->streamFactory->createStreamFromFile(dirname(__DIR__) . '/Fixtures/Files/api-response-invalid.xml');

        $this->requestFactory->response = new Response($stream);

        $this->expectException(MalformedApiResponseException::class);
        $this->expectExceptionCode(1677234223);

        $this->subject->getJobs();
    }

    /**
     * @test
     */
    public function getJobsReturnsMappedSingleJobObject(): void
    {
        $stream = $this->streamFactory->createStreamFromFile(dirname(__DIR__) . '/Fixtures/Files/api-response-single-position.xml');

        $this->requestFactory->response = new Response($stream);

        $actual = $this->subject->getJobs();

        self::assertCount(1, $actual);
        self::assertJobEqualsJob($this->createJob(1), $actual[0]);
    }

    /**
     * @test
     */
    public function getJobsReturnsMappedMultipleJobObjects(): void
    {
        $stream = $this->streamFactory->createStreamFromFile(dirname(__DIR__) . '/Fixtures/Files/api-response-multiple-positions.xml');

        $this->requestFactory->response = new Response($stream);

        $actual = $this->subject->getJobs();

        self::assertCount(2, $actual);
        self::assertJobEqualsJob($this->createJob(1), $actual[0]);
        self::assertJobEqualsJob($this->createJob(2), $actual[1]);
    }

    /**
     * @test
     */
    public function getJobsReturnsJobInGivenLanguage(): void
    {
        $stream = $this->streamFactory->createStreamFromFile(dirname(__DIR__) . '/Fixtures/Files/api-response-other-language.xml');

        $this->requestFactory->response = new Response($stream);

        $actual = $this->subject->getJobs('de');

        self::assertSame('https://testing.jobs.personio.local/xml?language=de', $this->requestFactory->lastUri);
        self::assertCount(2, $actual);
        self::assertJobEqualsJob($this->createGermanJob(1), $actual[0]);
        self::assertJobEqualsJob($this->createGermanJob(2), $actual[1]);
    }

    private static function assertJobEqualsJob(Job $expected, Job $actual): void
    {
        // Create expected job descriptions
        $expectedJobDescription1 = (new JobDescription())
            ->setHeader('Hello World!')
            ->setBodytext('<strong>Lorem ipsum dolor sit amet.</strong>')
        ;
        $expectedJobDescription2 = (new JobDescription())
            ->setHeader('See you soon!')
            ->setBodytext('<strong>Lorem ipsum dolor sit amet.</strong>')
        ;

        // Fetch actual job descriptions to compare them separately
        $actualJobDescriptions = $actual->getJobDescriptions()->toArray();

        // Reset job descriptions (we compare them separately)
        $actual->setJobDescriptions(new ObjectStorage());
        $actual->recalculateContentHash();

        // Compare job
        self::assertEquals($expected, $actual);

        // Compare job descriptions
        self::assertCount(2, $actualJobDescriptions);
        self::assertEquals($expectedJobDescription1->setJob($actual), $actualJobDescriptions[0]);
        self::assertEquals($expectedJobDescription2->setJob($actual), $actualJobDescriptions[1]);
    }

    private function createJob(int $id): Job
    {
        $job = (new Job())
            ->setPersonioId($id)
            ->setSubcompany('Test company')
            ->setOffice('Berlin')
            ->setDepartment('IT')
            ->setRecruitingCategory('Testing')
            ->setName('Software tester (f/m/x)')
            ->setEmploymentType(EmploymentType::Permanent->value)
            ->setSeniority(Seniority::Experienced->value)
            ->setSchedule(Schedule::FullTime->value)
            ->setYearsOfExperience(YearsOfExperience::TwoFiveYears->value)
            ->setKeywords('Testing,QA,Fun')
            ->setOccupation('software_and_web_development')
            ->setOccupationCategory('it_software')
            ->setCreateDate(DateTime::createFromFormat(\DateTimeInterface::ATOM, '2023-08-11T14:15:17+00:00'));
        $job->recalculateContentHash();

        return $job;
    }

    private function createGermanJob(int $id): Job
    {
        $job = $this->createJob($id)
            ->setSubcompany('Testfirma')
            ->setName('Software-Tester (w/m/d)')
            ->setKeywords('Testing,QS');
        $job->recalculateContentHash();

        return $job;
    }
}
