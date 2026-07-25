<?php

/**
 * Smoke checks for StateTrait BC across projects that do not register listeners.
 */
require dirname(__DIR__, 3) . '/autoload.php';
require dirname(__DIR__, 3) . '/yiisoft/yii2/Yii.php';

use cinghie\traits\StateTrait;
use yii\base\Component;
use yii\base\Event;

final class StateTraitHost extends Component
{
	use StateTrait;

	public $state = 0;
	public $id = 1;

	/** @var array */
	public $updated = [];

	public function updateAttributes($attributes)
	{
		$this->updated = $attributes;
		foreach ($attributes as $k => $v) {
			$this->$k = $v;
		}

		return 1;
	}
}

$model = new StateTraitHost();
$fired = 0;
// No class-level handlers registered: active/deactive must not throw.
$model->active();
if ($model->state !== 1) {
	fwrite(STDERR, "FAIL: state not set to 1\n");
	exit(1);
}
$model->deactive();
if ($model->state !== 0) {
	fwrite(STDERR, "FAIL: state not set to 0\n");
	exit(1);
}

Event::on(StateTraitHost::class, 'cinghie.traits.afterStateChange', static function () use (&$fired) {
	$fired++;
});
$model->active();
$model->deactive();
if ($fired !== 2) {
	fwrite(STDERR, "FAIL: expected 2 events, got {$fired}\n");
	exit(1);
}

echo "OK StateTrait BC smoke\n";
