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
    // major()
    ////////////////////////////////////////////////////////////////////////////
    public function testMajor()
    {
        $data = [
            '1.10.10'                  => '2.0.0',
            '1.10.10-alpha.test'       => '2.0.0',
            '1.10.10-alpha.test+build' => '2.0.0',
            '1.10.10+build'            => '2.0.0',
            '1.0.0+build'              => '2.0.0',
            '1.0.0-alpha'              => '1.0.0',
            '1.0.0-alpha+build'        => '1.0.0',
        ];

        foreach ($data as $currentVersion => $expectedVersion) {
            $current = Version::fromString($currentVersion);
            $result  = $current->major();
            static::assertEquals(Version::fromString($expectedVersion), $result);
            static::assertNotSame($current, $result, '::major() should create a new version.');
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // minor()
    ////////////////////////////////////////////////////////////////////////////
    public function testMinor()
    {
        $data = [
            '1.10.10'                  => '1.11.0',
            '1.10.10-alpha.test'       => '1.11.0',
            '1.10.10-alpha.test+build' => '1.11.0',
            '1.10.10+build'            => '1.11.0',
            '1.0.0+build'              => '1.1.0',
            '1.0.0-alpha'              => '1.0.0',
            '1.0.0-alpha+build'        => '1.0.0',
        ];

        foreach ($data as $currentVersion => $expectedVersion) {
            $current = Version::fromString($currentVersion);
            $result  = $current->minor();
            static::assertEquals(Version::fromString($expectedVersion), $result);
            static::assertNotSame($current, $result, '::minor() should create a new version.');
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // patch()
    ////////////////////////////////////////////////////////////////////////////
    public function testPatch()
    {
        $data = [
            '1.10.10'                  => '1.10.11',
            '1.10.10+build'            => '1.10.11',
            '1.10.10-alpha.test'       => '1.10.10',
            '1.10.10-alpha.test+build' => '1.10.10',
        ];

        foreach ($data as $currentVersion => $expectedVersion) {
            $current = Version::fromString($currentVersion);
            $result  = $current->patch();
            static::assertEquals(Version::fromString($expectedVersion), $result);
            static::assertNotSame($current, $result, '::patch() should create a new version.');
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // isEquals()
    ////////////////////////////////////////////////////////////////////////////
    public function testIsEquals()
    {
        $current = new Version(1, 0, 0);
        $other   = new Version(1, 0, 0);
        static::assertEquals(true, $other->isEquals($current));
        $current = new Version(1, 1, 0);
        $other   = new Version(1, 0, 0);
        static::assertEquals(false, $other->isEquals($current));
    }

    ////////////////////////////////////////////////////////////////////////////
    // isGreaterThan()
    ////////////////////////////////////////////////////////////////////////////
    public function testIsGreaterThan()
    {
        $current = new Version(1, 0, 0);
        $other   = new Version(1, 1, 0);
        static::assertEquals(false, $current->isGreaterThan($other));
        static::assertEquals(true, $other->isGreaterThan($current));
    }

    ////////////////////////////////////////////////////////////////////////////
    // isGreaterThanOrEqual()
    ////////////////////////////////////////////////////////////////////////////
    public function testIsGreaterThanOrEqual()
    {
        $current = new Version(1, 0, 0);
        $other   = new Version(1, 1, 0);
        static::assertEquals(false, $current->isGreaterThanOrEqual($other));
        static::assertEquals(true, $other->isGreaterThanOrEqual($current));
    }

    ////////////////////////////////////////////////////////////////////////////
    // isLessThan()
    ////////////////////////////////////////////////////////////////////////////
    public function testIsLessThan()
    {
        $current = new Version(1, 0, 0);
        $other   = new Version(1, 1, 0);
        static::assertEquals(true, $current->isLessThan($other));
        static::assertEquals(false, $other->isLessThan($current));
    }

    ////////////////////////////////////////////////////////////////////////////
    // isLessThanOrEqual()
    ////////////////////////////////////////////////////////////////////////////
    public function testIsLessThanOrEqual()
    {
        $current = new Version(1, 0, 0);
        $other   = new Version(1, 1, 0);
        static::assertEquals(true, $current->isLessThanOrEqual($other));
        static::assertEquals(false, $other->isLessThanOrEqual($current));
    }
}
