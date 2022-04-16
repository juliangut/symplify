<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Config;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Webmozart\Assert\Assert;

/**
 * @api
 */
final class ECSConfig extends ContainerConfigurator
{
    /**
     * @param string[] $paths
     */
    public function paths(array $paths): void
    {
        Assert::allString($paths);

        $parameters = $this->parameters();
        $parameters->set(Option::PATHS, $paths);
    }

    /**
     * @param mixed[] $skips
     */
    public function skip(array $skips): void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::SKIP, $skips);
    }

    /**
     * @param string[] $sets
     */
    public function sets(array $sets): void
    {
        Assert::allString($sets);
        Assert::allFileExists($sets);

        foreach ($sets as $set) {
            $this->import($set);
        }
    }

    /**
     * @param class-string<Sniff|FixerInterface> $checkerClass
     */
    public function rule(string $checkerClass): void
    {
        $this->isCheckerClass($checkerClass);

        $services = $this->services();
        $services->set($checkerClass);
    }

    /**
     * @param class-string $checkerClass
     * @param mixed[] $configuration
     */
    public function ruleWithConfiguration(string $checkerClass, array $configuration): void
    {
        $this->isCheckerClass($checkerClass);

        $services = $this->services();

        $service = $services->set($checkerClass);
        if (is_a($checkerClass, FixerInterface::class, true)) {
            Assert::isAnyOf($checkerClass, [ConfigurableFixerInterface::class, ConfigurableRuleInterface::class]);

            $service->call('configure', [$configuration]);
        }

        if (is_a($checkerClass, Sniff::class, true)) {
            foreach ($configuration as $propertyName => $value) {
                Assert::propertyExists($checkerClass, $propertyName);

                $service->property($propertyName, $value);
            }
        }
    }

    /**
     * @param class-string $checkerClass
     */
    private function isCheckerClass(string $checkerClass): void
    {
        Assert::isAnyOf($checkerClass, [Sniff::class, FixerInterface::class]);
    }
}
