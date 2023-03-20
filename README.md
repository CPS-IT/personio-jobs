<div align="center">

![Extension icon](Resources/Public/Icons/Extension.svg)

# TYPO3 extension `personio_jobs`

[![Maintainability](https://api.codeclimate.com/v1/badges/xxx/maintainability)](https://codeclimate.com/github/CPS-IT/typo3-personio-jobs/maintainability)
[![CGL](https://github.com/CPS-IT/typo3-personio-jobs/actions/workflows/cgl.yaml/badge.svg)](https://github.com/CPS-IT/typo3-personio-jobs/actions/workflows/cgl.yaml)
[![Release](https://github.com/CPS-IT/typo3-personio-jobs/actions/workflows/release.yaml/badge.svg)](https://github.com/CPS-IT/typo3-personio-jobs/actions/workflows/release.yaml)
[![License](http://poser.pugx.org/CPS-IT/typo3-personio-jobs/license)](LICENSE.md)\
[![Version](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/personio_jobs/version/shields)](https://extensions.typo3.org/extension/personio_jobs)
[![Downloads](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/personio_jobs/downloads/shields)](https://extensions.typo3.org/extension/personio_jobs)
[![Supported TYPO3 versions](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/personio_jobs/typo3/shields)](https://extensions.typo3.org/extension/personio_jobs)
[![Extension stability](https://shields.io/endpoint?url=https://typo3-badges.dev/badge/personio_jobs/stability/shields)](https://extensions.typo3.org/extension/personio_jobs)

📦&nbsp;[Packagist](https://packagist.org/packages/cpsit/typo3-personio-jobs) |
🐥&nbsp;[TYPO3 extension repository](https://extensions.typo3.org/extension/personio_jobs) |
💾&nbsp;[Repository](https://github.com/CPS-IT/typo3-personio-jobs) |
🐛&nbsp;[Issue tracker](https://github.com/CPS-IT/typo3-personio-jobs/issues)

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
* Compatible with TYPO3 11.5 LTS and 12.2

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
typo3 personio-jobs:import [--force] [--no-delete] [--no-update] [--dry-run]
```

The following command parameters are available:

| Command parameter       | Description                                              | Required | Default |
|-------------------------|----------------------------------------------------------|----------|---------|
| **`-f`**, **`--force`** | Enforce re-import of unchanged jobs                      | –        | no      |
| **`--no-delete`**       | Do not delete orphaned jobs                              | –        | no      |
| **`--no-update`**       | Do not update imported jobs that have been changed       | –        | no      |
| **`--dry-run`**         | Do not perform database operations, only display changes | –        | no      |

💡 Increase verbosity with `--verbose` or `-v` to show all changes,
even unchanged jobs that were skipped.

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
| **`storagePid`**  | UID of the page under which the job pages are persisted              | ✅        | `0`     |

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
