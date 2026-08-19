Yii2 Traits
------------

![License](https://img.shields.io/packagist/l/cinghie/yii2-traits.svg)
![Latest Stable Version](https://img.shields.io/github/release/cinghie/yii2-traits.svg)
![Latest Release Date](https://img.shields.io/github/release-date/cinghie/yii2-traits.svg)
![Latest Commit](https://img.shields.io/github/last-commit/cinghie/yii2-traits.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/cinghie/yii2-traits.svg)](https://packagist.org/packages/cinghie/yii2-traits)

Yii2 Traits is a library with commonly used traits designed to reduce duplicated code across Yii2 modules.
It contains reusable implementations for:

    - attributes
    - attributeLabels()
    - rules()
    - messages
    - common functions
    - widgets

## Requirements

- PHP 8.1 or later
- Yii 2.0.55 or later

`cinghie/yii2-traits` is also a shared runtime foundation for other Cinghie modules. For this reason, the integrations used by its traits are required Composer dependencies and are installed automatically with the package.

### Required runtime dependencies

| Package | Used for |
| --- | --- |
| `2amigos/yii2-ckeditor-widget` | CKEditor support in `EditorTrait` |
| `2amigos/yii2-taggable-behavior` | `TaggableTrait` |
| `2amigos/yii2-tinymce-widget` | TinyMCE support in `EditorTrait` |
| `cinghie/yii2-user-extended` | User helper integrations |
| `cocur/slugify` | Slug generation in `NameAliasTrait` and `TitleAliasTrait` |
| `dektrium/yii2-user` | User relations in `CreatedTrait`, `ModifiedTrait` and `UserTrait` |
| `google/cloud-translate` | `GoogleTranslateTrait` |
| `james-heinrich/getid3` | Attachment and media metadata |
| `kartik-v/yii2-detail-view` | DetailView helpers |
| `kartik-v/yii2-helpers` | Kartik HTML helpers |
| `kartik-v/yii2-markdown` | Markdown editor support |
| `kartik-v/yii2-mpdf` | `PDFTrait` |
| `kartik-v/yii2-social` | `SocialTrait` |
| `kartik-v/yii2-widgets` | Form widgets such as Select2, DateTimePicker and FileInput |
| `php-ffmpeg/php-ffmpeg` | Video thumbnail generation |
| `vova07/yii2-imperavi-widget` | Imperavi support in `EditorTrait` |

These dependencies are part of the package runtime contract and must not be removed from an application that uses `cinghie/yii2-traits` or modules depending on it.

## Installation

The preferred way to install this extension is through [Composer](https://getcomposer.org/).

Either run

```
php composer.phar require cinghie/yii2-traits "*"
```

or add

```
"cinghie/yii2-traits": "*"
```

to the `require` section of your application's `composer.json` file.

Composer installs the complete runtime dependency set automatically, including the editor, media, user, PDF, social and Google Cloud Translate integrations required by the traits and by other Cinghie modules.

## Configuration

Add the translations to your configuration file:

```
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

If Markdown support from `EditorTrait` is used, add the Kartik Markdown module configuration required by that package:

```
'modules' => [
    'markdown' => [
        'class' => 'kartik\markdown\Module',
    ],
],
```

No Kartik Grid module is required by `yii2-traits` itself.

## Usage Example

To include a trait in your model:

```
class YourModel extends ActiveRecord
{
    use \cinghie\traits\CreatedTrait;
}
```

Merge trait rules and attribute labels in the Yii2 model instance methods:

```
public function rules()
{
    return array_merge(
        parent::rules(),
        $this->getCreatedRules(),
        [your_rules]
    );
}

public function attributeLabels()
{
    return array_merge(
        parent::attributeLabels(),
        $this->getCreatedAttributeLabels(),
        [your_attributeLabels]
    );
}
```

All functions implemented in the traits can be called like any other model method:

```
if ($model->isCurrentUserCreator()) {
    // your code
}
```

## Traits

### AccessTrait

    - int $access
    - getAccessWidget($form): Generate Access Form Widget
    - function getAccessGridView(): Generate GridView Access
    - function getAccessDetailView(): Generate DetailView Access

### AddressTrait

    - string $name
    - string $latitude
    - string $longitude
    - string $street
    - string $number
    - string $postal_code
    - string $city
    - string $state
    - string $country
    - function getLatLng($address, $key = null): Get latitude and longitude from Google Maps API

### AttachmentTrait

The required Kartik widgets, getID3 and PHP-FFMpeg integrations are installed automatically by Composer.

    - string $extension
    - string $filename
    - string $mimetype
    - function getFileWidget($form,$attachType): Generate File Ipunt Form Widget
    - function getFilesWidget($attachType): Generate Files Ipunt Form Widget
    - function getExtensionWidget($form): Generate Extension Form Widget
    - function getMimeTypeWidget($form): Generate MimeType Form Widget
    - function getSizeWidget($form): Generate Size Form Widget
    - function getFileUrl(): return file attached
    - function deleteFile(): delete file attached
    - function getAttachmentType(): Generate Attachment type from mimetype
    - function formatSize(): Format size in readable size
    - function generateMd5FileName($filename, $extension): Generate a random filename while preserving the legacy method name
    - function getAttachmentTypeIcon(): Get Attachment Type Image by Type

### CacheTrait

    - function actionCache()
    - function actionFlushCache($id)
    - function actionFlushCacheKey($id, $key)
    - function actionFlushCacheTag($id, $tag)
    - function getCache($id)
    - function findCaches(array $cachesNames = [])
    - function isCacheClass($className)

### CreatedTrait

User and UI dependencies are installed automatically by Composer.

    - string $created
    - int $created_by
    - User $createdBy
    - function getCreatedBy(): Relation with User Model
    - function getCreatedWidget($form): Generate Created Form Widget
    - function getCreatedDetailView(): Generate DetailView for Created
    - function getCreatedByWidget($form): Generate CreatedBy Form Widget
    - function getCreatedByGridView(): Generate GridView for CreatedBy
    - function getCreatedByDetailView(): Generate DetailView for CreatedBy
    - function isCurrentUserCreator(): Check if current user is the created_by
    - function isUserCreator($user_id): Check if user_id params is the created_by

### EditorTrait

CKEditor, TinyMCE, Imperavi and Markdown integrations are installed automatically by Composer.

    - function getEditorWidget($form, $field, $requestEditor = '', $value = ''): Generate Editor Widget
    - function getCKEditorWidget($form, $field, $value, $options, $preset): Get a CKEditor Editor Widget
    - function getImperaviWidget($form, $field, $value, $options, $plugins): Get a Imperavi Editor Widget
    - function getMarkdownWidget($form, $field, $value, $options): Get a Markdown Editor Widget
    - function getNoEditorWidget($form, $field, $value, $maxLength = false): Get a No-Editor Widget
    - function getTinyMCEWidget($form, $field, $value, $options): Get a TinyMCE Editor Widget

### ImageTrait

Kartik UI dependencies are installed automatically by Composer.

    - string $image
    - string $image_caption
    - string $image_credits
    - function getImageWidget(): Generate Image Form Widget
    - function getImageCaptionWidget($form): Generate Image Caption Form Widget
    - function getImageCreditsWidget($form): Generate Image Credits Form Widget
    - function getImageGridView(): Generate GridView for Image
    - function getUploadMaxSize(): Get Upload Max Size
    - function getImagesAllowed(): Get Allowed images
    - function getImagesAccept(): Get Allowed images in Accept Format

### LanguageTrait

Kartik UI dependencies are installed automatically by Composer.

    - string $language
    - function getLang(): Get language code (2 chars)
    - function getLangTag(): Get language tag (5 chars)
    - function getLanguageWidget($form): Generate Language Form Widget
    - function getLanguagesSelect2(): Return an array with languages allowed

### ModifiedTrait

User and UI dependencies are installed automatically by Composer.

    - string $modified
    - int $modified_by
    - User $modifiedBy
    - function getModifiedBy(): Relation with User Model
    - function getModifiedWidget($form): Generate Modified Form Widget
    - function getModifiedDetailView(): Generate DetailView for Modified
    - function getModifiedByWidget($form): Generate ModifiedBy Form Widget
    - function getModifiedByGridView(): Generate GridView for ModifiedBy
    - function getModifiedByDetailView(): Generate DetailView for ModifiedBy
    - function isCurrentUserModifier(): Check if current user is the modified_by
    - function isUserModifier($user_id): Check if user_id params is the modified_by

### NameAliasTrait

Slugify and Kartik UI dependencies are installed automatically by Composer.

    - string $alias
    - string $name
    - function generateAlias($name): Generate URL alias by string
    - function setAlias($post,$field): Set alias from post
    - function purgeAlias($string): Purge alias by string
    - function getNameWidget($form): Generate Name Form Widget
    - function getAliasWidget($form): Generate Alias Form Widget

### OrderingTrait

Kartik UI dependencies are installed automatically by Composer.

    - integer $ordering
    - function setOrdering($class,$fieldOrdering,$oldOrdering,$lastOrdering): Set Model Ordering on Class
    - function setMinOrder(): Set Min Ordering
    - function setMaxOrdering($class,$condition): Set Max Ordering
    - function getLastOrdering($class,$condition): Get Max ordering in field
    - function getOrderingWidget($form, $class, $orderingField, $selectField, $condition): Generate Ordering Form Widget
    - function getOrderingSelect2($class, $orderingField = '', array $selectField = [], array $condition = []): Return array with all Items by $cat_id

### ParentTrait

Kartik UI dependencies are installed automatically by Composer.

    - int $parent_id
    - getParentWidget($form, $items): Generate Parent Form Widget
    - getParentGridView($field, $url, $hideItem): Generate Parent Grid View

### SeoTrait

Kartik UI dependencies are installed automatically by Composer.

    - string $robots
    - string $author
    - string $copyright
    - string $metadesc
    - string $metakey
    - function getRobotsWidget($form): Generate Robots Form Widget
    - function getAuthorWidget($form): Generate Author Form Widget
    - function getCopyrightWidget($form): Generate Copyright Form Widget
    - function getMetaDescriptionWidget($form): Generate Meta Description Form Widget
    - function getMetaKeyWidget($form): Generate Meta Key Form Widget
    - function getRobotsOptions(): Get Robots Options

### SequentialTrait

    - string generateSequentialCode($number, $prefix, $sequence): Generate Sequential Code

### StateTrait

Kartik widget and DetailView dependencies are installed automatically by Composer.

    - int $state
    - function active(): Active model state (Set 1)
    - function deactive(): Inactive model state (Set 0)
    - function getStateWidget($form): Generate State Form Widget
    - function getStateGridView(): Generate GridView for State
    - function getStateDetailView(): Generate DetailView for State
    - function getStateSelect2(): Return an array with states

### TaggableTrait

The taggable behavior dependency is installed automatically by Composer.

    - int $tagNames
    - function getTagsDetailView(): Generate DetailView for Tags

### TitleAliasTrait

Slugify and Kartik UI dependencies are installed automatically by Composer.

    - string $alias
    - string $title
    - function generateAlias($name): Generate URL alias by string
    - function setAlias($post,$field): Set alias from post
    - function purgeAlias($string): Purge alias by string
    - function getTitleWidget($form): Generate Title Form Widget
    - function getAliasWidget($form): Generate Alias Form Widget

### UserHelperTrait

`cinghie/yii2-user-extended` is installed automatically by Composer.

    - function getUserByEmail($email): Get the User by user email
    - function getCurrentUser($field = ''): Get current User or Current User field
    - function getCurrentUserProfile($field = ''): Get current User Profile object or field if on param
    - function getCurrentUserSelect2(): Return an array with current User
    - function getRolesSelect2(): Return an array with the User's Roles adding "Public" on first position
    - function getUsersSelect2(): Return array with all Users (not blocked or not unconfirmed)

### UserTrait

User and UI dependencies are installed automatically by Composer.

    - int $user_id
    - User user
    - function getUser(): Relation with User Model
    - function getUserWidget($form): Generate User Form Widget
    - function getUserGridView(): Generate GridView for User
    - function getUserDetailView(): Generate DetailView for User

### VideoTrait

Kartik UI dependencies are installed automatically by Composer.

    - string $video
    - string $video_caption
    - string $video_credits
    - string $video_type
    - function getVideoTypeSelect2(): Return array for Video Type
    - function getVideoIDWidget($form): Generate Video ID Form Widget
    - function getVideoTypeWidget($form): Generate Video Type Form Widget
    - function getVideoCaptionWidget($form): Generate Video Caption Form Widget
    - function getVideoCreditsWidget($form): Generate Video Credits Form Widget

### ViewsHelperTrait

Kartik helper and DetailView dependencies are installed automatically by Composer.

    - function getCreateButton(array $url = ['create']): Return action create button
    - function getUpdateButton($id = 0): Return action update button
    - function getUpdateButtonJavascript($w): Return javascript for action update button
    - function getDeleteButton($id = 0): Return action delete button
    - function getDeleteButtonJavascript($w): Return javascript for action delete button
    - function getPreviewButton(array $url = [ '#' ]): Return action preview button
    - function getPreviewButtonJavascript($w): Return javascript for action preview button
    - function getActiveButton($id = 0): Return action active button
    - function getActiveButtonJavascript($w): Return javascript for action active button
    - function getDeactiveButton($id = 0): Return action deactive button
    - function getDeactiveButtonJavascript($w): Return javascript for action deactive button
    - function getResetButton(array $url = ['index']): Return action reset button
    - function getSaveButton(): Return action save button
    - function getCancelButton($icon = 'fa fa-times-circle text-red', $title = '', array $url = [ '' ] ): Return action cancel button
    - function getExitButton($icon = 'fa fa-sign-out text-blue', $title = '', array $url = [ 'index' ]): Return action exit button
    - function getSendButton(): Return action send button
    - function getSendButtonJavascript(): Return javascript for action deactive button
    - function getStandardButton($icon,$title,$url, array $aClass = [ 'class' => 'btn btn-mini' ], $divClass = 'pull-right text-center' ): Return standard button
    - function getEntryInformationsDetailView(): Generate DetailView for Entry Informations
