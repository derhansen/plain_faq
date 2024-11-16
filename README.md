[![Code Quality Checks](https://github.com/derhansen/plain_faq/actions/workflows/CodeQuality.yml/badge.svg)](https://github.com/derhansen/plain_faq/actions/workflows/CodeQuality.yml)
[![Unit tests](https://github.com/derhansen/plain_faq/actions/workflows/UnitTests.yml/badge.svg)](https://github.com/derhansen/plain_faq/actions/workflows/UnitTests.yml)
[![Functional tests](https://github.com/derhansen/plain_faq/actions/workflows/FunctionalTests.yml/badge.svg)](https://github.com/derhansen/plain_faq/actions/workflows/FunctionalTests.yml)
[![Project Status: Active – The project has reached a stable, usable state and is being actively developed.](https://www.repostatus.org/badges/latest/active.svg)](https://www.repostatus.org/#active)

# Plain FAQ 

## What does it do?

This extension for TYPO3 CMS is a solution to manage frequently asked questions. The focus of the extension is to
keep things simple and to have a modern and clean code basis.

**Features:**

* Easy usage for editors
* Uses TYPO3 system categories to structure FAQs by category
* Field for media and files
* Possibility to add related FAQs
* Configurable template layouts for the views
* Automatic cache clearing when FAQ has been changed in backend
* Symfony Console commands to migrate from ext:irfaq
* PSR-14 events to extend the extension with own functionality
* Pagination for list view using the TYPO3 pagination API

**Background**

* Based on Extbase and Fluid
* Covered with unit and functional tests
* Actively maintained

## Screenshot

The screenshot below shows the backend form of a FAQ record.

![Screenshot of backend form](Documentation/Images/faq-screenshot.png "FAQ record")

## Documentation

The extension includes a detailed documentation in ReST format. You can view the extension manual on TYPO3 
TER https://docs.typo3.org/p/derhansen/plain-faq/master/en-us/

## Installation

### Installation using Composer

The recommended way to install the extension is by using [Composer](https://getcomposer.org/). In your Composer based TYPO3 project root, just do `composer require derhansen/plain-faq`. 

### Installation as extension from TYPO3 Extension Repository (TER)

Download and install the extension with the TYPO3 extension manager module.

## Versions

| Version | TYPO3     | PHP       | Support/Development                  |
|---------|-----------|-----------|--------------------------------------|
| 5.x     | 13.4      | 8.2 - 8.4 | Features, Bugfixes, Security Updates |
| 4.x     | 12.4      | 8.0 - 8.4 | Features, Bugfixes, Security Updates |
| 3.x     | 11.5      | 7.4 - 8.4 | Bugfixes, Security Updates           |
| 2.x     | 10.4      | 7.2 - 7.4 | Security Updates                     |
| 1.x     | 8.7 - 9.5 | 7.0 - 7.3 | Support dropped                      |

## Thanks for sponsoring

The initial development of this extension is sponsored by [Julius-Maximilians-Universität Würzburg](https://www.uni-wuerzburg.de).
Thanks for supporting TYPO3 and open source software!

