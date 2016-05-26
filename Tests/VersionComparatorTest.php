<?php

/*
 * This file is part of semver/semver.
 *
 * (c) SemVer <https://github.com/git-pull-request>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SemVer\SemVer\Tests;

use PHPUnit_Framework_TestCase;
use SemVer\SemVer\Version;
use SemVer\SemVer\VersionComparator;

/**
 * Test of VersionComparator object.
 */
final class VersionComparatorTest extends PHPUnit_Framework_TestCase
{
    ////////////////////////////////////////////////////////////////////////////
    // ::compare()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @dataProvider provideCompareVersions
     *
     * @param Version $version1
     * @param Version $version2
     * @param int     $expectedResult
     * @param string  $message
     */
    public function testCompare(Version $version1, Version $version2, int $expectedResult, string $message)
    {
        static::assertEquals($expectedResult, VersionComparator::compare($version1, $version2), $message);
    }

    /**
     * @return array
     */
    public function provideCompareVersions() : array
    {
        return [
            // major
            [
                Version::fromString('1.0.0'),
                Version::fromString('2.0.0'),
                -1,
                '::compare() major version. 1.0.0 is lower than 2.0.0',
            ],
            [
                Version::fromString('2.0.0'),
                Version::fromString('1.0.0'),
                1,
                '::compare() major version. 2.0.0 is greater than 1.0.0',
            ],
            [
                Version::fromString('10.0.0'),
                Version::fromString('2.0.0'),
                1,
                '::compare() major version compares numerically. 10.0.0 is greater than 2.0.0',
            ],
            // minor
            [
                Version::fromString('2.0.0'),
                Version::fromString('2.10.0'),
                -1,
                '::compare() minor version. 2.0.0 is lower than 2.10.0',
            ],
            [
                Version::fromString('2.10.0'),
                Version::fromString('2.0.0'),
                1,
                '::compare() minor version. 2.10.0 is greater than 2.0.0',
            ],
            [
                Version::fromString('2.10.0'),
                Version::fromString('2.2.0'),
                1,
                '::compare() minor version compares numerically. 2.10.0 is greater than 2.2.0',
            ],
            // patch
            [
                Version::fromString('2.0.0'),
                Version::fromString('2.0.10'),
                -1,
                '::compare() patch version. 2.0.0 is lower than 2.0.10',
            ],
            [
                Version::fromString('2.0.10'),
                Version::fromString('2.0.0'),
                1,
                '::compare() patch version. 2.0.10 is greater than 2.0.0',
            ],
            [
                Version::fromString('2.0.10'),
                Version::fromString('2.0.2'),
                1,
                '::compare() patch version. 2.0.10 is greater than 2.0.2',
            ],
            [
                Version::fromString('2.0.0'),
                Version::fromString('2.0.0+build'),
                0,
                '::compare() build differs. versions should be equals.',
            ],
            // pre release
            [
                Version::fromString('2.0.0'),
                Version::fromString('2.0.0-alpha'),
                1,
                '::compare() second version has a pre-release, the first not',
            ],
            [
                Version::fromString('2.0.0-alpha'),
                Version::fromString('2.0.0'),
                -1,
                '::compare() first version has a pre-release, the second not',
            ],
            [
                Version::fromString('2.0.0-alpha.1'),
                Version::fromString('2.0.0-alpha'),
                1,
                '::compare() first has two pre-release identifiers, second only has one',
            ],
            [
                Version::fromString('2.0.0-alpha'),
                Version::fromString('2.0.0-alpha.1'),
                -1,
                '::compare() second has two pre-release identifiers, first only has one',
            ],
            [
                Version::fromString('2.0.0-1'),
                Version::fromString('2.0.0-beta'),
                -1,
                '::compare() a numeric identifier is lower than an alphabetical one',
            ],
            [
                Version::fromString('2.0.0-beta'),
                Version::fromString('2.0.0-1'),
                1,
                '::compare() an alphabetical identifier is greater than a numeric one',
            ],
            [
                Version::fromString('2.0.0-alpha.1'),
                Version::fromString('2.0.0-alpha.beta'),
                -1,
                '::compare() a numeric identifier is lower than an alphabetical one even when multiple identifiers given',
            ],
            [
                Version::fromString('2.0.0-alpha.10'),
                Version::fromString('2.0.0-alpha.2'),
                1,
                '::compare() an alphabetical identifier is greater than a numeric one even when multiple identifiers given',
            ],
            [
                Version::fromString('2.0.0-alpha+build127'),
                Version::fromString('2.0.0-alpha+build128'),
                0,
                '::compare() two versions that only differs with their build are equals.',
            ],
        ];
    }
}
