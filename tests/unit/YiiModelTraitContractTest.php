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
        $root = dirname(__DIR__, 2);
        $missing = [];

        foreach (self::HELPER_PAIRS as $file => [$rulesMethod, $labelsMethod]) {
            $source = file_get_contents($root . DIRECTORY_SEPARATOR . $file);
            foreach ([$rulesMethod, $labelsMethod] as $method) {
                if (!preg_match('/public\s+function\s+' . preg_quote($method, '/') . '\s*\(/', $source)) {
                    $missing[] = $file . '::' . $method . '()';
                }
            }
        }

        $this->assertSame([], $missing, 'Missing trait composition helpers: ' . implode(', ', $missing));
    }

    public function testTraitHelpersHaveConciseDocblocks(): void
    {
        $root = dirname(__DIR__, 2);
        $invalid = [];

        foreach (self::HELPER_PAIRS as $file => $methods) {
            $source = file_get_contents($root . DIRECTORY_SEPARATOR . $file);
            foreach ($methods as $method) {
                $pattern = '/(\/\*\*.*?\*\/)\s*public\s+function\s+' . preg_quote($method, '/') . '\s*\(/s';
                if (!preg_match($pattern, $source, $matches)) {
                    $invalid[] = $file . '::' . $method . '() missing docblock';
                    continue;
                }

                if (substr_count($matches[1], "\n") > 6) {
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
}

final class YiiModelTraitContractHost extends Model
{
    use OrderingTrait;

    public $ordering;
}
