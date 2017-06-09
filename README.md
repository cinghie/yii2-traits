Yii2 Traits
------------

Yii2 Traits is a library with most used Traits

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
                   	
]	
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
    - function isCurrentUserCreator(): Check if current user is the created_by
    - function isUserCreator($user_id): Check if user_id params is the created_by
    - function getCreatedWidget($form,$model): Generate Created Form Widget
    - function getCreatedByWidget($form,$model): Generate CreatedBy Form Widget
    
### LanguageTrait

Add in your model: 

    - language: String language
    - function getLanguagesSelect2(): Return an array with languages allowed
    - function getLanguageWidget($form,$model): Generate Language Form Widget

### ModifiedTrait

Add in your model: 

    - modified: Datetime of modified Model
    - modified_by: Integer userid of modified Model 
    - function getModifiedBy(): Relation with User Model
    - function isCurrentUserModifier(): Check if current user is the modified_by
    - function isUserModifier($user_id): Check if user_id params is the modified_by
    - function getModifiedWidget($form,$model): Generate Modified Form Widget
    - function getModifiedByWidget($form,$model): Generate ModifiedBy Form Widget

### NameAliasTrait

Add in your model: 

    - alias: string alias
    - name: string name  
    - function generateAlias($name): Generate URL alias by name
    - getNameWidget($form,$model): Generate Name Form Widget
    - getAliasWidget($form,$model): Generate Alias Form Widget
    
### StateTrait

Add in your model: 

    - state: Integer for state active (1) o inactive (0)
    - function active(): Active model state (Set 1)
    - function inactive():  Inactive model state (Set 0)
    
### TitleAliasTrait

Add in your model: 

    - alias: string alias
    - title: string title  
    - function generateAlias($title): Generate URL alias by title
    - getTitleWidget($form,$model): Generate Title Form Widget
    - getAliasWidget($form,$model): Generate Alias Form Widget

### UserHelperTrait

Add in your model: 

    - function getRoles(): Return an array with the User's Roles 
    - function getUserIdByEmail($email): Get the user_id By user email 
    - function getUsersSelect2(): Return array with all Users (not blocked or not unconfirmed)    
    
### UserTrait

Add in your model: 

    - user_id: Integer userid of User Model
    - function getUser(): Relation with User Model   