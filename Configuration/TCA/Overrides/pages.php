<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

$ll = 'LLL:EXT:personio_jobs/Resources/Private/Language/locallang.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'personio_jobid' => [
            'exclude' => 1,
            'label' => $ll . 'pages.personio_jobid',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '200',
                'default' => '0',
            ],
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    'personio_jobid'
);
