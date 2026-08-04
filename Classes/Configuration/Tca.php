<?php

declare(strict_types=1);

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

namespace CPSIT\Typo3PersonioJobs\Configuration;

use CPSIT\Typo3PersonioJobs\Extension;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Tca
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class Tca
{
    /**
     * @param list<string|\BackedEnum> $itemValues
     * @return list<array{string, string}>|list<array{label: string, value: string}>
     */
    public static function mapItems(
        string $tableName,
        string $fieldName,
        array $itemValues,
        bool $allowEmpty = false,
    ): array {
        $items = [];

        if ($allowEmpty) {
            $items[] = self::resolveItem('', '');
        }

        foreach ($itemValues as $itemValue) {
            if ($itemValue instanceof \BackedEnum) {
                $itemValue = (string)$itemValue->value;
            }

            $items[] = self::resolveItem(
                self::label(
                    'personio_jobs.db:' . $tableName . '.' . $fieldName . '.' . $itemValue,
                    'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:' . $tableName . '.' . $fieldName . '.' . $itemValue,
                ),
                $itemValue,
            );
        }

        return $items;
    }

    public static function addPlugin(
        string $name,
        ?string $icon = null,
        ?string $flexForm = null,
    ): void {
        $pluginSignature = self::buildPluginSignature($name);

        // @todo Simplify once support for TYPO3 v13 is dropped
        $usesLegacyFlexFormRegistration = self::isLegacyTypo3Version();
        $registerPluginArguments = [
            Extension::KEY,
            $name,
            self::label(
                'personio_jobs.db:plugins.' . lcfirst($name) . '.title',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:plugins.' . lcfirst($name) . '.title',
            ),
            $icon,
            'personio',
            self::label(
                'personio_jobs.db:plugins.' . lcfirst($name) . '.description',
                'LLL:EXT:personio_jobs/Resources/Private/Language/locallang_db.xlf:plugins.' . lcfirst($name) . '.description',
            ),
        ];

        if ($flexForm !== null) {
            if ($usesLegacyFlexFormRegistration) {
                ExtensionManagementUtility::addPiFlexFormValue('*', $flexForm, $pluginSignature);
                ExtensionManagementUtility::addToAllTCAtypes(
                    'tt_content',
                    '--div--;Configuration,pi_flexform,',
                    $pluginSignature,
                    'after:subheader',
                );
            } else {
                $registerPluginArguments[] = $flexForm;
            }
        }

        ExtensionUtility::registerPlugin(...$registerPluginArguments);
    }

    /**
     * @return array{label: string, value: string}
     */
    private static function resolveItem(string $label, string $value): array
    {
        return [
            'label' => $label,
            'value' => $value,
        ];
    }

    private static function buildPluginSignature(string $pluginName): string
    {
        $extensionName = GeneralUtility::underscoredToUpperCamelCase(Extension::KEY);

        return strtolower($extensionName . '_' . $pluginName);
    }

    /**
     * @todo Remove once support for TYPO3 v13 is dropped
     */
    public static function label(string $new, string $legacy): string
    {
        return self::isLegacyTypo3Version() ? $legacy : $new;
    }

    /**
     * @todo Remove once support for TYPO3 v13 is dropped
     */
    public static function isLegacyTypo3Version(): bool
    {
        return (new Typo3Version())->getMajorVersion() === 13;
    }
}
