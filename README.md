PHP7+ SemVer
============

PHP implementation of the Semantic Versioning 2.0 documented on [semver.org](http://semver.org)

[![Build Status][travis-master-svg]][travis-master-url] [![Coverage Status][coveralls-master-svg]][coveralls-master-url]

[travis-master-svg]: https://travis-ci.org/git-pull-request/php-semver.svg?branch=master
[travis-master-url]: https://travis-ci.org/git-pull-request/php-semver
[coveralls-master-svg]: https://coveralls.io/repos/github/git-pull-request/php-semver/badge.svg?branch=master
[coveralls-master-url]: https://coveralls.io/github/git-pull-request/php-semver?branch=master

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
var_dump(Version::sort($versions));
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
