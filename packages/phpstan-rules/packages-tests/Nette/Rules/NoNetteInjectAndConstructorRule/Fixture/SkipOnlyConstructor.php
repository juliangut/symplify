<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteInjectAndConstructorRule\Fixture;

final class SkipOnlyConstructor
{
    /**
     * @var string
     */
    private $name;

    public function __construct()
    {
        $this->name = 'hey';
    }
}
