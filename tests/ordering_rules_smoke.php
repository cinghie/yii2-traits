<?php

require dirname(__DIR__) . '/vendor/autoload.php';

class OrderingRulesSmokeModel extends yii\base\Model
{
    use cinghie\traits\OrderingTrait;

    public $ordering;
}

$model = new OrderingRulesSmokeModel();

if ($model->getOrderingRules() !== [[['ordering'], 'integer']]) {
    fwrite(STDERR, "OrderingTrait rules helper failed.\n");
    exit(1);
}

echo "OrderingTrait rules compatibility smoke test passed.\n";
