<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\OrderingTrait;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;
use yii\base\Model;

final class YiiModelTraitContractTest extends TestCase
{
    private const HELPER_PAIRS = [
        'AccessTrait.php' => ['getAccessRules', 'getAccessAttributeLabels'],
        'AddressTrait.php' => ['getAddressRules', 'getAddressAttributeLabels'],
        'AttachmentTrait.php' => ['getAttachmentRules', 'getAttachmentAttributeLabels'],
        'CreatedTrait.php' => ['getCreatedRules', 'getCreatedAttributeLabels'],
        'FatturazioneElettronicaTrait.php' => ['getFatturazioneElettronicaRules', 'getFatturazioneElettronicaAttributeLabels'],
        'ImageTrait.php' => ['getImageRules', 'getImageAttributeLabels'],
        'LanguageTrait.php' => ['getLanguageRules', 'getLanguageAttributeLabels'],
        'ModifiedTrait.php' => ['getModifiedRules', 'getModifiedAttributeLabels'],
        'NameAliasTrait.php' => ['getNameAliasRules', 'getNameAliasAttributeLabels'],
        'OrderingTrait.php' => ['getOrderingRules', 'getOrderingAttributeLabels'],
        'ParentTrait.php' => ['getParentRules', 'getParentAttributeLabels'],
        'SeoTrait.php' => ['getSeoRules', 'getSeoAttributeLabels'],
        'SocialTrait.php' => ['getSocialRules', 'getSocialAttributeLabels'],
        'StateTrait.php' => ['getStateRules', 'getStateAttributeLabels'],
        'TaggableTrait.php' => ['getTaggableRules', 'getTaggableAttributeLabels'],
        'TitleAliasTrait.php' => ['getTitleAliasRules', 'getTitleAliasAttributeLabels'],
        'UserTrait.php' => ['getUserRules', 'getUserAttributeLabels'],
        'VideoTrait.php' => ['getVideoRules', 'getVideoAttributeLabels'],
    ];

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

    public function testEveryRemovedYiiModelMethodHasTraitHelpers(): void
    {
        $invalid = [];

        foreach (self::HELPER_PAIRS as $file => $methods) {
            $reflection = $this->traitReflection($file);
            foreach ($methods as $method) {
                if (!$reflection->hasMethod($method)) {
                    $invalid[] = $file . '::' . $method . '() missing';
                    continue;
                }

                $methodReflection = $reflection->getMethod($method);
                if (!$methodReflection->isPublic()) {
                    $invalid[] = $file . '::' . $method . '() must be public';
                }
                if ($methodReflection->isStatic()) {
                    $invalid[] = $file . '::' . $method . '() must be an instance method';
                }
            }
        }

        $this->assertSame([], $invalid, 'Invalid trait composition helpers: ' . implode(', ', $invalid));
    }

    public function testTraitHelpersHaveConciseDocblocks(): void
    {
        $invalid = [];

        foreach (self::HELPER_PAIRS as $file => $methods) {
            $reflection = $this->traitReflection($file);
            foreach ($methods as $method) {
                if (!$reflection->hasMethod($method)) {
                    continue;
                }

                $docblock = $reflection->getMethod($method)->getDocComment();
                if ($docblock === false) {
                    $invalid[] = $file . '::' . $method . '() missing docblock';
                    continue;
                }

                if (substr_count($docblock, "\n") > 6) {
                    $invalid[] = $file . '::' . $method . '() docblock is too long';
                }
            }
        }

        $this->assertSame([], $invalid, 'Trait helper documentation issues: ' . implode(', ', $invalid));
    }

    public function testOrderingTraitComposesWithYiiModel(): void
    {
        $model = new YiiModelTraitContractHost();

        $this->assertSame([[['ordering'], 'integer']], $model->getOrderingRules());
        $this->assertArrayHasKey('ordering', $model->getOrderingAttributeLabels());
        $this->assertSame([], $model->rules());
        $this->assertSame([], $model->attributeLabels());
    }

    private function traitReflection(string $file): ReflectionClass
    {
        $class = 'cinghie\\traits\\' . substr($file, 0, -4);
        $reflection = new ReflectionClass($class);
        $this->assertTrue($reflection->isTrait(), $class . ' must remain a trait.');

        return $reflection;
    }
}

final class YiiModelTraitContractHost extends Model
{
    use OrderingTrait;

    public $ordering;
}
