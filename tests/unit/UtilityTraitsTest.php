<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\ParentTrait;
use cinghie\traits\SeoTrait;
use cinghie\traits\SequentialTrait;
use PHPUnit\Framework\TestCase;
use yii\base\Model;

final class UtilityTraitsTest extends TestCase
{
    protected function tearDown(): void
    {
        ParentTraitTestHost::$rows = [];
        parent::tearDown();
    }

    public function testRobotsOptionsUseStandardDirectives(): void
    {
        $this->assertSame([
            'index, follow' => 'index, follow',
            'noindex, nofollow' => 'noindex, nofollow',
            'noindex, follow' => 'noindex, follow',
            'index, nofollow' => 'index, nofollow',
        ], SeoTraitTestHost::getRobotsOptions());
    }

    public function testSequentialCodePadsAndAllowsOverflow(): void
    {
        $host = new SequentialTraitTestHost();

        $this->assertSame('A00000042', $host->generateSequentialCode(42));
        $this->assertSame('A123456789', $host->generateSequentialCode(123456789));
    }

    public function testParentRulesIncludeExistenceAndHierarchyValidation(): void
    {
        $rules = ParentTraitTestHost::rules();

        $this->assertSame('exist', $rules[1][1]);
        $this->assertSame(ParentTraitTestHost::class, $rules[1]['targetClass']);
        $this->assertSame(['parent_id' => 'id'], $rules[1]['targetAttribute']);
        $this->assertSame('validateParentHierarchy', $rules[2][1]);
    }

    public function testParentHierarchyAllowsEmptyParent(): void
    {
        $model = new ParentTraitTestHost();
        $model->id = 10;
        $model->parent_id = null;

        $model->validateParentHierarchy('parent_id');

        $this->assertFalse($model->hasErrors('parent_id'));
    }

    public function testParentHierarchyRejectsSelfParent(): void
    {
        $model = new ParentTraitTestHost();
        $model->id = 10;
        $model->parent_id = 10;

        $model->validateParentHierarchy('parent_id');

        $this->assertTrue($model->hasErrors('parent_id'));
    }

    public function testParentHierarchyRejectsAncestorCycle(): void
    {
        ParentTraitTestHost::$rows = [
            2 => ['id' => 2, 'parent_id' => 3],
            3 => ['id' => 3, 'parent_id' => 1],
        ];

        $model = new ParentTraitTestHost();
        $model->id = 1;
        $model->parent_id = 2;

        $model->validateParentHierarchy('parent_id');

        $this->assertTrue($model->hasErrors('parent_id'));
    }

    public function testParentHierarchyAllowsAcyclicAncestorChain(): void
    {
        ParentTraitTestHost::$rows = [
            2 => ['id' => 2, 'parent_id' => 3],
            3 => ['id' => 3, 'parent_id' => null],
        ];

        $model = new ParentTraitTestHost();
        $model->id = 1;
        $model->parent_id = 2;

        $model->validateParentHierarchy('parent_id');

        $this->assertFalse($model->hasErrors('parent_id'));
    }
}

final class SeoTraitTestHost
{
    use SeoTrait;
}

final class SequentialTraitTestHost
{
    use SequentialTrait;
}

final class ParentTraitTestHost extends Model
{
    use ParentTrait;

    /** @var array<int,array{id:int,parent_id:int|null}> */
    public static $rows = [];

    /** @var int|null */
    public $id;

    /** @var int|null */
    public $parent_id;

    public static function find()
    {
        return new ParentTraitFakeQuery(static::$rows);
    }
}

final class ParentTraitFakeQuery
{
    /** @var array */
    private $rows;

    /** @var int|null */
    private $id;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function select($columns)
    {
        return $this;
    }

    public function where($condition)
    {
        $this->id = isset($condition['id']) ? (int)$condition['id'] : null;
        return $this;
    }

    public function one()
    {
        if ($this->id === null || !isset($this->rows[$this->id])) {
            return null;
        }

        $row = $this->rows[$this->id];
        return (object)$row;
    }
}
