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

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Personio Jobs',
    'description' => 'Extension to integrate jobs from Personio Recruiting API',
    'category' => 'plugin',
    'version' => '0.6.0',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'author' => 'Juliane Wundermann, Elias Häußler',
    'author_email' => 'j.wundermann@familie-redlich.de, e.haeussler@familie-redlich.de',
    'author_company' => 'coding. powerful. systems. CPS GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'php' => '8.2.0-8.5.99',
            'cache_bags' => '0.3.0-0.3.99',
            'schema' => '3.0.0-3.99.99',
        ],
    ],
];
