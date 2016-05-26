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
 * Compare two Version objects.
 */
final class VersionComparator
{
    /** @var Version */
    private $first;
    /** @var Version */
    private $second;

    /**
     * @param Version $first
     * @param Version $second
     */
    private function __construct(Version $first, Version $second)
    {
        $this->first  = $first;
        $this->second = $second;
    }

    /**
     * @param Version $first
     * @param Version $second
     *
     * @return int
     */
    public static function compare(Version $first, Version $second) : int
    {
        $obj = new self($first, $second);

        return $obj->doCompare();
    }

    /**
     * @return int
     */
    private function doCompare() : int
    {
        $compare = $this->compareNumericParts();
        if (0 !== $compare) {
            return $compare;
        }

        return $this->comparePreRelease();
    }

    /**
     * @return int
     */
    private function compareNumericParts() : int
    {
        $compare = $this->first->getMajor() <=> $this->second->getMajor();
        if (0 !== $compare) {
            return $compare;
        }
        $compare = $this->first->getMinor() <=> $this->second->getMinor();
        if (0 !== $compare) {
            return $compare;
        }
        $compare = $this->first->getPatch() <=> $this->second->getPatch();

        return $compare;
    }

    /**
     * @return int
     */
    private function comparePreRelease() : int
    {
        $preRelease1 = $this->first->getPreRelease();
        $preRelease2 = $this->second->getPreRelease();

        $leftPreReleaseIsEmpty  = '' === $preRelease1;
        $rightPreReleaseIsEmpty = '' === $preRelease2;
        if ($rightPreReleaseIsEmpty !== $leftPreReleaseIsEmpty) {
            return $leftPreReleaseIsEmpty ? 1 : -1;
        }

        if ($leftPreReleaseIsEmpty) {
            return 0;
        }

        return $this->comparePreReleaseIdentifiers(explode('.', $preRelease1), explode('.', $preRelease2));
    }

    /**
     * @param array $identifiers1
     * @param array $identifiers2
     *
     * @return int
     */
    private function comparePreReleaseIdentifiers(array $identifiers1, array $identifiers2) : int
    {
        $preReleasePart1 = array_shift($identifiers1);
        $preReleasePart2 = array_shift($identifiers2);
        if (null === $preReleasePart2) {
            return (int) (null !== $preReleasePart1);
        }

        if (null === $preReleasePart1) {
            return -1;
        }

        $compare = $this->comparePreReleaseIdentifier($preReleasePart1, $preReleasePart2);
        if (0 === $compare) {
            return $this->comparePreReleaseIdentifiers($identifiers1, $identifiers2);
        }

        return $compare;
    }

    /**
     * @param $identifier1
     * @param $identifier2
     *
     * @return int
     */
    private function comparePreReleaseIdentifier($identifier1, $identifier2) : int
    {
        $mineIsInt  = $this->isIdentifierInt($identifier1);
        $theirIsInt = $this->isIdentifierInt($identifier2);

        if ($mineIsInt !== $theirIsInt) {
            return $mineIsInt ? -1 : 1;
        }

        if ($mineIsInt) {
            return ((int) $identifier1) <=> ((int) $identifier2);
        }

        return $identifier1 <=> $identifier2;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    private function isIdentifierInt(string $identifier) : bool
    {
        return ctype_digit($identifier) && strpos($identifier, '00') !== 0;
    }
}
