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

Add in your model: 

    - access: string of Auth Item
    - getAccessWidget($form): Generate Access Form Widget
    - function getAccessGridView(): Generate GridView Access
    - function getAccessDetailView(): Generate DetailView Access

### AttachmentTrait

Add in your model: 

    - extension: string extension of Attachment
    - filename: string filename of Attachment
    - mimetype: string mimetype of Attachment
    - function generateMd5FileName($filename, $extension): Generate a MD5 filename by original filename

### CacheTrait

Add in your model: 

    - function actionCache()
    - function actionFlushCache($id)
    - function actionFlushCacheKey($id, $key)
    - function actionFlushCacheTag($id, $tag)
    - function getCache($id)
    - function findCaches(array $cachesNames = [])
    - function isCacheClass($className)

### CreatedTrait

Add in your model: 

    - created: Datetime of created Model
    - created_by: Integer userid of created Model 
    - function getCreatedBy(): Relation with User Model
    - function getCreatedWidget($form): Generate Created Form Widget
    - function getCreatedDetailView(): Generate DetailView for Created
    - function getCreatedByWidget($form): Generate CreatedBy Form Widget
    - function getCreatedByGridView(): Generate GridView for CreatedBy
    - function getCreatedByDetailView(): Generate DetailView for CreatedBy
    - function isCurrentUserCreator(): Check if current user is the created_by
    - function isUserCreator($user_id): Check if user_id params is the created_by    
  
### EditorTrait

Add in your model: 

    - function getEditorWidget($form,$field,$requestEditor = ""): Generate Editor Widget
    - function getCKEditorWidget($form,$field): Get a CKEditor Editor Widget
    - function getTinyMCEWidget($form,$field): Get a TinyMCE Editor Widget
    - function getImperaviWidget($form,$field): Get a Imperavi Editor Widget
    - function getNoEditorWidget($form,$field): Get a No-Editor Widget
    
### LanguageTrait

Add in your model: 

    - language: String language
    - function getLanguageWidget($form): Generate Language Form Widget
    - function getLanguagesSelect2(): Return an array with languages allowed

### ModifiedTrait

Add in your model: 

    - modified: Datetime of modified Model
    - modified_by: Integer userid of modified Model 
    - function getModifiedBy(): Relation with User Model
    - function getModifiedWidget($form): Generate Modified Form Widget
    - function getModifiedDetailView(): Generate DetailView for Modified
    - function getModifiedByWidget($form): Generate ModifiedBy Form Widget
    - function getModifiedByGridView(): Generate GridView for ModifiedBy
    - function isCurrentUserModifier(): Check if current user is the modified_by
    - function isUserModifier($user_id): Check if user_id params is the modified_by

### NameAliasTrait

Add in your model: 

    - alias: string alias
    - name: string name  
    - function getAliasWidget($form): Generate Alias Form Widget
    - function getNameWidget($form): Generate Name Form Widget
    - function generateAlias($name): Generate URL alias by name
    
### StateTrait

Add in your model: 

    - state: Integer for state active (1) o inactive (0)
    - function active(): Active model state (Set 1)
    - function inactive():  Inactive model state (Set 0)
    - function getStateWidget($form): Generate State Form Widget
    - function getStateGridView(): Generate GridView for State
    - function getStateDetailView(): Generate DetailView for State
    - function getStateSelect2(): Return an array with states
    
### TitleAliasTrait

Add in your model: 

    - alias: string alias
    - title: string title  
    - getAliasWidget($form): Generate Alias Form Widget
    - getTitleWidget($form): Generate Title Form Widget
    - function generateAlias($title): Generate URL alias by title

### UserHelperTrait

Add in your model: 

    - function getUserByEmail($email): Get the User by user email
    - function getCurrentUserSelect2(): Return an array with current User
    - function getRolesSelect2(): Return an array with the User's Roles adding "Public" on first position
    - function getUsersSelect2(): Return array with all Users (not blocked or not unconfirmed)    
    
### UserTrait

Add in your model: 

    - user_id: Integer userid of User Model
    - function getUser(): Relation with User Model   
    - function getUserWidget($form): Generate User Form Widget
    - function getUserGridView(): Generate GridView for User
    - function getUserDetailView(): Generate DetailView for User
    
### ViewsHelperTrait

Add in your model: 

    - function getEntryInformationsDetailView(): Generate DetailView for Entry Informations
