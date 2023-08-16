# Contributing

Thanks for considering contributing to this extension! Since it is an open source
product, its successful further development depends largely on improving and
optimizing it together.

The development of this extension follows the official
[TYPO3 coding standards](https://github.com/TYPO3/coding-standards). To ensure the
stability and cleanliness of the code, various code quality tools are used and most
components are covered with test cases. In addition, we use
[DDEV](https://ddev.readthedocs.io/en/stable/) for local development. Make sure to
set it up as described below. For continuous integration, we use GitHub Actions.

## Preparation

```bash
# Clone repository
git clone https://github.com/CPS-IT/personio-jobs.git
cd personio-jobs

# Start DDEV project
ddev start

# Install dependencies
ddev composer install
```

You can access the DDEV site at <https://typo3-ext-personio-jobs.ddev.site/>.

## Run linters

```bash
# All linters
ddev composer lint

# Specific linters
ddev composer lint:composer
ddev composer lint:editorconfig
ddev composer lint:php
ddev composer lint:typoscript

# Fix all CGL issues
ddev composer fix

# Fix specific CGL issues
ddev composer fix:composer
ddev composer fix:editorconfig
ddev composer fix:php
```

## Run static code analysis

```bash
# All static code analyzers
ddev composer sca

# Specific static code analyzers
ddev composer sca:php
```

## Run tests

```bash
# All tests
ddev composer test

# Specific tests
ddev composer test:functional
ddev composer test:unit

# All tests with code coverage
ddev composer test:coverage

# Specific tests with code coverage
ddev composer test:coverage:functional
ddev composer test:coverage:unit

# Merge code coverage of all test suites
ddev composer test:coverage:merge
```

### Test reports

Code coverage reports are written to `.Build/log/coverage`. You can open the
last merged HTML report like follows:

```bash
open .Build/coverage/html/_merged/index.html
```

💡 Make sure to merge coverage reports as written above.

## Submit a pull request

Once you have finished your work, please **submit a pull request** and describe
what you've done. Ideally, your PR references an issue describing the problem
you're trying to solve.

All described code quality tools are automatically executed on each pull request
for all currently supported PHP versions and TYPO3 versions. Take a look at the
appropriate [workflows](.github/workflows) to get a detailed overview.
