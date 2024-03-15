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

namespace CPSIT\Typo3PersonioJobs\Controller;

use Brotkrueml\Schema\Manager\SchemaManager;
use CPSIT\Typo3PersonioJobs\Cache\CacheManager;
use CPSIT\Typo3PersonioJobs\Domain\Factory\SchemaFactory;
use CPSIT\Typo3PersonioJobs\Domain\Model\Dto\ListDemand;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Domain\Repository\JobRepository;
use CPSIT\Typo3PersonioJobs\Exception\ExtensionNotLoadedException;
use CPSIT\Typo3PersonioJobs\PageTitle\JobPageTitleProvider;
use CPSIT\Typo3PersonioJobs\Pagination\PaginationFactory;
use CPSIT\Typo3PersonioJobs\Service\PersonioApiService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * JobController
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class JobController extends ActionController
{
    public function __construct(
        protected readonly JobRepository $jobRepository,
        protected readonly MetaTagManagerRegistry $metaTagManagerRegistry,
        protected readonly JobPageTitleProvider $pageTitleProvider,
        protected readonly CacheManager $cacheManager,
        protected readonly PersonioApiService $personioApiService,
        protected readonly SchemaFactory $schemaFactory,
        protected readonly PaginationFactory $paginationFactory,
    ) {}

    public function listAction(): ResponseInterface
    {
        $this->cacheManager->addTag();

        // Fetch jobs
        $demand = ListDemand::fromArray($this->settings);
        $jobs = $this->jobRepository->findByDemand($demand);

        // Create pagination
        if ($this->settings['enablePagination'] ?? false) {
            [$paginator, $pagination] = $this->createPagination($jobs);
        } else {
            $paginator = null;
            $pagination = null;
        }

        $this->view->assignMultiple([
            'jobs' => $jobs,
            'paginator' => $paginator,
            'pagination' => $pagination,
        ]);

        return $this->htmlResponse();
    }

    public function showAction(Job $job): ResponseInterface
    {
        $this->cacheManager->addTag($job);

        $this->overwritePageTitle($job);
        $this->addMetaTags($job);
        $this->addSchema($job);

        $this->view->assign('job', $job);
        $this->view->assign('applyUrl', (string)$this->personioApiService->getApplyUrl($job));

        return $this->htmlResponse();
    }

    protected function overwritePageTitle(Job $job): void
    {
        $this->pageTitleProvider->setJob($job);
    }

    protected function addMetaTags(Job $job): void
    {
        $description = trim($this->generateDescription($job));
        $keywords = trim($job->getKeywords());

        // Add description
        if ($description !== '') {
            $this->addMetaTag('description', $description);
            $this->addMetaTag('og:description', $description);
            $this->addMetaTag('twitter:description', $description);
        }

        // Add keywords
        if ($keywords !== '') {
            $this->addMetaTag('keywords', $keywords);
        }
    }

    protected function addMetaTag(string $property, string $content): void
    {
        $metaTagManager = $this->metaTagManagerRegistry->getManagerForProperty($property);
        $metaTagManager->addProperty($property, $content);
    }

    protected function generateDescription(Job $job, int $maxLength = 150): string
    {
        $description = '';

        foreach ($job->getJobDescriptions() as $jobDescription) {
            $rawJobDescription = strip_tags($jobDescription->getBodytext());
            $description .= $rawJobDescription . ' ';

            if (mb_strlen($description) >= $maxLength) {
                break;
            }
        }

        return mb_strimwidth($description, 0, $maxLength, '…');
    }

    protected function addSchema(Job $job): void
    {
        try {
            $jobPosting = $this->schemaFactory->createJobPosting($job);
        } catch (ExtensionNotLoadedException) {
            // Early return if schema extension is not installed
            return;
        }

        $schemaManager = GeneralUtility::makeInstance(SchemaManager::class);
        $schemaManager->addType($jobPosting);
    }

    /**
     * @param QueryResultInterface<Job> $jobs
     * @return array{PaginatorInterface, PaginationInterface}
     */
    private function createPagination(QueryResultInterface $jobs): array
    {
        $itemsPerPage = (int)($this->settings['itemsPerPage'] ?? 10);
        $maximumNumberOfLinks = (int)($this->settings['maximumNumberOfLinks'] ?? 5);
        $currentPageNumber = 1;

        // Get current page number from request
        if ($this->request->hasArgument('currentPage')) {
            $currentPageFromRequest = $this->request->getArgument('currentPage');

            if (is_numeric($currentPageFromRequest) && $currentPageFromRequest >= 1) {
                $currentPageNumber = (int)$currentPageFromRequest;
            }
        }

        $paginator = new QueryResultPaginator($jobs, $currentPageNumber, $itemsPerPage);
        $pagination = $this->paginationFactory->get($paginator, $maximumNumberOfLinks);

        return [$paginator, $pagination];
    }
}
