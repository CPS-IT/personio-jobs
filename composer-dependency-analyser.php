<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$configuration = new Configuration();
$configuration->ignoreErrorsOnPackage('brotkrueml/schema', [ErrorType::DEV_DEPENDENCY_IN_PROD]);

return $configuration;
