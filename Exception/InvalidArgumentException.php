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

namespace SemVer\SemVer\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;

/**
 * Exception thrown if an argument does not match with the expected value.
 *
 * @link http://php.net/manual/en/class.invalidargumentexception.php
 */
final class InvalidArgumentException extends BaseInvalidArgumentException implements SemVerExceptionInterface
{
}
