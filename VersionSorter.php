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

namespace SemVer\SemVer;

/**
 * Sort multiple Version objects.
 */
final class VersionSorter
{
    /**
     * @param array $versions
     *
     * @return array
     */
    public static function sort(array $versions) : array
    {
        usort(
            $versions,
            function (Version $a, Version $b) {
                return VersionComparator::compare($a, $b);
            }
        );

        return $versions;
    }
}
