<div align="center">

![Extension icon](Resources/Public/Icons/Extension.svg)

# TYPO3 extension `personio_jobs`

[![Coverage](https://codecov.io/gh/CPS-IT/personio-jobs/graph/badge.svg?token=9T4ZqzlqVL)](https://codecov.io/gh/CPS-IT/personio-jobs)
[![Maintainability](https://api.codeclimate.com/v1/badges/75952c5451dea0632fc0/maintainability)](https://codeclimate.com/github/CPS-IT/personio-jobs/maintainability)
[![CGL](https://github.com/CPS-IT/personio-jobs/actions/workflows/cgl.yaml/badge.svg)](https://github.com/CPS-IT/personio-jobs/actions/workflows/cgl.yaml)
[![Release](https://github.com/CPS-IT/personio-jobs/actions/workflows/release.yaml/badge.svg)](https://github.com/CPS-IT/personio-jobs/actions/workflows/release.yaml)
[![License](http://poser.pugx.org/cpsit/typo3-personio-jobs/license)](LICENSE.md)\
[![Version](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/personio_jobs/version/shields)](https://extensions.typo3.org/extension/personio_jobs)
[![Downloads](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/personio_jobs/downloads/shields)](https://extensions.typo3.org/extension/personio_jobs)
[![Supported TYPO3 versions](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/personio_jobs/typo3/shields)](https://extensions.typo3.org/extension/personio_jobs)
[![Extension stability](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/personio_jobs/stability/shields)](https://extensions.typo3.org/extension/personio_jobs)

📦&nbsp;[Packagist](https://packagist.org/packages/cpsit/typo3-personio-jobs) |
🐥&nbsp;[TYPO3 extension repository](https://extensions.typo3.org/extension/personio_jobs) |
💾&nbsp;[Repository](https://github.com/CPS-IT/personio-jobs) |
🐛&nbsp;[Issue tracker](https://github.com/CPS-IT/personio-jobs/issues)

</div>

---

An extension for TYPO3 CMS that integrates jobs from Personio Recruiting API
into TYPO3. It provides a console command to import jobs into modern-typed
value objects. In addition, plugins for list and detail views are provided
with preconfigured support for Bootstrap v5 components.

## 🚀 Features

* Console command to import jobs from Personio Recruiting API
* Usage of modern-typed value objects during the import process
* Plugins for list and detail view
* Optional support for JSON Schema on job detail pages using [EXT:schema][1]
* Compatible with TYPO3 11.5 LTS and 12.4 LTS

## 🔥 Installation

### Composer

```bash
composer require cpsit/typo3-personio-jobs
```

💡 If you want to use the [JSON schema](#json-schema) feature, you must
additionally require the `schema` extension:

```bash
composer require brotkrueml/schema
```

### TER

Alternatively, you can download the extension via the
[TYPO3 extension repository (TER)][2].

### First-step configuration

Once installed, make sure to include the TypoScript setup at
`EXT:personio_jobs/Configuration/TypoScript` in your root template.

## ⚡ Usage

### Plugins

The extension provides two plugins:

| Icon                                                           | Description                                                                                                                                                                |
|----------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| ![List plugin icon](Resources/Public/Icons/plugins.list.svg)   | **Personio: Job list**<br>Lists all imported jobs as unordered list. Each list item shows the job title, office and schedule and links to the job's detail view.           |
| ![Detail plugin icon](Resources/Public/Icons/plugins.show.svg) | **Personio: Job detail**<br>Shows a single job, including several job properties and all imported job descriptions. In addition, it renders a button to apply for the job. |

### Command-line usage

#### `personio-jobs:import`

```bash
typo3 personio-jobs:import <storage-pid> [options]
```

The following command parameters are available:

| Command parameter          | Description                                                                         | Required | Default        |
|----------------------------|-------------------------------------------------------------------------------------|----------|----------------|
| **`storage-pid`**          | Storage pid of imported jobs                                                        | ✅        | –              |
| **`-l`**, **`--language`** | Job language, should be the two-letter ISO 639-1 code of a configured site language | –        | –<sup>1)</sup> |
| **`-f`**, **`--force`**    | Enforce re-import of unchanged jobs                                                 | –        | no             |
| **`--no-delete`**          | Do not delete orphaned jobs                                                         | –        | no             |
| **`--no-update`**          | Do not update imported jobs that have been changed                                  | –        | no             |
| **`--dry-run`**            | Do not perform database operations, only display changes                            | –        | no             |

*<sup>1)</sup> If no language is configured, Personio will return
jobs from the default language. They will be persisted in a way that
all languages are matched (that is, the value of `sys_language_uid`
will be `-1`).*

💡 Increase verbosity with `--verbose` or `-v` to show all changes,
even unchanged jobs that were skipped.

### Code usage

The Personio job import process can also be triggered directly within PHP. For
this, two services exist:

- [`PersonioApiService`](Classes/Service/PersonioApiService.php) provides the
  main functionality to fetch jobs from Personio API and return them as hydrated
  [`Job`](Classes/Domain/Model/Job.php) models. Note that these jobs are not yet
  persisted. Instead, they only represent the current Personio job feed as
  strong-typed value objects.
- [`PersonioImportService`](Classes/Service/PersonioImportService.php) provides
  additional functionality to actually persist imported jobs. Under the hood, the
  previously mentioned `PersonioApiService` is called to fetch jobs, followed by
  their actual persistence. For the import process, a set of import settings is
  available:
  - `int $storagePid`: Page ID where to persist new or updated jobs.
  - `bool $updateExistingJobs = true`: Define whether to update jobs that were
    already imported, but have changed in the meantime.
  - `bool $deleteOrphans = true`: Define whether to delete jobs that are no
    longer available on Personio.
  - `bool $forceImport = false`: Select whether existing, unchanged jobs should
    be re-imported.
  - `bool $dryRun = false`: Do not perform any persistence operations, just fetch
    and validate jobs.

#### Fetch jobs from Personio API

```php
use CPSIT\Typo3PersonioJobs\Service\PersonioApiService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$apiService = GeneralUtility::makeInstance(PersonioApiService::class);
$jobs = $apiService->getJobs();

foreach ($jobs as $job) {
    echo 'Successfully fetched job: ' . $job->getName() . PHP_EOL;
}
```

#### Import jobs from Personio API

```php
use CPSIT\Typo3PersonioJobs\Service\PersonioImportService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$importService = GeneralUtility::makeInstance(PersonioImportService::class);
$result = $importService->import();

foreach ($result->getNewJobs() as $newJob) {
    echo 'Imported new job: ' . $newJob->getName() . PHP_EOL;
}

foreach ($result->getUpdatedJobs() as $updatedJob) {
    echo 'Updated job: ' . $updatedJob->getName() . PHP_EOL;
}

foreach ($result->getRemovedJobs() as $removedJob) {
    echo 'Removed job: ' . $removedJob->getName() . PHP_EOL;
}

foreach ($result->getSkippedJobs() as $skippedJob) {
    echo 'Skipped job: ' . $skippedJob->getName() . PHP_EOL;
}
```

### JSON schema

In combination with [EXT:schema][1], a JSON schema for a single job is included
on job detail pages. It is rendered as type [`JobPosting`][3] and includes some
generic job properties.

**⚠️ The `schema` extension must be installed to use this feature. Read more in
the [installation](#-installation) section above.**

## 📂 Configuration

### TypoScript

The following TypoScript constants are available:

| TypoScript constant                                | Description               | Required | Default |
|----------------------------------------------------|---------------------------|----------|---------|
| **`plugin.tx_personiojobs.view.templateRootPath`** | Path to template root     | –        | –       |
| **`plugin.tx_personiojobs.view.partialRootPath`**  | Path to template partials | –        | –       |
| **`plugin.tx_personiojobs.view.layoutRootPath`**   | Path to template layouts  | –        | –       |

### Extension configuration

The following extension configuration options are available:

| Configuration key | Description                                                          | Required | Default |
|-------------------|----------------------------------------------------------------------|----------|---------|
| **`apiUrl`**      | URL to Personio job page, e.g. `https://my-company.jobs.personio.de` | ✅        | –       |

### Routing configuration

On each import, a slug is generated. The slug can be used for an advanced routing
configuration of job detail pages.

Example:

```yaml
# config/sites/<identifier>/config.yaml

routeEnhancers:
  PersonioJobDetail:
    type: Extbase
    limitToPages:
      # Replace with the actual detail page id
      - 10
    extension: PersonioJobs
    plugin: Show
    routes:
      -
        routePath: '/job/{job_title}'
        _controller: 'Job::show'
        _arguments:
          job_title: job
    defaultController: 'Job::show'
    aspects:
      job_title:
        type: PersistedAliasMapper
        tableName: tx_personiojobs_domain_model_job
        routeFieldName: slug
```

## ⏰ Events

PSR-14 events can be used to modify jobs and job schemas. The following events
are available:

* [`AfterJobsImportedEvent`](Classes/Event/AfterJobsImportedEvent.php)
* [`AfterJobsMappedEvent`](Classes/Event/AfterJobsMappedEvent.php)
* [`EnrichJobPostingSchemaEvent`](Classes/Event/EnrichJobPostingSchemaEvent.php)

## 🚧 Migration

### 0.4.x → 0.5.x

#### Decouple import process

Import process is moved to a separate service class.

* All import operations are now performed by the new
  [`PersonioImportService`](Classes/Service/PersonioImportService.php) class.
* `PersonioService` was renamed to [`PersonioApiService`](Classes/Service/PersonioApiService.php).
  Replace all usages of this class by the new class name.
* Import results are now properly displayed by objects of the new
  [`ImportResult`](Classes/Domain/Model/Dto/ImportResult.php) class.
* Public methods in [`AfterJobsImportedEvent`](Classes/Event/AfterJobsImportedEvent.php)
  have changed to match the new `ImportResult` class. Use the new public method
  `AfterJobsImportedEvent::getImportResult()` instead of previously available
  methods.

### 0.3.x → 0.4.x

#### Finalize `SchemaFactory`

[`SchemaFactory`](Classes/Domain/Factory/SchemaFactory.php) is now final and cannot
be extended anymore.

* Remove classes extending from `SchemaFactory`.
* Replace customizations of the `SchemaFactory` by an event listener for the
  [`EnrichJobPostingSchemaEvent`](Classes/Event/EnrichJobPostingSchemaEvent.php)
  PSR-14 event.

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## 💎 Credits

The Personio logo as part of all distributed icons is a trademark of
[Personio SE & Co. KG][4].

## ⭐ License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE.md).

[1]: https://extensions.typo3.org/extension/schema
[2]: https://extensions.typo3.org/extension/personio_jobs
[3]: https://schema.org/JobPosting
[4]: https://www.personio.de/
