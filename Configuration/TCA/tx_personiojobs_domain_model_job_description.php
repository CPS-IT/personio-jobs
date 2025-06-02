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

use CPSIT\Typo3PersonioJobs\Domain\Model\Job;

return [
    'ctrl' => [
        'label' => 'header',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'title' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job_description',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => 'EXT:personio_jobs/Resources/Public/Icons/tx_personiojobs_domain_model_job_description.svg',
        'searchFields' => 'header',
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
                        'label' => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'default' => 0,
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'header' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job_description.header',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'bodytext' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job_description.bodytext',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'required' => true,
            ],
        ],
        'job' => [
            'exclude' => true,
            'label' => 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:tx_personiojobs_domain_model_job_description.job',
            'config' => [
                'type' => 'inline',
                'foreign_table' => Job::TABLE_NAME,
                'readOnly' => true,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    header,
                    bodytext,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
                    starttime,
                    endtime,
            ',
        ],
    ],
];
