<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteInjectAndConstructorRule\Fixture;

final class InjectPropertyAndConstructor
{
    /**
     * @inject
     */
    public $name;

    public function __construct()
    {
        $this->name = 'hey';
    }
}
