<?php

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

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Personio Jobs',
    'description' => 'Extension to integrate jobs from Personio Recruiting API',
    'category' => 'plugin',
    'version' => '0.5.9',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'author' => 'Juliane Wundermann, Elias Häußler',
    'author_email' => 'j.wundermann@familie-redlich.de, e.haeussler@familie-redlich.de',
    'author_company' => 'coding. powerful. systems. CPS GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
            'php' => '8.1.0-8.3.99',
        ],
        'suggests' => [
            'schema' => '2.7.0-3.99.99',
        ],
    ],
];
