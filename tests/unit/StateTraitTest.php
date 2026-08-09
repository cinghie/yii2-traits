<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\StateTrait;
use PHPUnit\Framework\TestCase;
use yii\base\Component;
use yii\base\Event;

final class StateTraitTest extends TestCase
{
	protected function tearDown(): void
	{
		Event::off(StateTraitTestHost::class, 'cinghie.traits.afterStateChange');
		parent::tearDown();
	}

	public function testStateChangesWorkWithoutListeners(): void
	{
		$model = new StateTraitTestHost();

		$this->assertTrue($model->active());
		$this->assertSame(1, $model->state);
		$this->assertTrue($model->deactive());
		$this->assertSame(0, $model->state);
	}

	public function testStateChangesRaiseRegisteredEvent(): void
	{
		$model = new StateTraitTestHost();
		$fired = 0;

		Event::on(StateTraitTestHost::class, 'cinghie.traits.afterStateChange', static function () use (&$fired): void {
			$fired++;
		});

		$model->active();
		$model->deactive();

		$this->assertSame(2, $fired);
	}
}

final class StateTraitTestHost extends Component
{
	use StateTrait;

	/** @var int */
	public $state = 0;

	/** @var int */
	public $id = 1;

	/** @var array */
	public $updated = [];

	public function updateAttributes($attributes)
	{
		$this->updated = $attributes;

		foreach ($attributes as $key => $value) {
			$this->$key = $value;
		}

		return 1;
	}
}
