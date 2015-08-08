<img src="https://fluidtypo3.org/logo.svgz" width="100%" />

Builder: Development Support Utilities
======================================

[![Build Status](https://img.shields.io/travis/FluidTYPO3/builder.svg?style=flat-square&label=package)](https://travis-ci.org/FluidTYPO3/builder) [![Coverage Status](https://img.shields.io/coveralls/FluidTYPO3/builder/development.svg?style=flat-square)](https://coveralls.io/r/FluidTYPO3/builder) [![Build Status](https://img.shields.io/travis/FluidTYPO3/fluidtypo3-testing.svg?style=flat-square&label=framework)](https://travis-ci.org/FluidTYPO3/fluidtypo3-testing/) [![Coverage Status](https://img.shields.io/coveralls/FluidTYPO3/fluidtypo3-testing/master.svg?style=flat-square)](https://coveralls.io/r/FluidTYPO3/fluidtypo3-testing)

## Usage

Execution is done via CommandControllers. Those are invoked by executing the TYPO3 cli-script:

```bash
php ./typo3/cli_dispatch.phpsh NAMESPACE COMMAND
```

### Commands for Extension extbase

```bash
php ./typo3/cli_dispatch.phpsh extbase help
Extbase 6.1.0
usage: ./cli_dispatch.phpsh extbase <command identifier>

The following commands are currently available:

EXTENSION "EXTBASE":
-------------------------------------------------------------------------------
  help                                     Display help for a command


EXTENSION "BUILDER":
-------------------------------------------------------------------------------
  builder:fluidsyntax                      Syntax check Fluid template
  builder:phpsyntax                        Syntax check PHP code
  builder:providerextension                Builds a ProviderExtension

See './cli_dispatch.phpsh extbase help <command identifier>' for more information about a specific command.
```

#### Fluid syntax checker

```bash
php typo3/cli_dispatch.phpsh extbase help builder:fluidsyntax

Syntax check Fluid template

COMMAND:
  builder:builder:fluidsyntax

USAGE:
  ./cli_dispatch.phpsh extbase builder:fluidsyntax [<options>]

OPTIONS:
  --extension          Optional extension key (if path is included, only files
                       in that path in this extension are checked)
  --path               file or folder path (if extensionKey is included, path
                       is relative to this extension)
  --extensions         If provided, this CSV list of file extensions are
                       considered Fluid templates
  --verbose            If TRUE, outputs more information about each file check
                       - default is to only output errors

DESCRIPTION:
  Syntax check Fluid template

  Checks one template file, all templates in
  an extension or a sub-path (which can be used
```

#### PHP Syntax checker

```
php typo3/cli_dispatch.phpsh extbase help builder:phpsyntax

Syntax check PHP code

COMMAND:
  builder:builder:phpsyntax

USAGE:
  ./cli_dispatch.phpsh extbase builder:phpsyntax [<options>]

OPTIONS:
  --extension          Optional extension key (if path is included, only files
                       in that path in this extension are checked)
  --path               file or folder path (if extensionKey is included, path
                       is relative to this extension)
  --verbose            If TRUE, outputs more information about each file check
                       - default is to only output errors

DESCRIPTION:
  Syntax check PHP code

  Checks PHP source files in $path, if extension
  key is also given, only files in that path relative
```

#### Generate a FluidTYPO3 provider extension

This may be the most important command available. It allows you to generate a stub extension which is fully capable
of being used as provider for fluidpages, fluidcontent and fluidbackend extensions.

```bash
php typo3/cli_dispatch.phpsh extbase help builder:providerextension

Builds a ProviderExtension

COMMAND:
  builder:builder:providerextension

USAGE:
  ./cli_dispatch.phpsh extbase builder:providerextension [<options>] <extension key> <author>

ARGUMENTS:
  --extension-key      The extension key which should be generated. Must not
                       exist.
  --author             The author of the extension, in the format "Name
                       Lastname <name@example.com>" with optional company name,
                       in which case form is "Name Lastname <name@example.com>,
                       Company Name"

OPTIONS:
  --title              The title of the resulting extension, by default
                       "Provider extension for $enabledFeaturesList"
  --description        The description of the resulting extension, by default
                       "Provider extension for $enabledFeaturesList"
  --use-vhs            If TRUE, adds the VHS extension as dependency -
                       recommended, on by default
  --pages              If TRUE, generates basic files for implementing Fluid
                       Page templates
  --content            IF TRUE, generates basic files for implementing Fluid
                       Content templates
  --backend            If TRUE, generates basic files for implementing Fluid
                       Backend modules
  --controllers        If TRUE, generates controllers for each enabled feature.
                       Enabling $backend will always generate a controller
                       regardless of this toggle.
  --minimum-version    The minimum required core version for this extension,
                       defaults to latest LTS (currently 4.5)
  --dry                If TRUE, performs a dry run: does not write any files
                       but reports which files would have been written
  --verbose            If FALSE, suppresses a lot of the otherwise output
                       messages (to STDOUT)
  --git                If TRUE, initialises the newly created extension
                       directory as a Git repository and commits all files. You
                       can then "git add remote origin <URL>" and "git push
                       origin master -u" to push the initial state
  --travis             If TRUE, generates a Travis-CI build script which uses
                       Fluid Powered TYPO3 coding standards analysis and code
                       inspections to automate testing on Travis-CI

DESCRIPTION:
  Builds a ProviderExtension

  The resulting extension will contain source code
  and configuration options needed by the various
  toggles. Each of these toggles enable/disable
  generation of source code and configuration for
```
