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

final class VersionTest extends PHPUnit_Framework_TestCase
{
    ////////////////////////////////////////////////////////////////////////////
    // __construct()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @dataProvider provideValidVersion
     *
     * @param string $unused
     * @param int    $major
     * @param int    $minor
     * @param int    $patch
     * @param string $preRelease
     * @param string $build
     */
    public function testConstructor(
        string $unused,
        int $major,
        int $minor,
        int $patch,
        string $preRelease,
        string $build
    ) {
        $version = new Version($major, $minor, $patch, $preRelease, $build);
        static::assertEquals(
            $major,
            $version->getMajor(),
            '__construct() takes the major version as its first argument'
        );
        static::assertEquals(
            $minor,
            $version->getMinor(),
            '__construct() takes the minor version as its second argument'
        );
        static::assertEquals(
            $patch,
            $version->getPatch(),
            '__construct() takes the patch version as its third argument'
        );
        static::assertEquals(
            $preRelease,
            $version->getPreRelease(),
            '__construct() takes the pre-release version as its forth argument'
        );
        static::assertEquals(
            $build,
            $version->getBuild(),
            '__construct() takes the build version as its fifth argument'
        );
    }

    /**
     * @dataProvider             provideWrongPreReleaseVersion
     * @expectedException \SemVer\SemVer\Exception\InvalidArgumentException
     * @expectedExceptionMessage The pre-release version is not compatible with rule 9 of the specifications.
     *
     * @param string $preRelease
     */
    public function testConstructorFailsIfPreReleaseNotValid(string $preRelease)
    {
        new Version(10, 12, 14, $preRelease);
    }

    /**
     * @dataProvider             provideWrongPreReleaseVersion
     * @expectedException \SemVer\SemVer\Exception\InvalidArgumentException
     * @expectedExceptionMessage The build version is not compatible with rule 10 of the specifications.
     *
     * @param string $build
     */
    public function testConstructorFailsIfBuildNotValid(string $build)
    {
        new Version(10, 12, 14, '', $build);
    }

    /** @return array */
    public function provideWrongPreReleaseVersion() : array
    {
        return [
            ['é'],
            ['"'],
            ['~'],
            ['+'],
            ['+R'],
            ['R+'],
            ['.RC'],
            ['.'],
            ['RC.'],
        ];
    }

    ////////////////////////////////////////////////////////////////////////////
    // fromString()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @dataProvider             provideValidVersion
     *
     * @param string $string
     * @param int    $expectedMajor
     * @param int    $expectedMinor
     * @param int    $expectedPatch
     * @param string $expectedPreRelease
     * @param string $exceptedBuild
     */
    public function testFromString(
        string $string,
        int $expectedMajor,
        int $expectedMinor,
        int $expectedPatch,
        string $expectedPreRelease,
        string $exceptedBuild
    ) {
        $version = Version::fromString($string);
        static::assertEquals(
            $expectedMajor,
            $version->getMajor(),
            '__construct() takes the major version as its first argument'
        );
        static::assertEquals(
            $expectedMinor,
            $version->getMinor(),
            '__construct() takes the minor version as its second argument'
        );
        static::assertEquals(
            $expectedPatch,
            $version->getPatch(),
            '__construct() takes the patch version as its third argument'
        );
        static::assertEquals(
            $expectedPreRelease,
            $version->getPreRelease(),
            '__construct() takes the pre-release version as its forth argument'
        );
        static::assertEquals(
            $exceptedBuild,
            $version->getBuild(),
            '__construct() takes the build version as its fifth argument'
        );
    }

    /** @return array */
    public function provideValidVersion() : array
    {
        return [
            ['1.2.3', 1, 2, 3, '', ''],
            ['1.0.0-alpha', 1, 0, 0, 'alpha', ''],
            ['1.0.0-alpha.1', 1, 0, 0, 'alpha.1', ''],
            ['1.0.0-0.3.7', 1, 0, 0, '0.3.7', ''],
            ['1.0.0-x.7.z.92', 1, 0, 0, 'x.7.z.92', ''],
            ['1.0.0-alpha+001', 1, 0, 0, 'alpha', '001'],
            ['1.0.0+20130313144700', 1, 0, 0, '', '20130313144700'],
            ['1.0.0-beta+exp.sha.5114f85', 1, 0, 0, 'beta', 'exp.sha.5114f85'],
        ];
    }

    /**
     * @dataProvider                   provideWrongStringVersion
     * @expectedException \SemVer\SemVer\Exception\InvalidArgumentException
     * @expectedExceptionMessageRegExp /The string ".+" does not look like a version\./
     *
     * @param string $string
     */
    public function testFromStringFailsWithInvalidString(string $string)
    {
        Version::fromString($string);
    }

    /**
     * @return array
     */
    public function provideWrongStringVersion() : array
    {
        return [
            ['1'],
            ['a'],
            ['1.1'],
            ['1.a'],
            ['1.1.a'],
        ];
    }

    ////////////////////////////////////////////////////////////////////////////
    // sort()
    ////////////////////////////////////////////////////////////////////////////
    public function testSort()
    {
        $result = Version::sort(
            [
                Version::fromString('2.0.0'),
                Version::fromString('1.10.10'),
            ]
        );
        $expectedResult =
            [
                Version::fromString('1.10.10'),
                Version::fromString('2.0.0'),
            ];
        do {
            $shiftResult   = array_shift($result);
            $shiftExpected = array_shift($expectedResult);
            static::assertEquals(
                $shiftExpected->getMajor(),
                $shiftResult->getMajor(),
                '::sort() versions must be ordered by major version'
            );
            static::assertEquals(
                $shiftExpected->getMinor(),
                $shiftResult->getMinor(),
                '::sort() versions must be ordered by major version'
            );
            static::assertEquals(
                $shiftExpected->getPatch(),
                $shiftResult->getPatch(),
                '::sort() versions must be ordered by major version'
            );
            static::assertEquals(
                $shiftExpected->getPreRelease(),
                $shiftResult->getPreRelease(),
                '::sort() versions must be ordered by major version'
            );
        } while (count($result) || count($expectedResult));
    }

    ////////////////////////////////////////////////////////////////////////////
    // compare()
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
        static::assertEquals($expectedResult, $version1->compare($version2), $message);
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
                '::compare() versions must be ordered by major version (current lower than other)',
            ],
            [
                Version::fromString('2.0.0'),
                Version::fromString('1.0.0'),
                1,
                '::compare() versions must be ordered by major version (current greater than other)',
            ],
            [
                Version::fromString('2.0.0'),
                Version::fromString('10.0.0'),
                -1,
                '::compare() versions must be ordered by major version numerically',
            ],
            // minor
            [
                Version::fromString('2.10.0'),
                Version::fromString('2.0.0'),
                1,
                '::compare() if major versions are equals, then it must be ordered by minor version (current lower than other)',
            ],
            [
                Version::fromString('2.0.0'),
                Version::fromString('2.10.0'),
                -1,
                '::compare() if major versions are equals, then it must be ordered by minor version (current greater than other)',
            ],
            [
                Version::fromString('2.10.0'),
                Version::fromString('2.2.0'),
                1,
                '::compare() if major versions are equals, then it must be ordered by minor version numerically',
            ],
            // patch
            [
                Version::fromString('2.0.10'),
                Version::fromString('2.0.0'),
                1,
                '::compare() if major and minor versions are equals, then it must be ordered by patch version numerically (current lower than other)',
            ],
            [
                Version::fromString('2.0.0'),
                Version::fromString('2.0.10'),
                -1,
                '::compare() if major and minor versions are equals, then it must be ordered by patch version numerically (current greater than other)',
            ],
            [
                Version::fromString('2.0.10'),
                Version::fromString('2.0.2'),
                1,
                '::compare() if major and minor versions are equals, then it must be ordered by patch version numerically',
            ],
            [
                Version::fromString('2.0.0'),
                Version::fromString('2.0.0+build'),
                0,
                '::compare() if major, minor and patch versions are equals and both versions do not have pre-release, then they are equals',
            ],
            [
                Version::fromString('2.0.0'),
                Version::fromString('2.0.0-alpha'),
                1,
                '::compare() When major, minor, and patch are equal, a pre-release version has lower precedence than a normal version. (current without pre-release)',
            ],
            [
                Version::fromString('2.0.0-alpha'),
                Version::fromString('2.0.0'),
                -1,
                '::compare() A larger set of pre-release fields has a higher precedence than a smaller set',
            ],
            [
                Version::fromString('2.0.0-alpha.1'),
                Version::fromString('2.0.0-alpha'),
                1,
                '::compare() A larger set of pre-release fields has a higher precedence than a smaller set (multiple level)',
            ],
            [
                Version::fromString('2.0.0-1'),
                Version::fromString('2.0.0-beta'),
                -1,
                '::compare() Precedence for two pre-release versions with the same major, minor, and patch version. Numeric identifiers always have lower precedence than non-numeric identifiers',
            ],
            [
                Version::fromString('2.0.0-beta'),
                Version::fromString('2.0.0-1'),
                1,
                '::compare() Precedence for two pre-release versions with the same major, minor, and patch version. Numeric identifiers always have lower precedence than non-numeric identifiers',
            ],
            [
                Version::fromString('2.0.0-alpha.1'),
                Version::fromString('2.0.0-alpha.beta'),
                -1,
                '::compare() Precedence for two pre-release versions with the same major, minor, and patch version. Numeric identifiers always have lower precedence than non-numeric identifiers. Test with multiple identifiers level.',
            ],
            [
                Version::fromString('2.0.0-alpha.10'),
                Version::fromString('2.0.0-alpha.2'),
                1,
                '::compare() numeric pre-release, minor, and patch version. Numeric identifiers always have lower precedence than non-numeric identifiers. Test with multiple identifiers level.',
            ],
        ];
    }

    ////////////////////////////////////////////////////////////////////////////
    // __toString()
    ////////////////////////////////////////////////////////////////////////////
    public function testToString()
    {
        $data = [
            '1.0.0',
            '1.0.0-alpha.test',
            '1.0.0-alpha.test+build',
            '1.0.0+build',
        ];

        foreach ($data as $item) {
            static::assertEquals($item, (string) Version::fromString($item));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // greaterThan()
    ////////////////////////////////////////////////////////////////////////////
    public function testGreaterThan()
    {
        $current = new Version(1, 0, 0);
        $other   = new Version(1, 1, 0);
        static::assertEquals(false, $current->greaterThan($other));
        static::assertEquals(true, $other->greaterThan($current));
    }

    ////////////////////////////////////////////////////////////////////////////
    // greaterThanOrEqual()
    ////////////////////////////////////////////////////////////////////////////
    public function testGreaterThanOrEqual()
    {
        $current = new Version(1, 0, 0);
        $other   = new Version(1, 1, 0);
        static::assertEquals(false, $current->greaterThanOrEqual($other));
        static::assertEquals(true, $other->greaterThanOrEqual($current));
    }

    ////////////////////////////////////////////////////////////////////////////
    // lessThan()
    ////////////////////////////////////////////////////////////////////////////
    public function testLessThan()
    {
        $current = new Version(1, 0, 0);
        $other   = new Version(1, 1, 0);
        static::assertEquals(true, $current->lessThan($other));
        static::assertEquals(false, $other->lessThan($current));
    }

    ////////////////////////////////////////////////////////////////////////////
    // lessThanOrEqual()
    ////////////////////////////////////////////////////////////////////////////
    public function testLessThanOrEqual()
    {
        $current = new Version(1, 0, 0);
        $other   = new Version(1, 1, 0);
        static::assertEquals(true, $current->lessThanOrEqual($other));
        static::assertEquals(false, $other->lessThanOrEqual($current));
    }
}
