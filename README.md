Yii2 Traits
------------

![License](https://img.shields.io/packagist/l/cinghie/yii2-traits.svg)
![Latest Stable Version](https://img.shields.io/github/release/cinghie/yii2-traits.svg)
![Latest Release Date](https://img.shields.io/github/release-date/cinghie/yii2-traits.svg)
![Latest Commit](https://img.shields.io/github/last-commit/cinghie/yii2-traits.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/cinghie/yii2-traits.svg)](https://packagist.org/packages/cinghie/yii2-traits)

Yii2 Traits is a library of reusable traits designed to reduce duplicated code across Yii2 modules. It provides shared model rules and labels, UI helpers, attachment/media helpers, hierarchy and ordering helpers, cache actions, user helpers and other common functionality.

## Requirements

- PHP 8.1 or later
- Yii 2.0.55 or later

`cinghie/yii2-traits` is a shared runtime foundation for other Cinghie modules. Integrations used by its traits are therefore required Composer dependencies and are installed automatically with the package.

### Required runtime dependencies

| Package | Used for |
| --- | --- |
| `2amigos/yii2-ckeditor-widget` | CKEditor support in `EditorTrait` |
| `2amigos/yii2-taggable-behavior` | `TaggableTrait` |
| `2amigos/yii2-tinymce-widget` | TinyMCE support in `EditorTrait` |
| `cinghie/yii2-user-extended` | User helper integrations |
| `cocur/slugify` | Slug generation in `NameAliasTrait` and `TitleAliasTrait` |
| `dektrium/yii2-user` | User relations in `CreatedTrait`, `ModifiedTrait` and `UserTrait` |
| `google/cloud-translate` | Google Cloud Translation V3 in `GoogleTranslateTrait` |
| `james-heinrich/getid3` | Attachment and media metadata |
| `kartik-v/yii2-detail-view` | DetailView helpers |
| `kartik-v/yii2-helpers` | Kartik HTML helpers |
| `kartik-v/yii2-markdown` | Markdown editor support |
| `kartik-v/yii2-mpdf` | `PDFTrait` |
| `kartik-v/yii2-social` | `SocialTrait` |
| `kartik-v/yii2-widgets` | Form widgets such as Select2, DateTimePicker and FileInput |
| `php-ffmpeg/php-ffmpeg` | Video thumbnail generation |
| `vova07/yii2-imperavi-widget` | Imperavi support in `EditorTrait` |

Some required integrations are legacy/abandoned upstream. They currently remain part of the runtime contract for compatibility with Cinghie modules and should be migrated in a future major/minor compatibility effort.

## Installation

Install the package with Composer:

```
composer require cinghie/yii2-traits:^1.3
```

Composer installs the complete runtime dependency set automatically.

## Configuration

### Translations

Add the translation source to your application configuration:

```php
'components' => [
    'i18n' => [
        'translations' => [
            'traits' => [
                'class' => 'yii\\i18n\\PhpMessageSource',
                'basePath' => '@vendor/cinghie/yii2-traits/messages',
            ],
        ],
    ],
],
```

### Runtime configuration

Attachment paths, editor selection, FFmpeg binaries and Google Translate settings can be supplied through `Yii::$app->params['yii2Traits']` (or an equivalent host `getTraitsConfig()` configuration). Legacy module-level values remain supported where applicable.

Example:

```php
'params' => [
    'yii2Traits' => [
        'attachPath' => '@webroot/uploads/',
        'attachURL' => '@web/uploads/',
        'editor' => 'ckeditor',
        'ffmpegBinary' => '/usr/bin/ffmpeg',
        'ffprobeBinary' => '/usr/bin/ffprobe',
        'googleTranslateProjectId' => 'your-google-cloud-project-id',
    ],
],
```

`GoogleTranslateTrait` uses Google Cloud Translation V3 and Application Default Credentials (ADC). Configure credentials in the runtime environment, for example with `GOOGLE_APPLICATION_CREDENTIALS`. The legacy `$apiKey` method argument is retained only for API compatibility and is not used for V3 authentication.

If Markdown support from `EditorTrait` is used, configure the Kartik Markdown module:

```php
'modules' => [
    'markdown' => [
        'class' => 'kartik\\markdown\\Module',
    ],
],
```

No Kartik Grid module is required by `yii2-traits` itself.

## Model rules and labels

Trait rules and labels follow Yii2's instance-method contract. Merge the trait-specific helpers from the model's `rules()` and `attributeLabels()` methods:

```php
class YourModel extends yii\\db\\ActiveRecord
{
    use \\cinghie\\traits\\CreatedTrait;
    use \\cinghie\\traits\\ModifiedTrait;

    public function rules()
    {
        return array_merge(
            parent::rules(),
            $this->getCreatedRules(),
            $this->getModifiedRules()
        );
    }

    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            $this->getCreatedAttributeLabels(),
            $this->getModifiedAttributeLabels()
        );
    }
}
```

Do not call trait `rules()` or `attributeLabels()` statically. Model-facing traits expose `get*Rules()` and `get*AttributeLabels()` helpers for composition.

## Security-sensitive traits

### AttachmentTrait

Attachment deletion is constrained to the configured attachment directory. Generated attachment filenames use secure random values while the legacy `generateMd5FileName()` method name is preserved for backward compatibility. FFmpeg/FFprobe paths should be configured explicitly when media thumbnail generation is used.

### CacheTrait

Cache mutation actions are administrative operations. They require POST requests and authorization through the host controller/application. Keep Yii CSRF validation enabled for controllers exposing these actions and configure the required cache-management permission/RBAC policy.

### OrderingTrait

Ordering updates run transactionally. Models should pass an ActiveRecord-compatible class and use the trait helper methods through the host model.

### ParentTrait

`getParent()` returns the direct parent. `getParents()` and `getAncestors()` represent the ancestor chain, nearest parent first. Parent validation prevents missing parents, self-parenting and hierarchy cycles.

## Main traits

- `AccessTrait` — access state helpers and widgets.
- `AddressTrait` — address fields and geocoding helper.
- `AttachmentTrait` — attachment metadata, filesystem and media helpers.
- `CacheTrait` — cache inspection and administrative invalidation actions.
- `CreatedTrait` / `ModifiedTrait` — audit timestamps and user relations.
- `EditorTrait` — CKEditor, TinyMCE, Imperavi and Markdown integration.
- `GoogleTranslateTrait` — Google Cloud Translation V3 integration.
- `ImageTrait` / `VideoTrait` — media fields and UI helpers.
- `LanguageTrait` — language selection helpers.
- `NameAliasTrait` / `TitleAliasTrait` — slug generation and alias widgets.
- `OrderingTrait` — transactional scoped ordering.
- `ParentTrait` — parent relation, validation and ancestor traversal.
- `PDFTrait` — PDF integration.
- `SeoTrait` — SEO metadata and robots directives.
- `SequentialTrait` — sequential code generation.
- `SocialTrait` — social helpers.
- `StateTrait` — active/inactive state helpers.
- `TaggableTrait` — tag behavior helpers.
- `UserTrait` / `UserHelpersTrait` — user relations and user helper methods.
- `ViewsHelperTrait` — common view/action helpers.

## Compatibility notes

- PHP 7.4 and PHP 8.0 are no longer supported; the runtime baseline is PHP 8.1.
- Runtime integrations listed in `composer.json` are required and installed automatically.
- Google Translate uses the V3 SDK with ADC and a configured `googleTranslateProjectId`.
- Static trait `rules()` / `attributeLabels()` APIs are not supported; use the `get*Rules()` / `get*AttributeLabels()` helpers.
- Some legacy dependency packages are abandoned upstream even though Composer currently reports no known security advisories for the resolved release dependency set.

## Testing

The project CI validates PHP 8.1 through PHP 8.5 with Composer validation, dependency installation/audit, PHP syntax checks, PHPUnit, standalone smoke tests and required-runtime dependency smoke tests.
