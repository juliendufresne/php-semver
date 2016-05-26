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
use SemVer\SemVer\VersionSorter;

/**
 * Test VersionSorter class.
 */
final class VersionSorterTest extends PHPUnit_Framework_TestCase
{
    ////////////////////////////////////////////////////////////////////////////
    // ::sort()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @dataProvider provideVersions
     *
     * @param array $versions
     * @param array $expectedResult
     */
    public function testSort(array $versions, array $expectedResult)
    {
        $result = VersionSorter::sort($versions);

        static::assertCount(count($versions), $result, 'sort result should contain every items.');
        static::assertCount(count($expectedResult), $result);

        do {
            $version         = array_shift($result);
            $expectedVersion = array_shift($expectedResult);
            static::assertEquals(
                $expectedVersion->getMajor(),
                $version->getMajor(),
                '::sort() versions must be ordered by major version'
            );
            static::assertEquals(
                $expectedVersion->getMinor(),
                $version->getMinor(),
                '::sort() versions must be ordered by major version'
            );
            static::assertEquals(
                $expectedVersion->getPatch(),
                $version->getPatch(),
                '::sort() versions must be ordered by major version'
            );
            static::assertEquals(
                $expectedVersion->getPreRelease(),
                $version->getPreRelease(),
                '::sort() versions must be ordered by major version'
            );
        } while (count($result));
    }

    /**
     * @return array
     */
    public function provideVersions() : array
    {
        return [
            [
                [
                    Version::fromString('2.0.0'),
                    Version::fromString('1.2.3'),
                    Version::fromString('1.3.3'),
                    Version::fromString('1.3.3-alpha.10'),
                    Version::fromString('1.3.3-alpha.2'),
                    Version::fromString('1.2.3-rc.1+exp.sha.5114f85'),
                ],
                [
                    Version::fromString('1.2.3-rc.1+exp.sha.5114f85'),
                    Version::fromString('1.2.3'),
                    Version::fromString('1.3.3-alpha.2'),
                    Version::fromString('1.3.3-alpha.10'),
                    Version::fromString('1.3.3'),
                    Version::fromString('2.0.0'),
                ],
            ],
        ];
    }
}
