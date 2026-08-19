Yii2 Traits
------------

![License](https://img.shields.io/packagist/l/cinghie/yii2-traits.svg)
![Latest Stable Version](https://img.shields.io/github/release/cinghie/yii2-traits.svg)
![Latest Release Date](https://img.shields.io/github/release-date/cinghie/yii2-traits.svg)
![Latest Commit](https://img.shields.io/github/last-commit/cinghie/yii2-traits.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/cinghie/yii2-traits.svg)](https://packagist.org/packages/cinghie/yii2-traits)

Yii2 Traits is a library of reusable traits designed to reduce duplicated code across Yii2 modules. It provides shared model attributes, validation rules and labels, UI helpers, attachment/media helpers, hierarchy and ordering helpers, cache actions, user helpers and other common functionality.

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

Some required integrations are legacy or abandoned upstream. They remain part of the runtime contract for compatibility with Cinghie modules and should be migrated in a future compatibility effort.

## Installation

Install the package with Composer:

```bash
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
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vendor/cinghie/yii2-traits/messages',
            ],
        ],
    ],
],
```

### Runtime configuration

Attachment paths, editor selection, FFmpeg binaries and Google Translate settings can be supplied through `Yii::$app->params['yii2Traits']` or an equivalent host `getTraitsConfig()` configuration. Legacy module-level values remain supported where applicable.

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

`GoogleTranslateTrait` uses Google Cloud Translation V3 and Application Default Credentials (ADC). Configure credentials in the runtime environment, for example with `GOOGLE_APPLICATION_CREDENTIALS`. The legacy `$apiKey` method argument is retained for API compatibility and is not used for V3 authentication.

If Markdown support from `EditorTrait` is used, configure the Kartik Markdown module:

```php
'modules' => [
    'markdown' => [
        'class' => 'kartik\markdown\Module',
    ],
],
```

No Kartik Grid module is required by `yii2-traits` itself.

## Usage

### Model rules and labels

Trait rules and labels follow Yii2's instance-method contract. Merge trait-specific helpers from the model's `rules()` and `attributeLabels()` methods:

```php
class YourModel extends yii\db\ActiveRecord
{
    use \cinghie\traits\CreatedTrait;
    use \cinghie\traits\ModifiedTrait;

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

Trait methods can then be called like normal model methods:

```php
if ($model->isCurrentUserCreator()) {
    // ...
}
```

## Security and behavioral notes

### AttachmentTrait

Attachment deletion is constrained to the configured attachment directory. Generated attachment filenames use secure random values while the legacy `generateMd5FileName()` method name is preserved for backward compatibility. Configure FFmpeg/FFprobe paths explicitly when media thumbnail generation is used.

### CacheTrait

Cache mutation actions are administrative operations. They require POST requests, the configured cache-management authorization and CSRF validation. Applications should not disable CSRF validation on controllers exposing these actions.

### OrderingTrait

Ordering updates run transactionally. Move-to-last operations calculate the scoped maximum inside the transaction, and the supplied ActiveRecord class is checked for compatibility with the host model.

### ParentTrait

`getParent()` returns the direct parent. `getParents()` and `getAncestors()` represent the ancestor chain, nearest parent first. Validation prevents missing parents, self-parenting and hierarchy cycles.

## Traits reference

### AccessTrait

Access-state model and UI helpers.

- `int $access`
- `getAccessRules()` — validation rules contributed by the trait.
- `getAccessAttributeLabels()` — attribute labels contributed by the trait.
- `getAccessWidget($form)` — access form widget.
- `getAccessGridView()` — GridView configuration for access.
- `getAccessDetailView()` — DetailView configuration for access.

### AddressTrait

Address fields and geocoding helpers.

- `string $name`
- `string $latitude`
- `string $longitude`
- `string $street`
- `string $number`
- `string $postal_code`
- `string $city`
- `string $state`
- `string $country`
- `getAddressRules()` — validation rules contributed by the trait.
- `getAddressAttributeLabels()` — attribute labels contributed by the trait.
- `getLatLng($address, $key = null)` — resolve latitude and longitude from an address.

### AttachmentTrait

Attachment metadata, filesystem, widget and media helpers. Kartik widgets, getID3 and PHP-FFMpeg are installed as runtime dependencies.

- `string $extension`
- `string $filename`
- `string $mimetype`
- `getAttachmentRules()` — validation rules contributed by the trait.
- `getAttachmentAttributeLabels()` — attribute labels contributed by the trait.
- `getFileWidget($form, $attachType)` — file input widget.
- `getFilesWidget($attachType)` — multiple-file input widget.
- `getExtensionWidget($form)` — extension field widget.
- `getMimeTypeWidget($form)` — MIME type field widget.
- `getSizeWidget($form)` — size field widget.
- `getFileUrl()` — URL for the attached file.
- `deleteFile()` — safely delete the attached file inside the configured attachment directory.
- `getAttachmentType()` — derive the attachment type from the MIME type.
- `formatSize()` — format a byte size for display.
- `generateMd5FileName($filename, $extension)` — generate a secure random filename while preserving the legacy method name.
- `getAttachmentTypeIcon()` — icon for the attachment type.

### CacheTrait

Cache inspection and administrative invalidation actions.

- `actionCache()` — cache administration action.
- `actionFlushCache($id)` — flush a configured cache.
- `actionFlushCacheKey($id, $key)` — delete a cache key.
- `actionFlushCacheTag($id, $tag)` — invalidate a cache tag.
- `getCache($id)` — resolve a configured cache component.
- `findCaches(array $cachesNames = [])` — discover configured caches.
- `isCacheClass($className)` — check whether a class is a supported cache class.

### CreatedTrait

Creation timestamp/user audit helpers.

- `string|null $created`
- `int|null $created_by`
- `getCreatedRules()` — validation rules contributed by the trait.
- `getCreatedAttributeLabels()` — attribute labels contributed by the trait.
- `getCreatedBy()` — relation to the creator user.
- `getCreatedWidget($form)` — created-at form widget.
- `getCreatedDetailView()` — DetailView configuration for created-at.
- `getCreatedByWidget($form)` — creator form widget.
- `getCreatedByGridView()` — GridView configuration for creator.
- `getCreatedByDetailView()` — DetailView configuration for creator.
- `isCurrentUserCreator()` — whether the current authenticated user is the creator.
- `isUserCreator($user_id)` — whether the supplied user is the creator.

### EditorTrait

Editor selection and rendering facade. CKEditor, TinyMCE, Imperavi and Markdown integrations are installed automatically.

- `getEditorWidget($form, $field, $requestEditor = '', $value = '')` — render the configured editor.
- `getCKEditorWidget(...)` — CKEditor widget.
- `getImperaviWidget(...)` — Imperavi widget.
- `getMarkdownWidget(...)` — Markdown widget.
- `getNoEditorWidget(...)` — plain input without a rich editor.
- `getTinyMCEWidget(...)` — TinyMCE widget.

### GoogleTranslateTrait

Google Cloud Translation V3 integration.

- `getGoogleCloudTranslation(...)` — translate text using `TranslationServiceClient`.
- Requires `googleTranslateProjectId` in runtime configuration.
- Uses Google Application Default Credentials (ADC); the legacy API-key argument is retained only for compatibility.

### ImageTrait

Image metadata and UI helpers.

- `string $image`
- `string $image_caption`
- `string $image_credits`
- `getImageRules()` — validation rules contributed by the trait.
- `getImageAttributeLabels()` — attribute labels contributed by the trait.
- `getImageWidget()` — image form widget.
- `getImageCaptionWidget($form)` — caption widget.
- `getImageCreditsWidget($form)` — credits widget.
- `getImageGridView()` — GridView configuration for image.
- `getUploadMaxSize()` — configured upload limit.
- `getImagesAllowed()` — allowed image extensions/types.
- `getImagesAccept()` — allowed image types in input `accept` format.

### LanguageTrait

Language model and selection helpers.

- `string $language`
- `getLanguageRules()` — validation rules contributed by the trait.
- `getLanguageAttributeLabels()` — attribute labels contributed by the trait.
- `getLang()` — two-character language code.
- `getLangTag()` — language tag.
- `getLanguageWidget($form)` — language form widget.
- `getLanguagesSelect2()` — selectable languages.

### ModifiedTrait

Modification timestamp/user audit helpers.

- `string|null $modified`
- `int|null $modified_by`
- `getModifiedRules()` — validation rules contributed by the trait.
- `getModifiedAttributeLabels()` — attribute labels contributed by the trait.
- `getModifiedBy()` — relation to the modifying user.
- `getModifiedWidget($form)` — modified-at form widget.
- `getModifiedDetailView()` — DetailView configuration for modified-at.
- `getModifiedByWidget($form)` — modifier form widget.
- `getModifiedByGridView()` — GridView configuration for modifier.
- `getModifiedByDetailView()` — DetailView configuration for modifier.
- `isCurrentUserModifier()` — whether the current authenticated user is the modifier.
- `isUserModifier($user_id)` — whether the supplied user is the modifier.

### NameAliasTrait

Name and slug/alias helpers.

- `string $alias`
- `string $name`
- `getNameAliasRules()` — validation rules contributed by the trait.
- `getNameAliasAttributeLabels()` — attribute labels contributed by the trait.
- `generateAlias($name)` — generate a URL-safe alias.
- `setAlias($post, $field)` — set an alias from submitted data.
- `purgeAlias($string)` — normalize an alias string.
- `getNameWidget($form)` — name form widget.
- `getAliasWidget($form)` — alias form widget.

### OrderingTrait

Transactional ordering helpers.

- `int $ordering`
- `getOrderingRules()` — validation rules contributed by the trait.
- `getOrderingAttributeLabels()` — attribute labels contributed by the trait.
- `setOrdering($class, $fieldOrdering, $oldOrdering, $lastOrdering)` — update ordering transactionally.
- `setMinOrder()` — move/set minimum ordering.
- `setMaxOrdering($class, $condition)` — set maximum scoped ordering.
- `getLastOrdering($class, $condition)` — retrieve the maximum scoped ordering.
- `getOrderingWidget(...)` — ordering form widget.
- `getOrderingSelect2(...)` — selectable ordered items.

### ParentTrait

Parent relation, hierarchy validation and ancestor traversal.

- `int|null $parent_id`
- `getParentRules()` — validation rules including existence and hierarchy checks.
- `getParentAttributeLabels()` — attribute labels contributed by the trait.
- `getParent()` — direct parent relation.
- `getParents()` — ancestor chain, nearest first.
- `getAncestors()` — ancestor chain alias/helper.
- `getParentWidget($form, $items)` — parent form widget.
- `getParentGridView($field, $url, $hideItem)` — parent GridView configuration.

### PDFTrait

PDF integration backed by the required Kartik mPDF package.

### SeoTrait

SEO metadata and robots helpers.

- `string $robots`
- `string $author`
- `string $copyright`
- `string $metadesc`
- `string $metakey`
- `getSeoRules()` — validation rules contributed by the trait.
- `getSeoAttributeLabels()` — attribute labels contributed by the trait.
- `getRobotsWidget($form)` — robots form widget.
- `getAuthorWidget($form)` — author form widget.
- `getCopyrightWidget($form)` — copyright form widget.
- `getMetaDescriptionWidget($form)` — meta-description widget.
- `getMetaKeyWidget($form)` — meta-keywords widget.
- `getRobotsOptions()` — supported robots directives.

### SequentialTrait

Sequential code generation helper.

- `generateSequentialCode($number, $prefix, $sequence)` — generate a sequential code with the requested prefix/width.

### SocialTrait

Social integration helpers backed by the required Kartik Social package.

### StateTrait

Active/inactive state helpers and UI integration.

- `int $state`
- `getStateRules()` — validation rules contributed by the trait.
- `getStateAttributeLabels()` — attribute labels contributed by the trait.
- `active()` — set active state.
- `deactive()` — set inactive state.
- `getStateWidget($form)` — state form widget.
- `getStateGridView()` — GridView configuration for state.
- `getStateDetailView()` — DetailView configuration for state.
- `getStateSelect2()` — selectable states.

### TaggableTrait

Tag behavior helpers.

- `$tagNames`
- `getTaggableRules()` — validation rules contributed by the trait.
- `getTaggableAttributeLabels()` — attribute labels contributed by the trait.
- `getTagsDetailView()` — DetailView configuration for tags.

### TitleAliasTrait

Title and slug/alias helpers.

- `string $alias`
- `string $title`
- `getTitleAliasRules()` — validation rules contributed by the trait.
- `getTitleAliasAttributeLabels()` — attribute labels contributed by the trait.
- `generateAlias($name)` — generate a URL-safe alias.
- `setAlias($post, $field)` — set an alias from submitted data.
- `purgeAlias($string)` — normalize an alias string.
- `getTitleWidget($form)` — title form widget.
- `getAliasWidget($form)` — alias form widget.

### UserHelpersTrait

Helpers for the current user, profiles, roles and selectable users.

- `getUserByEmail($email)` — find a user by email.
- `getCurrentUser($field = '')` — current user or a selected field.
- `getCurrentUserProfile($field = '')` — current profile or a selected field.
- `getCurrentUserSelect2()` — current user as Select2 options.
- `getRolesSelect2()` — selectable roles including public access where applicable.
- `getUsersSelect2()` — selectable active/confirmed users.

### UserTrait

User relation and UI helpers.

- `int|null $user_id`
- `getUserRules()` — validation rules contributed by the trait.
- `getUserAttributeLabels()` — attribute labels contributed by the trait.
- `getUser()` — user relation.
- `getUserWidget($form)` — user form widget.
- `getUserGridView()` — GridView configuration for user.
- `getUserDetailView()` — DetailView configuration for user.

### VideoTrait

Video metadata and UI helpers.

- `string $video`
- `string $video_caption`
- `string $video_credits`
- `string $video_type`
- `getVideoRules()` — validation rules contributed by the trait.
- `getVideoAttributeLabels()` — attribute labels contributed by the trait.
- `getVideoTypeSelect2()` — selectable video types.
- `getVideoIDWidget($form)` — video ID widget.
- `getVideoTypeWidget($form)` — video type widget.
- `getVideoCaptionWidget($form)` — caption widget.
- `getVideoCreditsWidget($form)` — credits widget.

### ViewsHelperTrait

Common view and action-button helpers.

- `getCreateButton(...)`
- `getUpdateButton(...)`
- `getUpdateButtonJavascript(...)`
- `getDeleteButton(...)`
- `getDeleteButtonJavascript(...)`
- `getPreviewButton(...)`
- `getPreviewButtonJavascript(...)`
- `getActiveButton(...)`
- `getActiveButtonJavascript(...)`
- `getDeactiveButton(...)`
- `getDeactiveButtonJavascript(...)`
- `getResetButton(...)`
- `getSaveButton()`
- `getCancelButton(...)`
- `getExitButton(...)`
- `getSendButton()`
- `getSendButtonJavascript()`
- `getStandardButton(...)`
- `getEntryInformationsDetailView()`

## Compatibility notes

- PHP 7.4 and PHP 8.0 are no longer supported; the runtime baseline is PHP 8.1.
- Runtime integrations listed in `composer.json` are required and installed automatically.
- Google Translate uses the V3 SDK with ADC and a configured `googleTranslateProjectId`.
- Static trait `rules()` / `attributeLabels()` APIs are not supported; use `get*Rules()` / `get*AttributeLabels()` helpers.
- Some legacy dependency packages are abandoned upstream even though Composer currently reports no known security advisories for the resolved release dependency set.

## Testing

The project CI validates PHP 8.1 through PHP 8.5 with Composer validation, dependency installation and audit, PHP syntax checks, PHPUnit, standalone smoke tests and required-runtime dependency smoke tests.
