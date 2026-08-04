<?php

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

defined('TYPO3') or die();

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

use CPSIT\Typo3PersonioJobs\Configuration\Tca;
use CPSIT\Typo3PersonioJobs\Domain\Model\Job;
use CPSIT\Typo3PersonioJobs\Domain\Model\JobDescription;
use CPSIT\Typo3PersonioJobs\Enums\Job\EmploymentType;
use CPSIT\Typo3PersonioJobs\Enums\Job\Schedule;
use CPSIT\Typo3PersonioJobs\Enums\Job\Seniority;
use CPSIT\Typo3PersonioJobs\Enums\Job\YearsOfExperience;

$tca = [
    'ctrl' => [
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'title' => Tca::label(
            'personio_jobs.db:tx_personiojobs_domain_model_job',
            'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job',
        ),
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => 'EXT:personio_jobs/Resources/Public/Icons/tx_personiojobs_domain_model_job.svg',
    ],
    'columns' => [
        'personio_id' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.personio_id',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.personio_id',
            ),
            'config' => [
                'type' => 'number',
                'size' => 30,
                'eval' => 'unique',
                'readOnly' => true,
                'required' => true,
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.name',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.name',
            ),
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'slug' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.slug',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.slug',
            ),
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => [
                        'name',
                        'personio_id',
                    ],
                    'fieldSeparator' => '-',
                    'replacements' => [
                        '/' => '-',
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'unique',
                'default' => '',
            ],
        ],
        'content_hash' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.content_hash',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.content_hash',
            ),
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
                'readOnly' => true,
                'required' => true,
            ],
        ],
        'subcompany' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.subcompany',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.subcompany',
            ),
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'office' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.office',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.office',
            ),
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'department' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.department',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.department',
            ),
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'recruiting_category' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.recruiting_category',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.recruiting_category',
            ),
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'employment_type' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.employment_type',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.employment_type',
            ),
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => Tca::mapItems(Job::TABLE_NAME, 'employment_type', EmploymentType::cases()),
            ],
        ],
        'seniority' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.seniority',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.seniority',
            ),
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => Tca::mapItems(
                    Job::TABLE_NAME,
                    'seniority',
                    Seniority::cases(),
                ),
            ],
        ],
        'schedule' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.schedule',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.schedule',
            ),
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => Tca::mapItems(Job::TABLE_NAME, 'schedule', Schedule::cases()),
            ],
        ],
        'years_of_experience' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.years_of_experience',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.years_of_experience',
            ),
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => Tca::mapItems(
                    Job::TABLE_NAME,
                    'years_of_experience',
                    YearsOfExperience::cases(),
                    true,
                ),
            ],
        ],
        'keywords' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.keywords',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.keywords',
            ),
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
            ],
        ],
        'occupation' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.occupation',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.occupation',
            ),
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'occupation_category' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.occupation_category',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.occupation_category',
            ),
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'create_date' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.create_date',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.create_date',
            ),
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'required' => true,
            ],
        ],
        'job_descriptions' => [
            'exclude' => true,
            'label' => Tca::label(
                'personio_jobs.db:tx_personiojobs_domain_model_job.job_descriptions',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.job_descriptions',
            ),
            'config' => [
                'type' => 'inline',
                'foreign_table' => JobDescription::TABLE_NAME,
                'foreign_field' => 'job',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;' . Tca::label('core.form.tabs:general', 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general') . ',
                    personio_id,
                    name,
                    slug,
                    content_hash,
                --div--;' . Tca::label('personio_jobs.db:tabs.job', 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tabs.job') . ',
                    subcompany,
                    office,
                    department,
                    recruiting_category,
                    employment_type,
                    seniority,
                    schedule,
                    years_of_experience,
                    keywords,
                    occupation,
                    occupation_category,
                    create_date,
                --div--;' . Tca::label('personio_jobs.db:tabs.description', 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tabs.description') . ',
                    job_descriptions,
                --div--;' . Tca::label('core.form.tabs:access', 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access') . ',
                    hidden,
                    starttime,
                    endtime
            ',
        ],
    ],
];

// @todo Remove once support for TYPO3 v13 is dropped
if (Tca::isLegacyTypo3Version()) {
    $tca['ctrl']['searchFields'] = 'personio_id, name, recruiting_category, slug';
}

return $tca;
