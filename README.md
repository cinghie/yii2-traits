Yii2 Traits
------------

Yii2 Traits is a library with most used Traits, to minimize code on development new Modules.
It contains a large number of features already implemented:

    - attributes
    - attributeLabels()
    - rules(()
    - messages
    - common functions
    - widgets

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require cinghie/yii2-traits "*"
```

or add

```
"cinghie/yii2-traits": "*"
```

## Configuration

Add in your configuration file the translations

```	
'components' => [ 

    // Internationalization
    'i18n' => [
        'translations' => [
            'traits' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vendor/cinghie/yii2-traits/messages',
            ],
        ],
    ],
                   	
],
'modules' => [ 
    
    // Module Kartik-v Grid
    'gridview' =>  [
        'class' => '\kartik\grid\Module',
    ],

    // Module Kartik-v Markdown Editor
    'markdown' => [
        'class' => 'kartik\markdown\Module',
    ],
    
],
```

## Usage Example

To include a Trait in your Model:

```	
class YourModel extends ActiveRecord 
{
    use \cinghie\traits\CreatedTrait;
}
```

Merge rules() and attributeLabels():

```	
public function rules()
{
    return array_merge(
        CreatedTrait::rules(), 
        [your_rules]
    );
}    

public function attributeLabels()
{
    return array_merge(
        CreatedTrait::attributeLabels(), 
        [your_attributeLabels]
    );
}   
```

All functions implemented in the Traits can be called like as any function of the model

```
if( $model->isCurrentUserCreator() ) { 
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

    - string $extension
    - string $filename
    - string $mimetype
    - function getFileWidget($form,$attachType): Generate File Ipunt Form Widget
    - function getFilesWidget($attachType,$attachURL): Generate Files Ipunt Form Widget
    - function getExtensionWidget($form): Generate Extension Form Widget
    - function getMimeTypeWidget($form): Generate MimeType Form Widget
    - function getSizeWidget($form): Generate Size Form Widget
    - function getFileUrl(): return file attached
    - function deleteFile(): delete file attached
    - function getAttachmentType(): Generate Attachment type from mimetype
    - function formatSize(): Format size in readable size
    - function generateMd5FileName($filename, $extension): Generate a MD5 filename by original filename
    - function getAttachmentTypeIcon(): Get Attachmente Type Image by Type

### CacheTrait

    - function actionCache()
    - function actionFlushCache($id)
    - function actionFlushCacheKey($id, $key)
    - function actionFlushCacheTag($id, $tag)
    - function getCache($id)
    - function findCaches(array $cachesNames = [])
    - function isCacheClass($className)

### CreatedTrait

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

    - function getEditorWidget($form, $field, $requestEditor = '', $value = ''): Generate Editor Widget
    - function getCKEditorWidget($form, $field, $value, $options, $preset): Get a CKEditor Editor Widget
    - function getImperaviWidget($form, $field, $value, $options, $plugins): Get a Imperavi Editor Widget
    - function getMarkdownWidget($form, $field, $value, $options): Get a Markdown Editor Widget
    - function getNoEditorWidget($form, $field, $value, $maxLength = false): Get a No-Editor Widget
    - function getTinyMCEWidget($form, $field, $value, $options): Get a TinyMCE Editor Widget

### ImageTrait

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

    - string $language
    - function getLang(): Get language code (2 chars)
    - function getLangTag(): Get language tag (5 chars)
    - function getLanguageWidget($form): Generate Language Form Widget
    - function getLanguagesSelect2(): Return an array with languages allowed

### ModifiedTrait

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

    - string $alias
    - string $name
    - function generateAlias($name): Generate URL alias by string
    - function setAlias($post,$field): Set alias from post
    - function purgeAlias($string): Purge alias by string
    - function getNameWidget($form): Generate Name Form Widget
    - function getAliasWidget($form): Generate Alias Form Widget
    
### ParentTrait      

    - int $parent_id
    - getParentWidget($form, $items): Generate Parent Form Widget
    - getParentGridView($field, $url, $hideItem): Generate Parent Grid View
 
### SeoTrait    

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
    
### StateTrait

    - int $state
    - function active(): Active model state (Set 1)
    - function deactive():  Inactive model state (Set 0)
    - function getStateWidget($form): Generate State Form Widget
    - function getStateGridView(): Generate GridView for State
    - function getStateDetailView(): Generate DetailView for State
    - function getStateSelect2(): Return an array with states
    
### TaggableTrait

    - int $tagNames
    - function getTagsDetailView(): Generate DetailView for Tags
    
### TitleAliasTrait

    - string $alias
    - string $title  
    - function generateAlias($name): Generate URL alias by string
    - function setAlias($post,$field): Set alias from post
    - function purgeAlias($string): Purge alias by string
    - function getTitleWidget($form): Generate Title Form Widget
    - function getAliasWidget($form): Generate Alias Form Widget

### UserHelperTrait

    - function getUserByEmail($email): Get the User by user email
    - function getCurrentUser($field = ''): Get current User or Current User field
    - function getCurrentUserProfile($field = ''):  Get current User Profile object or fied if on param
    - function getCurrentUserSelect2(): Return an array with current User
    - function getRolesSelect2(): Return an array with the User's Roles adding "Public" on first position
    - function getUsersSelect2(): Return array with all Users (not blocked or not unconfirmed)
    
### UserTrait

    - int $user_id
    - User user
    - function getUser(): Relation with User Model    
    - function getUserWidget($form): Generate User Form Widget
    - function getUserGridView(): Generate GridView for User
    - function getUserDetailView(): Generate DetailView for User
    
### VideoTrait

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

    - function getCreateButton(): Return action create button
    - function getUpdateButton($id = 0): Return action update button
    - function getUpdateButtonJavascript($w): Return javascript for action update button
    - function getDeleteButton($id = 0): Return action delete button
    - function getDeleteButtonJavascript($w): Return javascript for action delete button
    - function getPreviewButton(): Return action preview button
    - function getPreviewButtonJavascript($w): Return javascript for action preview button
    - function getActiveButton($id = 0): Return action active button
    - function getActiveButtonJavascript($w): Return javascript for action active button
    - function getDeactiveButton($id = 0): Return action deactive button
    - function getDeactiveButtonJavascript($w): Return javascript for action deactive button
    - function getResetButton(): Return action reset button
    - function getSaveButton(): Return action save button
    - function getCancelButton($icon = 'fa fa-times-circle text-red', $title = '', array $url = [ '' ] ): Return action cancel button
    - function getExitButton($icon = 'fa fa-sign-out text-blue', $title = '', array $url = [ 'index' ]): Return action exit button
    - function getSendButton(): Return action send button
    - function getSendButtonJavascript(): Return javascript for action deactive button
    - function getStandardButton($icon,$title,$url, array $aClass = [ 'class' => 'btn btn-mini' ], $divClass = 'pull-right text-center' ): Return standard button
    - function getEntryInformationsDetailView(): Generate DetailView for Entry Informations


