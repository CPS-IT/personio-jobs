<?php

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

use CPSIT\Typo3PersonioJobs\Domain\Model\JobDescription;

return [
    'ctrl' => [
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'title' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => 'EXT:personio_jobs/Resources/Public/Icons/tx_personiojobs_domain_model_job.svg',
        'searchFields' => 'personio_id, name, recruiting_category, slug',
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'personio_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.personio_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int,unique,required',
                'readOnly' => true,
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'slug' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.slug',
            'config' => [
                'type' => 'slug',
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
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.content_hash',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'max' => 255,
                'readOnly' => true,
            ],
        ],
        'subcompany' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.subcompany',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'office' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.office',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'department' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.department',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'recruiting_category' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.recruiting_category',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'employment_type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.employment_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => \CPSIT\Typo3PersonioJobs\Configuration\Tca::mapItems(
                    \CPSIT\Typo3PersonioJobs\Domain\Model\Job::TABLE_NAME,
                    'employment_type',
                    \CPSIT\Typo3PersonioJobs\Enums\Job\EmploymentType::cases(),
                ),
            ],
        ],
        'seniority' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.seniority',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => \CPSIT\Typo3PersonioJobs\Configuration\Tca::mapItems(
                    \CPSIT\Typo3PersonioJobs\Domain\Model\Job::TABLE_NAME,
                    'seniority',
                    \CPSIT\Typo3PersonioJobs\Enums\Job\Seniority::cases(),
                ),
            ],
        ],
        'schedule' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.schedule',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => \CPSIT\Typo3PersonioJobs\Configuration\Tca::mapItems(
                    \CPSIT\Typo3PersonioJobs\Domain\Model\Job::TABLE_NAME,
                    'schedule',
                    \CPSIT\Typo3PersonioJobs\Enums\Job\Schedule::cases(),
                ),
            ],
        ],
        'years_of_experience' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.years_of_experience',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => \CPSIT\Typo3PersonioJobs\Configuration\Tca::mapItems(
                    \CPSIT\Typo3PersonioJobs\Domain\Model\Job::TABLE_NAME,
                    'years_of_experience',
                    \CPSIT\Typo3PersonioJobs\Enums\Job\YearsOfExperience::cases(),
                ),
            ],
        ],
        'keywords' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.keywords',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
            ],
        ],
        'occupation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.occupation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'occupation_category' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.occupation_category',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'create_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.create_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,required',
            ],
        ],
        'job_descriptions' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job.job_descriptions',
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
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    personio_id,
                    name,
                    slug,
                    content_hash,
                --div--;LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tabs.job,
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
                --div--;LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tabs.description,
                    job_descriptions,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
                    starttime,
                    endtime
            ',
        ],
    ],
];
