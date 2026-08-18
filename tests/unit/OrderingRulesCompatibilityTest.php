<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\OrderingTrait;
use PHPUnit\Framework\TestCase;
use yii\base\Model;

final class OrderingRulesCompatibilityTest extends TestCase
{
    public function testTraitCanBeUsedByYiiModelWithoutRulesSignatureConflict(): void
    {
        $model = new OrderingRulesCompatibilityHost();

        $this->assertSame([[['ordering'], 'integer']], $model->getOrderingRules());
        $this->assertSame([], $model->rules());
    }
}

final class OrderingRulesCompatibilityHost extends Model
{
    use OrderingTrait;

    public $ordering;
}
