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

	public function testViewButtonsDisableTheCurrentStateAction(): void
	{
		$model = new StateTraitTestHost();
		$model->state = 1;
		$active = $model->getStateActionActiveButton(7, 'Active');
		$deactive = $model->getStateActionDeactiveButton(7, 'Deactive');

		$this->assertStringContainsString('disabled', $active);
		$this->assertStringContainsString('return false', $active);
		$this->assertStringNotContainsString('changestate', $active);
		$this->assertStringContainsString('changestate', $deactive);
		$this->assertStringNotContainsString('disabled', $deactive);

		$model->state = 0;
		$active = $model->getStateActionActiveButton(7, 'Active');
		$deactive = $model->getStateActionDeactiveButton(7, 'Deactive');
		$this->assertStringContainsString('changestate', $active);
		$this->assertStringContainsString('disabled', $deactive);
	}

	public function testIndexToolbarButtonsStayEnabledWithoutRecordId(): void
	{
		$model = new StateTraitTestHost();
		$model->state = 1;
		$active = $model->getStateActionActiveButton(0, 'Active');
		$this->assertStringNotContainsString('disabled', $active);
		$this->assertStringNotContainsString('changestate', $active);
	}

	public function testStateActionButtonsAreNoOpWithoutViewsHelpersTrait(): void
	{
		$model = new StateTraitSmokeHost();
		$model->state = 1;

		$this->assertSame('', $model->getStateActionActiveButton(7));
		$this->assertSame('', $model->getStateActionDeactiveButton(7));
		$this->assertTrue($model->deactive());
		$this->assertSame(0, $model->state);
	}
}

final class StateTraitSmokeHost extends Component
{
	use StateTrait;

	/** @var int */
	public $state = 0;

	public function updateAttributes($attributes)
	{
		foreach ($attributes as $key => $value) {
			$this->$key = $value;
		}

		return 1;
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

	public function getStandardButton($icon, $title, $url, array $aClass = [])
	{
		$href = is_array($url) ? json_encode($url) : (string) $url;
		$class = (string) ($aClass['class'] ?? '');
		$onclick = (string) ($aClass['onclick'] ?? '');
		$method = isset($aClass['data']['method']) ? (string) $aClass['data']['method'] : '';

		return $class . ' ' . $href . ' ' . $onclick . ' ' . $method . ' ' . $title;
	}
}
