<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Privatization\NoPublicPropertyByTypeRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Privatization\NoPublicPropertyByTypeRule\Source\NoPublicPropertiesInterface;

final class SkipNoPublicProperties implements NoPublicPropertiesInterface
{
    private $name;
}