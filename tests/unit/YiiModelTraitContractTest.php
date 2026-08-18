<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\OrderingTrait;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use yii\base\Model;

final class YiiModelTraitContractTest extends TestCase
{
    public function testSourceTraitsDoNotDeclareStaticYiiModelMethods(): void
    {
        $root = dirname(__DIR__, 2);
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
        $files = new RegexIterator($iterator, '/Trait\.php$/i');
        $violations = [];

        foreach ($files as $file) {
            $path = $file->getPathname();
            if (strpos($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) {
                continue;
            }

            $source = file_get_contents($path);
            foreach (['rules', 'attributeLabels'] as $method) {
                if (preg_match('/public\s+static\s+function\s+' . preg_quote($method, '/') . '\s*\(/i', $source)) {
                    $violations[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path) . '::' . $method . '()';
                }
            }
        }

        $this->assertSame([], $violations, 'Yii model methods must be instance methods; use trait-specific get* helpers instead: ' . implode(', ', $violations));
    }

    public function testOrderingTraitComposesWithYiiModel(): void
    {
        $model = new YiiModelTraitContractHost();

        $this->assertSame([[['ordering'], 'integer']], $model->getOrderingRules());
        $this->assertSame(['ordering' => 'Ordering'], $model->getOrderingAttributeLabels());
        $this->assertSame([], $model->rules());
        $this->assertSame([], $model->attributeLabels());
    }
}

final class YiiModelTraitContractHost extends Model
{
    use OrderingTrait;

    public $ordering;
}
