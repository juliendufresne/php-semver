PHP7+ SemVer
============

PHP implementation of the Semantic Versioning 2.0 documented on [semver.org](http://semver.org)

[![Build Status][travis-master-img]][travis-master-url] [![Coverage Status][coveralls-master-img]][coveralls-master-url] [![Scrutinizer Code Quality][scrutinizer-master-img]][scrutinizer-master-url]

[travis-master-img]: https://travis-ci.org/git-pull-request/php-semver.svg?branch=master
[travis-master-url]: https://travis-ci.org/git-pull-request/php-semver
[coveralls-master-img]: https://coveralls.io/repos/github/git-pull-request/php-semver/badge.svg?branch=master
[coveralls-master-url]: https://coveralls.io/github/git-pull-request/php-semver?branch=master
[scrutinizer-master-img]: https://scrutinizer-ci.com/g/git-pull-request/php-semver/badges/quality-score.png?b=master
[scrutinizer-master-url]: https://scrutinizer-ci.com/g/git-pull-request/php-semver/?branch=master

Installation
------------

Install the latest version with

```bash
$ composer require semver/semver
```

Requirements
------------

This library is standalone so you don't have to worry about dependencies.  
The only requirement is to use **PHP 7**

Usage
-----

### Create a Version

**from decomposed elements**

```php
use SemVer\SemVer\Version;

$version = new Version(1, 2, 3);
$version = new Version(1, 2, 3, '', 'exp.sha.5114f85');
$version = new Version(1, 2, 3, 'rc.1', 'exp.sha.5114f85');
```

**from a string**

```php
use SemVer\SemVer\Version;

$version = Version::fromString('1.2.3');
$version = Version::fromString('1.2.3+exp.sha.5114f85');
$version = Version::fromString('1.2.3-rc.1+exp.sha.5114f85');
```

### Update to next version

It will always create a new Version, leaving the current as it is.

**update the major version**

```php
use SemVer\SemVer\Version;

$version = Version::fromString('1.2.3');
$nextVersion = $version->major();

var_dump($nextVersion->isEquals(new Version('2.0.0')); // true
```

Special case: when there is a pre-release and both minor and patch version are equals to 0, the next major version is the
actual version without the pre-release

```php
use SemVer\SemVer\Version;

$version = Version::fromString('1.0.0-rc.1');
$nextVersion = $version->major();

var_dump($nextVersion->isEquals(new Version('1.0.0')); // true
```

**update the minor version**

```php
use SemVer\SemVer\Version;

$version = Version::fromString('1.2.3');
$nextVersion = $version->minor();

var_dump($nextVersion->isEquals(new Version('1.3.0')); // true
```

Special case: when there is a pre-release and the patch version is equal to 0, the next minor version is the
actual version without the pre-release

```php
use SemVer\SemVer\Version;

$version = Version::fromString('1.1.0-rc.1');
$nextVersion = $version->minor();

var_dump($nextVersion->isEquals(new Version('1.1.0')); // true
```


**update the patch version**

```php
use SemVer\SemVer\Version;

$version = Version::fromString('1.2.3-rc.1');
$nextVersion = $version->patch();

var_dump($nextVersion->isEquals(new Version('1.2.3')); // true
```

Special case: when there is a pre-release, the next major version is the actual version without the pre-release

```php
use SemVer\SemVer\Version;

$version = Version::fromString('1.0.0-rc.1');
$nextVersion = $version->patch();

var_dump($nextVersion->isEquals(new Version('1.0.0')); // true
```

### Compare two versions

```php
use SemVer\SemVer\Version;
use SemVer\SemVer\VersionComparator;

$version1 = Version::fromString('1.2.3');
$version2 = Version::fromString('1.2.3-rc.1+exp.sha.5114f85');
var_dump(VersionComparator::compare($version1, $version2)); // 1
var_dump($version1->equals($version2)); // false
var_dump($version1->greaterThan($version2)); // true
var_dump($version1->greaterThanOrEqual($version2)); // true
var_dump($version1->lessThan($version2)); // false
var_dump($version1->lessThanOrEqual($version2)); // false
```

### Sort an array of Versions


```php
use SemVer\SemVer\Version;

$versions = [
    Version::fromString('2.0.0'),
    Version::fromString('1.2.3'),
    Version::fromString('1.3.3'),
    Version::fromString('1.3.3-alpha.10'),
    Version::fromString('1.3.3-alpha.2'),
    Version::fromString('1.2.3-rc.1+exp.sha.5114f85'),
];
var_dump(VersionSorter::sort($versions));
// Result:
// [
//    Version::fromString('1.2.3-rc.1+exp.sha.5114f85'),
//    Version::fromString('1.2.3'),
//    Version::fromString('1.3.3-alpha.2'),
//    Version::fromString('1.3.3-alpha.10'),
//    Version::fromString('1.3.3'),
//    Version::fromString('2.0.0'),
// ];
```
