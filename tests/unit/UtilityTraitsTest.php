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

    public function testParentRulesNormalizeEmptySelectionBeforeExistenceValidation(): void
    {
        $rules = (new ParentTraitTestHost())->getParentRules();
        $this->assertSame('filter', $rules[0][1]);
        $filter = $rules[0]['filter'];
        $this->assertNull($filter('0'));
        $this->assertNull($filter(0));
        $this->assertNull($filter(''));
        $this->assertSame(7, $filter(7));

        $existRule = $this->findRuleByValidator($rules, 'exist');
        $hierarchyRule = $this->findRuleByValidator($rules, 'validateParentHierarchy');
        $this->assertNotNull($existRule);
        $this->assertSame(ParentTraitTestHost::class, $existRule['targetClass']);
        $this->assertSame(['parent_id' => 'id'], $existRule['targetAttribute']);
        $this->assertNotNull($hierarchyRule);
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
        ParentTraitTestHost::$rows = [2 => ['id' => 2, 'parent_id' => 3], 3 => ['id' => 3, 'parent_id' => 1]];
        $model = new ParentTraitTestHost();
        $model->id = 1;
        $model->parent_id = 2;
        $model->validateParentHierarchy('parent_id');
        $this->assertTrue($model->hasErrors('parent_id'));
    }

    public function testParentHierarchyAllowsAcyclicAncestorChain(): void
    {
        ParentTraitTestHost::$rows = [2 => ['id' => 2, 'parent_id' => 3], 3 => ['id' => 3, 'parent_id' => null]];
        $model = new ParentTraitTestHost();
        $model->id = 1;
        $model->parent_id = 2;
        $model->validateParentHierarchy('parent_id');
        $this->assertFalse($model->hasErrors('parent_id'));
    }

    public function testParentsReturnsNearestFirstAncestorChain(): void
    {
        ParentTraitTestHost::$rows = [
            2 => ['id' => 2, 'parent_id' => 3],
            3 => ['id' => 3, 'parent_id' => 4],
            4 => ['id' => 4, 'parent_id' => null],
        ];
        $model = new ParentTraitTestHost();
        $model->id = 1;
        $model->parent_id = 2;
        $parentIds = array_map(static function ($parent) { return (int)$parent->id; }, $model->getParents());
        $ancestorIds = array_map(static function ($parent) { return (int)$parent->id; }, $model->getAncestors());
        $this->assertSame([2, 3, 4], $parentIds);
        $this->assertSame($parentIds, $ancestorIds);
    }

    public function testAncestorsStopsAtSafetyLimit(): void
    {
        ParentTraitTestHost::$rows = [
            2 => ['id' => 2, 'parent_id' => 3],
            3 => ['id' => 3, 'parent_id' => 4],
            4 => ['id' => 4, 'parent_id' => null],
        ];
        $model = new ParentTraitTestHost();
        $model->parent_id = 2;
        $this->assertCount(2, $model->getAncestors(2));
    }

    private function findRuleByValidator(array $rules, string $validator): ?array
    {
        foreach ($rules as $rule) {
            if (isset($rule[1]) && $rule[1] === $validator) { return $rule; }
        }
        return null;
    }
}

final class SeoTraitTestHost { use SeoTrait; }
final class SequentialTraitTestHost { use SequentialTrait; }

final class ParentTraitTestHost extends Model
{
    use ParentTrait;
    public static $rows = [];
    public $id;
    public $parent_id;
    public static function find() { return new ParentTraitFakeQuery(static::$rows); }
}

final class ParentTraitFakeQuery
{
    private $rows;
    private $id;
    public function __construct(array $rows) { $this->rows = $rows; }
    public function select($columns) { return $this; }
    public function where($condition) { $this->id = isset($condition['id']) ? (int)$condition['id'] : null; return $this; }
    public function one()
    {
        if ($this->id === null || !isset($this->rows[$this->id])) { return null; }
        return (object)$this->rows[$this->id];
    }
}
