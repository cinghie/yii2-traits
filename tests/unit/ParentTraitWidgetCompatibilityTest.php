<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\ParentTrait;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ParentTraitWidgetCompatibilityTest extends TestCase
{
    public function testParentWidgetKeepsLegacyArgumentsAndAddsOptionalOptions(): void
    {
        $method = new ReflectionMethod(ParentTraitFixture::class, 'getParentWidget');

        $this->assertSame(3, $method->getNumberOfParameters());
        $this->assertSame(1, $method->getNumberOfRequiredParameters());
        $this->assertTrue($method->getParameters()[1]->isDefaultValueAvailable());
        $this->assertSame([], $method->getParameters()[1]->getDefaultValue());
        $this->assertTrue($method->getParameters()[2]->isDefaultValueAvailable());
        $this->assertSame([], $method->getParameters()[2]->getDefaultValue());
    }

    public function testParentWidgetMergesConsumerSelect2Options(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/ParentTrait.php');

        $this->assertStringContainsString('ArrayHelper::merge($options, $widgetOptions)', $source);
        $this->assertStringContainsString("'data' => \$items", $source);
    }
}

class ParentTraitFixture
{
    use ParentTrait;
}
